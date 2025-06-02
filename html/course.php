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

// Handle form submissions (Add, Delete, Update Course)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $name = $_POST['name'] ?? null;
    $code = $_POST['code'] ?? null;
    $year = $_POST['year'] ?? null;
    $term = $_POST['term'] ?? null;
    $courseId = $_POST['course_id'] ?? null;

    try {
        if ($action === 'add' && $name && $code && $year && $term) {
            $query = "INSERT INTO course (name, code, year, term) VALUES (:name, :code, :year, :term)";
            $stmt = $conn->prepare($query);
            $stmt->execute(['name' => $name, 'code' => $code, 'year' => $year, 'term' => $term]);
        } elseif ($action === 'delete' && $courseId) {
            $query = "DELETE FROM course WHERE course_id = :course_id";
            $stmt = $conn->prepare($query);
            $stmt->execute(['course_id' => $courseId]);
        } elseif ($action === 'update' && $name && $code && $year && $term && $courseId) {
            $query = "UPDATE course SET name = :name, code = :code, year = :year, term = :term WHERE course_id = :course_id";
            $stmt = $conn->prepare($query);
            $stmt->execute(['name' => $name, 'code' => $code, 'year' => $year, 'term' => $term, 'course_id' => $courseId]);
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
    }
}

// Fetch courses
$courses = [];
try {
    $stmt = $conn->query("SELECT course_id, name, code, year, term FROM course");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management - NSTU QuickBill</title>
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

        .footer {
            background-color: #333;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <a class="navbar-brand" href="#">NSTU QuickBill</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Manage Courses</h1>

        <!-- Add Course Form -->
        <div class="card mb-4">
            <div class="card-header">Add Course</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="row mb-3">
                        <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="Course Name" required></div>
                        <div class="col-md-3"><input type="text" name="code" class="form-control" placeholder="Course Code" required></div>
                        <div class="col-md-3">
                            <select name="year" class="form-control" required>
                                <option value="" disabled selected>Year</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="term" class="form-control" required>
                                <option value="" disabled selected>Term</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Course</button>
                </form>
            </div>
        </div>

        <!-- Course List -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Course Code</th>
                    <th>Year</th>
                    <th>Term</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <!-- Update Form -->
                        <form method="POST" onsubmit="return confirmUpdate();">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                            <td>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($course['name']) ?>" required>
                            </td>
                            <td>
                                <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($course['code']) ?>" required>
                            </td>
                            <td>
                                <input type="number" name="year" class="form-control" value="<?= htmlspecialchars($course['year']) ?>" required>
                            </td>
                            <td>
                                <input type="number" name="term" class="form-control" value="<?= htmlspecialchars($course['term']) ?>" required>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-success btn-sm">Update</button>
                            </td>
                        </form>
                        <!-- Delete Form -->
                        <td>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

    <footer class="footer mt-5">
        <p>&copy; 2024 NSTU QuickBill. All rights reserved.</p>
    </footer>

    <script>
        // Enable Edit Mode for a Course Row
        function enableEdit(courseId) {
            const fields = ['name', 'code', 'year', 'term'];

            fields.forEach(field => {
                const span = document.getElementById(`${field}-${courseId}`);
                const value = span.textContent;
                span.innerHTML = `<input type="text" id="${field}-input-${courseId}" value="${value}" class="form-control form-control-sm">`;
            });

            // Show Save Button
            document.getElementById(`save-${courseId}`).classList.remove('d-none');
        }

        // Save Changes and Update Database (AJAX Call)
        function saveChanges(courseId) {
            const name = document.getElementById(`name-input-${courseId}`).value;
            const code = document.getElementById(`code-input-${courseId}`).value;
            const year = document.getElementById(`year-input-${courseId}`).value;
            const term = document.getElementById(`term-input-${courseId}`).value;

            // Send Data Using Fetch API
            fetch('course_update.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=update&course_id=${courseId}&name=${name}&code=${code}&year=${year}&term=${term}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Course updated successfully!');
                        location.reload(); // Reload to reflect changes
                    } else {
                        alert('Error updating course.');
                    }
                });
        }

        // Delete Course with Confirmation
        function deleteCourse(courseId) {
            if (confirm('Are you sure you want to delete this course?')) {
                fetch('course_update.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `action=delete&course_id=${courseId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Course deleted successfully!');
                            location.reload();
                        } else {
                            alert('Error deleting course.');
                        }
                    });
            }
        }

        function confirmUpdate() {
            return confirm('Are you sure you want to update this course?');
        }
    </script>

</body>

</html>