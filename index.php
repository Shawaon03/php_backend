<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

// Load Environment Variables
$host = getenv("DB_HOST");
$db = getenv("DB_NAME");
$user = getenv("DB_USER");
$pass = getenv("DB_PASS");
$port = getenv("DB_PORT");

// Database Connection
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed",
        "error" => $conn->connect_error
    ]);
    exit;
}

// Read JSON Body Safely
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Validate JSON
if (!$data || !isset($data["name"]) || !isset($data["email"]) || !isset($data["phone"]) || !isset($data["message"])) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid or incomplete data",
        "received" => $rawData
    ]);
    exit;
}

// Assign Data
$name = $data["name"];
$email = $data["email"];
$phone = $data["phone"];
$message = $data["message"];

// Insert Query
$sql = "INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $phone, $message);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Message saved!"]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to save",
        "error" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
