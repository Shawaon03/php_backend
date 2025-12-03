<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET");
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
    echo json_encode(["status"=>"error", "message"=>"DB connect fail", "error"=>$conn->connect_error]);
    exit;
}

// ------- READ JSON BODY ---------
$raw = file_get_contents("php://input");

if (!$raw || strlen($raw) < 5) {
    echo json_encode(["status"=>"error", "message"=>"No POST JSON received"]);
    exit;
}

$data = json_decode($raw, true);

if (!$data) {
    echo json_encode(["status"=>"error", "message"=>"Invalid JSON"]);
    exit;
}

// Extract fields
$name    = $data["name"] ?? "";
$email   = $data["email"] ?? "";
$phone   = $data["phone"] ?? "";
$message = $data["message"] ?? "";

// Prevent empty insert
if ($name == "" || $email == "" || $phone == "" || $message == "") {
    echo json_encode(["status"=>"error", "message"=>"Missing field"]);
    exit;
}

// Prepare and insert
$stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $message);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success", "message"=>"Message saved"]);
} else {
    echo json_encode(["status"=>"error", "message"=>"DB insert error", "error"=>$stmt->error]);
}

$stmt->close();
$conn->close();
?>
