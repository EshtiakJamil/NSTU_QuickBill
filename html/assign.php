<?php
// Start session
session_start();

// Redirect to login page if the session is not started or user is not logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'office') {
    header('Location: login.php'); // Redirect to login if not an admin
    exit;
}

// Database connection
include_once 'db.php';

// Handle DELETE request
if (isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];
    try {
        $query = "DELETE FROM assign_course WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $deleteId, PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Record deleted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error deleting record']);
    }
    exit;
}

// Handle EDIT request (save updates)
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $teacherId = $_POST['teacher_id'];
    $sharedTeacherId = $_POST['shared_teacher_id'];
    try {
        $query = "UPDATE assign_course SET teacher_id = :teacher_id, shared = :shared_teacher_id WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':teacher_id' => $teacherId,
            ':shared_teacher_id' => $sharedTeacherId,
            ':id' => $id
        ]);
        echo json_encode(['success' => true, 'message' => 'Record updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error updating record']);
    }
    exit;
}

// Fetch saved data
try {
    $query = "SELECT ac.id, ac.batch_id, ac.year, ac.term, c.code AS course_code, c.name AS course_name, 
                     t1.name AS teacher_name, t2.name AS shared_teacher_name
              FROM assign_course ac
              JOIN course c ON ac.course_id = c.course_id
              JOIN teacher t1 ON ac.teacher_id = t1.teacher_id
              LEFT JOIN teacher t2 ON ac.shared = t2.teacher_id";
    $stmt = $conn->query($query);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $assignments = [];
}

// Fetch all teachers for dropdowns
try {
    $teachers = $conn->query("SELECT teacher_id, name FROM teacher")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $teachers = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Courses</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Assigned Courses</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Batch</th>
                    <th>Year</th>
                    <th>Term</th>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Course Teacher</th>
                    <th>Shared Teacher</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $row) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['batch_id']) ?></td>
                        <td><?= htmlspecialchars($row['year']) ?></td>
                        <td><?= htmlspecialchars($row['term']) ?></td>
                        <td><?= htmlspecialchars($row['course_code']) ?></td>
                        <td><?= htmlspecialchars($row['course_name']) ?></td>
                        <td>
                            <select class="form-control teacher-select" data-id="<?= $row['id'] ?>">
                                <?php foreach ($teachers as $teacher) : ?>
                                    <option value="<?= $teacher['teacher_id'] ?>" <?= $teacher['name'] === $row['teacher_name'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($teacher['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-control shared-teacher-select" data-id="<?= $row['id'] ?>">
                                <option value="">None</option>
                                <?php foreach ($teachers as $teacher) : ?>
                                    <option value="<?= $teacher['teacher_id'] ?>" <?= $teacher['name'] === $row['shared_teacher_name'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($teacher['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $row['id'] ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Delete Functionality
        $('.delete-btn').on('click', function () {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this record?')) {
                $.post('assign.php', { delete_id: id }, function (response) {
                    const res = JSON.parse(response);
                    if (res.success) {
                        alert(res.message);
                        location.reload();
                    } else {
                        alert('Error: ' + res.message);
                    }
                });
            }
        });

        // Edit/Update Functionality
        $('.teacher-select, .shared-teacher-select').on('change', function () {
            const id = $(this).data('id');
            const teacherId = $(this).closest('tr').find('.teacher-select').val();
            const sharedTeacherId = $(this).closest('tr').find('.shared-teacher-select').val();

            if (teacherId === sharedTeacherId && teacherId) {
                alert('Course Teacher and Shared Teacher cannot be the same!');
                $(this).val('');
                return;
            }

            $.post('assign.php', {
                update: true,
                id: id,
                teacher_id: teacherId,
                shared_teacher_id: sharedTeacherId
            }, function (response) {
                const res = JSON.parse(response);
                if (res.success) {
                    alert(res.message);
                } else {
                    alert('Error: ' + res.message);
                }
            });
        });
    </script>
</body>

</html>
