<?php<?php
require "db.php";

echo "Database Connected Successfully!";
?>

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$name    = $data["name"] ?? "";
$email   = $data["email"] ?? "";
$phone   = $data["phone"] ?? "";
$message = $data["message"] ?? "";

if (!$email || !$message) {
    echo json_encode(["error" => "Email & message required"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $message);
$stmt->execute();

$id = $stmt->insert_id;

$to = getenv("NOTIFY_EMAIL");
$subject = "New Contact Message - $email";
$body = "Name: $name\nEmail: $email\nPhone: $phone\n\n$message";
mail($to, $subject, $body);

echo json_encode(["ok" => true, "id": $id]);
?>
