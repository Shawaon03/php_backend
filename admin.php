<?php
require "db.php";

// Simple password protection
$admin_password = "shawaon123"; // Change this

// If password form submitted
if (isset($_POST["password"])) {
    if ($_POST["password"] === $admin_password) {
        setcookie("admin_login", "1", time() + 3600);
        header("Location: admin.php");
        exit;
    } else {
        $error = "Wrong password!";
    }
}

// If not logged in
if (!isset($_COOKIE["admin_login"])) {
    echo '<form method="POST" style="max-width:300px;margin:100px auto;font-family:sans-serif;">
            <h2>Admin Login</h2>
            <input type="password" name="password" placeholder="Enter Password" style="width:100%;padding:10px;margin:10px 0;">
            <button style="padding:10px 20px;">Login</button>
            <p style="color:red;">'.($error ?? "").'</p>
          </form>';
    exit;
}

// Fetch messages
$result = $conn->query("SELECT * FROM contact_messages ORDER BY id DESC");

echo "<h1 style='font-family:sans-serif;text-align:center;'>Contact Messages</h1>";
echo "<table border='1' cellpadding='10' style='width:90%;margin:auto;font-family:sans-serif;border-collapse:collapse;'>
        <tr style='background:#222;color:#fff;'>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Message</th>
            <th>Date</th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['phone']}</td>
            <td>{$row['message']}</td>
            <td>{$row['created_at']}</td>
          </tr>";
}

echo "</table>";
?>
