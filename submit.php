<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// Handle preflight (CORS)
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require "db.php";  // Same folder, so path is correct

// Read JSON body
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// If JSON not found, fallback to form fields
if (!$data) {
    $data = $_POST;
}

$name    = trim($data["name"] ?? "");
$email   = trim($data["email"] ?? "");
$phone   = trim($data["phone"] ?? "");
$message = trim($data["message"] ?? "");

if ($name === "" || $email === "" || $message === "") {
    echo json_encode(["ok" => false, "error" => "Missing required fields"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["ok" => false, "error" => "Invalid email"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $message);

if ($stmt->execute()) {
    echo json_encode(["ok" => true, "message" => "Saved"]);
} else {
    echo json_encode(["ok" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
