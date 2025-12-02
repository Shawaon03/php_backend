<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Load DB credentials (from Render Environment Variables)
$host = getenv("DB_HOST");
$db   = getenv("DB_NAME");
$user = getenv("DB_USER");
$pass = getenv("DB_PASS");
$port = getenv("DB_PORT");

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed",
        "error" => $conn->connect_error
    ]);
    exit;
}

// Read POST data
$data = json_decode(file_get_contents("php://input"), true);

$name    = $data["name"] ?? "";
$email   = $data["email"] ?? "";
$phone   = $data["phone"] ?? "";
$message = $data["message"] ?? "";

// Validate
if (!$name || !$email || !$phone || !$message) {
    echo json_encode(["status" => "error", "message" => "All fields required"]);
    exit;
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $message);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Message saved successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database insert failed"]);
}

$stmt->close();
$conn->close();
?>
