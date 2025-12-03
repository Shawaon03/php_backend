<?php
// ==========================
// DATABASE CONNECTION
// ==========================
$host = getenv("DB_HOST");
$db   = getenv("DB_NAME");
$user = getenv("DB_USER");
$pass = getenv("DB_PASS");
$port = getenv("DB_PORT");

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

// ==========================
// DELETE HANDLER
// ==========================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM contacts WHERE id = $id");
    header("Location: admin.php");
    exit();
}

// ==========================
// PAGINATION SETUP
// ==========================
$limit = 10;  
$page  = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;

$total_rows = $conn->query("SELECT COUNT(*) AS total FROM contacts")->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch contacts
$result = $conn->query("SELECT * FROM contacts ORDER BY id DESC LIMIT $start, $limit");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Messages</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-center">📩 Contact Messages</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Message</th>
                <th>Time</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure to delete?')">
                       Delete
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- PAGINATION -->
    <nav>
      <ul class="pagination justify-content-center">

        <?php if ($page > 1): ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?= $page-1 ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?= $page+1 ?>">Next</a>
            </li>
        <?php endif; ?>

      </ul>
    </nav>
</div>

</body>
</html>
