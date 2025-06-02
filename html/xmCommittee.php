<?php
// Start session
session_start();

// Redirect to login page if the session is not started or user is not logged in


// Include database connection
include 'db.php';

// Fetch batch and teacher data
$batches = $conn->query("SELECT id, batch_no, session FROM batch")->fetchAll(PDO::FETCH_ASSOC);
$teachers = $conn->query("SELECT teacher_id, name FROM teacher")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Exam Committee</title>
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
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mt-5">
        <h1 class="text-center">Assign Exam Committee</h1>

        <!-- Batch Selection -->
        <div class="form-group">
            <label for="batch-select">Batch:</label>
            <select id="batch-select" class="form-control" onchange="fetchSession()">
                <option value="" disabled selected>Select Batch</option>
                <?php foreach ($batches as $batch) : ?>
                    <option value="<?= $batch['id'] ?>" data-session="<?= $batch['session'] ?>">
                        <?= $batch['batch_no'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Term Selection -->
        <div class="form-group">
            <label for="term-select">Term:</label>
            <select id="term-select" class="form-control">
                <option value="" disabled selected>Select Term</option>
                <option value="1-1">1-1</option>
                <option value="1-2">1-2</option>
                <option value="2-1">2-1</option>
                <option value="2-2">2-2</option>
                <option value="3-1">3-1</option>
                <option value="3-2">3-2</option>
                <option value="4-1">4-1</option>
                <option value="4-2">4-2</option>
            </select>
        </div>

        <!-- Session (Auto-filled) -->
        <div class="form-group">
            <label for="session-input">Session:</label>
            <input type="text" id="session-input" class="form-control" readonly>
        </div>

        <!-- Committee Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Teacher</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $roles = ['Chairman', 'Member 1', 'Member 2', 'Member 3', 'External Member'];
                foreach ($roles as $role) : ?>
                    <tr>
                        <td><?= $role ?></td>
                        <td>
                            <select class="form-control teacher-select">
                                <option value="" disabled selected>Select Teacher</option>
                                <?php foreach ($teachers as $teacher) : ?>
                                    <option value="<?= $teacher['teacher_id'] ?>"><?= $teacher['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Save Button -->
        <button class="btn btn-success btn-block" onclick="saveData()">Save Committee</button>
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
        function fetchSession() {
            const batchSelect = document.getElementById('batch-select');
            const selectedOption = batchSelect.options[batchSelect.selectedIndex];
            const session = selectedOption.getAttribute('data-session');
            document.getElementById('session-input').value = session || '';
        }

        function saveData() {
            const batch = $('#batch-select').val();
            const term = $('#term-select').val();
            const session = $('#session-input').val();

            if (!batch || !term || !session) {
                alert('Please select batch, term, and ensure session is filled.');
                return;
            }

            const committee = {};
            let allRolesFilled = true;

            $('.teacher-select').each(function () {
                const role = $(this).closest('tr').find('td:first').text();
                const teacherId = $(this).val();

                if (!teacherId) {
                    allRolesFilled = false;
                } else {
                    if (Object.values(committee).includes(teacherId)) {
                        alert('A teacher cannot be assigned to multiple roles.');
                        throw 'Duplicate teacher selection';
                    }
                    committee[role] = teacherId;
                }
            });

            if (!allRolesFilled) {
                alert('All roles must be filled.');
                return;
            }

            $.ajax({
                url: 'save_exam_committee.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    batch: batch,
                    term: term,
                    session: session,
                    committee: committee
                }),
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        alert('Committee assigned successfully!');
                    } else {
                        alert('Failed to assign committee: ' + data.message);
                    }
                },
                error: function () {
                    alert('Error saving data.');
                }
            });
        }
    </script>
</body>

</html>
