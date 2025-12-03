<?php
require "db.php";

// Optional simple password protect
$admin_password = "12345";  // <-- চাইলে বদলায় নিতে পারবেন

if (!isset($_GET["pass"]) || $_GET["pass"] !== $admin_password) {
    die("<h2>Access Denied</h2> <p>You must enter correct admin password.</p>");
}

// Fetch messages
$result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Panel - Messages</title>
<style>
body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background: #f5f6fa;
}
h2 {
    margin-bottom: 20px;
}
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
table th, table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}
table th {
    background: #2C3E50;
    color: white;
}
tr:hover {
    background: #f1f1f1;
}
</style>
</head>
<body>

<h2>📩 All Contact Messages</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Message</th>
        <th>Time</th>
    </tr>

    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row["id"] ?></td>
        <td><?= htmlspecialchars($row["name"]) ?></td>
        <td><?= htmlspecialchars($row["email"]) ?></td>
        <td><?= htmlspecialchars($row["phone"]) ?></td>
        <td><?= nl2br(htmlspecialchars($row["message"])) ?></td>
        <td><?= $row["created_at"] ?></td>
    </tr>
    <?php endwhile; ?>

</table>

</body>
</html>
