<?php
// Start session
session_start();

// Redirect to login page if the session is not started or user is not logged in
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit;
// }
// Include database connection
include_once 'db.php'; // Adjust the path to your database connection file

// Fetch exam committees
function getExamCommittees() {
    global $conn;
    try {
        $query = "
            SELECT 
                ec.id, 
                b.batch_no AS batch, 
                ec.term, 
                ec.session, 
                t1.name AS chairman, 
                t2.name AS member1, 
                t3.name AS member2, 
                t4.name AS member3, 
                t5.name AS external_member 
            FROM exam_committee ec
            INNER JOIN batch b ON ec.batch_id = b.id
            INNER JOIN teacher t1 ON ec.chairman = t1.teacher_id
            INNER JOIN teacher t2 ON ec.member1 = t2.teacher_id
            INNER JOIN teacher t3 ON ec.member2 = t3.teacher_id
            INNER JOIN teacher t4 ON ec.member3 = t4.teacher_id
            INNER JOIN teacher t5 ON ec.external_member = t5.teacher_id
        ";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

// Handle committee withdrawal
if (isset($_POST['withdraw_id'])) {
    $withdrawId = $_POST['withdraw_id'];
    try {
        // Fetch chairman ID to update the role
        $stmt = $conn->prepare("SELECT chairman FROM exam_committee WHERE id = :id");
        $stmt->bindValue(':id', $withdrawId, PDO::PARAM_INT);
        $stmt->execute();
        $chairmanId = $stmt->fetchColumn();

        if ($chairmanId) {
            // Delete committee
            $deleteStmt = $conn->prepare("DELETE FROM exam_committee WHERE id = :id");
            $deleteStmt->bindValue(':id', $withdrawId, PDO::PARAM_INT);
            $deleteStmt->execute();

            // Update chairman's role to 'user'
            $updateStmt = $conn->prepare("UPDATE user SET role = 'user' WHERE user_id = :id");
            $updateStmt->bindValue(':id', $chairmanId, PDO::PARAM_INT);
            $updateStmt->execute();

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Chairman not found']);
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error withdrawing committee']);
    }
    exit;
}

// Fetch exam committees for display
$committees = getExamCommittees();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Committees</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-custom {
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
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
            background: #333;
            color: white;
            padding: 10px 0;
            font-size: 14px;
            text-align: center;
        }

        .footer a {
            color: #f8d210;
        }

        .footer a:hover {
            color: #ff9f1c;
            text-decoration: none;
        }

        .footer .social-icons a {
            margin: 0 10px;
            color: white;
            font-size: 18px;
        }

        .footer .social-icons a:hover {
            color: #f8d210;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <a class="navbar-brand" href="#">NSTU-QuickBill</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="contactus.php">Contact Us</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mt-5">
        <h1 class="text-center">Exam Committees</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Batch</th>
                    <th>Term</th>
                    <th>Session</th>
                    <th>Chairman</th>
                    <th>Member 1</th>
                    <th>Member 2</th>
                    <th>Member 3</th>
                    <th>External Member</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($committees as $committee) : ?>
                    <tr>
                        <td><?= htmlspecialchars($committee['batch']) ?></td>
                        <td><?= htmlspecialchars($committee['term']) ?></td>
                        <td><?= htmlspecialchars($committee['session']) ?></td>
                        <td><?= htmlspecialchars($committee['chairman']) ?></td>
                        <td><?= htmlspecialchars($committee['member1']) ?></td>
                        <td><?= htmlspecialchars($committee['member2']) ?></td>
                        <td><?= htmlspecialchars($committee['member3']) ?></td>
                        <td><?= htmlspecialchars($committee['external_member']) ?></td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="withdrawCommittee(<?= $committee['id'] ?>)">Withdraw</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container text-center">
            <p>&copy; 2024 NSTU-QuickBill. All rights reserved.</p>
            <p><a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
            <div class="social-icons">
                <a href="#" class="fab fa-facebook-f"></a>
                <a href="#" class="fab fa-twitter"></a>
                <a href="#" class="fab fa-linkedin-in"></a>
                <a href="#" class="fab fa-instagram"></a>
            </div>
        </div>
    </footer>

    <script>
        function withdrawCommittee(id) {
            if (confirm('Are you sure you want to withdraw this committee?')) {
                $.ajax({
                    url: '',
                    method: 'POST',
                    data: { withdraw_id: id },
                    success: function (response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            alert('Committee withdrawn successfully!');
                            location.reload(); // Reload page to reflect changes
                        } else {
                            alert('Failed to withdraw committee: ' + (data.message || 'Unknown error'));
                        }
                    },
                    error: function () {
                        alert('Error withdrawing committee.');
                    }
                });
            }
        }
    </script>
</body>

</html>
