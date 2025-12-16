<?php
header("Content-Type: application/json");
require "db.php";
require "vendor/autoload.php";   // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$name    = $_POST["name"] ?? '';
$email   = $_POST["email"] ?? '';
$phone   = $_POST["phone"] ?? '';
$message = $_POST["message"] ?? '';

if (!$name || !$email || !$message) {
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
    exit;
}

// Database insert
$stmt = $conn->prepare(
    "INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $name, $email, $phone, $message);

if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Database error"]);
    exit;
}

// SMTP mail using PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = getenv("SMTP_HOST");
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv("SMTP_USER");
    $mail->Password   = getenv("SMTP_PASS");
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = getenv("SMTP_PORT");

    $mail->setFrom(getenv("MAIL_FROM"), "Portfolio Contact");
    $mail->addAddress(getenv("MAIL_TO"));
    $mail->addReplyTo($email, $name);

    $mail->isHTML(false);
    $mail->Subject = "New Contact Message - Portfolio";

    $mail->Body =
        "Name: $name\n" .
        "Email: $email\n" .
        "Phone: $phone\n\n" .
        "Message:\n$message\n";

    $mail->send();

    echo json_encode(["status" => "success"]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Mail failed"]);
}
