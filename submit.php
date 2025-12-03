
<?php
header("Content-Type: application/json");
require "db.php";

$name = $_POST["name"] ?? '';
$email = $_POST["email"] ?? '';
$phone = $_POST["phone"] ?? '';
$message = $_POST["message"] ?? '';

if (!$name || !$email || !$message) {
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $message);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}
?>
