<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

// Load Environment Variables
$host = getenv("DB_HOST");
$db   = getenv("DB_NAME");
$user = getenv("DB_USER");
$pass = getenv("DB_PASS");
$port = getenv("DB_PORT");

// Database Connection
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die(json_encode([
        "status"  => "error",
        "message" => "Database connection failed",
        "error"   => $conn->connect_error
    ]));
}

// Read JSON Data
$input = file_get_contents("php://input");

// ❗ যদি POST ফাঁকা হয় — তাহলে ব্ল্যাঙ্ক ইনসার্ট বন্ধ করি
if (!$input) {
    echo json_encode(["status" => "error", "message" => "No POST data received"]);
    exit;
}

$data = json_decode($input, true);

// ❗ Invalid JSON Protect
if (!is_array($data)) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON"]);
    exit;
}

// Validate required fields
$name    = $data["name"]    ?? null;
$email   = $data["email"]   ?? null;
$phone   = $data["phone"]   ?? null;
$message = $data["message"] ?? null;

if (!$name || !$email || !$phone || !$message) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// Insert Query
$sql  = "INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $phone, $message);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Message saved!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to save", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
