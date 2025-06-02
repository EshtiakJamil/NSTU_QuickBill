<?php
// Start session and check login
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not an admin
    exit;
}

// Database connection
include_once 'db.php';

// Handle Add Teacher Request
if (isset($_POST['add_teacher'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    try {
        $stmt = $conn->prepare("INSERT INTO teacher (name, email) VALUES (:name, :email)");
        $stmt->execute([':name' => $name, ':email' => $email]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false]);
    }
    exit;
}

// Handle Delete Request
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM teacher WHERE teacher_id = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false]);
    }
    exit;
}

// Handle Update Request
if (isset($_POST['update_teacher'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    try {
        $stmt = $conn->prepare("UPDATE teacher SET name = :name, email = :email WHERE teacher_id = :id");
        $stmt->execute([':name' => $name, ':email' => $email, ':id' => $id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false]);
    }
    exit;
}

// Fetch Teachers
$teachers = $conn->query("SELECT teacher_id, name, email FROM teacher")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Teacher Management</h2>

        <!-- Add Teacher Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="add-teacher-form">
                    <div class="form-row">
                        <div class="col-md-5">
                            <input type="text" name="name" class="form-control" placeholder="Teacher Name" required>
                        </div>
                        <div class="col-md-5">
                            <input type="email" name="email" class="form-control" placeholder="Teacher Email" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success btn-block">Add Teacher</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Teachers Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachers as $index => $teacher): ?>
                <tr id="teacher-<?= $teacher['teacher_id'] ?>">
                    <td><?= $index + 1 ?></td>
                    <td><input type="text" class="form-control" id="name-<?= $teacher['teacher_id'] ?>" value="<?= htmlspecialchars($teacher['name']) ?>" disabled></td>
                    <td><input type="email" class="form-control" id="email-<?= $teacher['teacher_id'] ?>" value="<?= htmlspecialchars($teacher['email']) ?>" disabled></td>
                    <td>
                        <button class="btn btn-primary btn-sm edit-btn" data-id="<?= $teacher['teacher_id'] ?>">Edit</button>
                        <button class="btn btn-success btn-sm update-btn d-none" data-id="<?= $teacher['teacher_id'] ?>">Update</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $teacher['teacher_id'] ?>">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Add Teacher
        $('#add-teacher-form').submit(function(e) {
            e.preventDefault();
            const formData = $(this).serialize() + '&add_teacher=true';
            $.post('teacher.php', formData, function(response) {
                alert('Teacher added successfully!');
                location.reload();
            });
        });

        // Edit Teacher
        $('.edit-btn').click(function() {
            const id = $(this).data('id');
            $(`#name-${id}, #email-${id}`).prop('disabled', false);
            $(this).addClass('d-none');
            $(`.update-btn[data-id="${id}"]`).removeClass('d-none');
        });

        // Update Teacher
        $('.update-btn').click(function() {
            const id = $(this).data('id');
            const name = $(`#name-${id}`).val();
            const email = $(`#email-${id}`).val();

            $.post('teacher.php', { update_teacher: true, id, name, email }, function(response) {
                alert('Teacher updated successfully!');
                location.reload();
            });
        });

        // Delete Teacher
        $('.delete-btn').click(function() {
            if (confirm('Are you sure you want to delete this teacher?')) {
                const id = $(this).data('id');
                $.post('teacher.php', { delete_id: id }, function(response) {
                    alert('Teacher deleted successfully!');
                    location.reload();
                });
            }
        });
    </script>
</body>
</html>
