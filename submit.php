<?php
header("Content-Type: application/json");
require "db.php";

$name    = $_POST["name"] ?? '';
$email   = $_POST["email"] ?? '';
$phone   = $_POST["phone"] ?? '';
$message = $_POST["message"] ?? '';

if (!$name || !$email || !$message) {
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
    exit;
}

// =====================
// DATABASE INSERT
// =====================
$stmt = $conn->prepare(
    "INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $name, $email, $phone, $message);

if ($stmt->execute()) {

    // =====================
    // EMAIL SEND
    // =====================
    $to = "shawaonahmed@gmail.com";  
    $subject = "New Contact Message - Portfolio";

    $body = "You have received a new message:\n\n"
          . "Name: $name\n"
          . "Email: $email\n"
          . "Phone: $phone\n\n"
          . "Message:\n$message\n";

    $headers = "From: noreply@yourdomain.com\r\n";
    $headers .= "Reply-To: $email\r\n";

    mail($to, $subject, $body, $headers);

    echo json_encode(["status" => "success"]);

} else {
    echo json_encode([
        "status" => "error",
        "message" => $stmt->error
    ]);
}
?>
