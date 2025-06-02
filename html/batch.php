<?php
// Start session
session_start();

// Include database connection
require 'db.php';

// Validate if the logged-in user is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not an admin
    exit;
}

// Handle form submissions (Add Batch)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $batchNo = $_POST['batch_no'] ?? null;
    $session = $_POST['session'] ?? null;
    $batchId = $_POST['id'] ?? null;

    try {
        if ($action === 'add' && $batchNo && $session) {
            $query = "INSERT INTO batch (batch_no, session) VALUES (:batch_no, :session)";
            $stmt = $conn->prepare($query);
            $stmt->execute(['batch_no' => $batchNo, 'session' => $session]);
        } elseif ($action === 'delete' && $batchId) {
            $query = "DELETE FROM batch WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->execute(['id' => $batchId]);
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
    }
}

// Fetch batches
$batches = [];
try {
    $stmt = $conn->query("SELECT id, batch_no, session FROM batch");
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Management - NSTU QuickBill</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Poppins', sans-serif;
        }

        .navbar-custom {
            background: linear-gradient(to right, #ff7e5f, #feb47b);
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: white;
            font-weight: bold;
        }

        .navbar-custom .nav-link:hover {
            color: #f8d210;
        }

        .container {
            margin-top: 50px;
        }

        .footer {
            background-color: #333;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <a class="navbar-brand" href="#">NSTU QuickBill</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center mb-5">Manage Batchs</h1>

        <!-- Add Batch Form -->
        <div class="card mb-4">
            <div class="card-header">Add Batch</div>
            <div class="card-body">
                <form method="POST" onsubmit="return confirmAddBatch();">
                    <input type="hidden" name="action" value="add">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" name="batch_no" class="form-control" placeholder="Batch No" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="session" class="form-control" placeholder="Session" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Batch</button>
                </form>
            </div>
        </div>

        <!-- Batch List -->
        <h2>Existing Batches</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Batch No</th>
                    <th>Session</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($batches as $batch) : ?>
                    <tr>
                        <td><?= htmlspecialchars($batch['batch_no']) ?></td>
                        <td><?= htmlspecialchars($batch['session']) ?></td>
                        <td>
                            <!-- Delete Form -->
                            <form method="POST" class="d-inline" onsubmit="return confirmDeleteBatch();">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $batch['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5">
        <p>&copy; 2024 NSTU QuickBill. All rights reserved.</p>
    </footer>

    <script>
        // Confirmation for Add Batch
        function confirmAddBatch() {
            return confirm("Are you sure you want to add this batch?");
        }

        // Confirmation for Delete Batch
        function confirmDeleteBatch() {
            return confirm("Are you sure you want to delete this batch?");
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
