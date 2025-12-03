<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

// Load Environment Variables
$host = getenv("DB_HOST");
$db   = getenv("DB_NAME");
$user = getenv("DB_USER");
$pass = getenv("DB_PASS");
$port = getenv("DB_PORT");

// Database Connection
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed!",
        "error" => $conn->connect_error
    ]);
    exit;
}

// Read RAW JSON Body
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// If JSON is invalid
if (!$data || !is_array($data)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid or empty JSON received!"
    ]);
    exit;
}

// Safe Data Extraction
$name    = $data["name"] ?? "";
$email   = $data["email"] ?? "";
$phone   = $data["phone"] ?? "";
$message = $data["message"] ?? "";

// Required Field Validation
if ($name === "" || $email === "" || $phone === "" || $message === "") {
    echo json_encode([
        "status" => "error",
        "message" => "All fields are required!"
    ]);
    exit;
}

// Insert Query
$sql = "INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "SQL Error",
        "error" => $conn->error
    ]);
    exit;
}

$stmt->bind_param("ssss", $name, $email, $phone, $message);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Message saved successfully!"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to save message!",
        "error" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
