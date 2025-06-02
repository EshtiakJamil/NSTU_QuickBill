<?php
// Start session
session_start();

// Redirect to login page if the session is not started or user is not logged in
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit;
// }

// Include database connection
include_once 'db.php';

// Fetch courses based on year and term
if (isset($_GET['year']) && isset($_GET['term'])) {
    $year = $_GET['year'];
    $term = $_GET['term'];

    $courses = getCoursesByYearTerm($year, $term);

    // Output courses as JSON
    header('Content-Type: application/json');
    echo json_encode($courses);
    exit;
}

// Save exam panel data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data)) {
        echo json_encode(['success' => false, 'message' => 'No data received']);
        exit;
    }

    $batch = $data['batch'];
    $year = $data['year'];
    $term = $data['term'];
    $panels = $data['panels'];

    foreach ($panels as $panel) {
        $ac_id = $panel['ac_id'];
        $first_examiner = $panel['first_examiner'];
        $second_examiner = $panel['second_examiner'];
        $third_examiner = $panel['third_examiner'];
        $fourth_examiner = $panel['fourth_examiner'];

        if (!$ac_id || !$first_examiner || !$second_examiner || !$third_examiner || !$fourth_examiner) {
            echo json_encode(['success' => false, 'message' => 'All fields are required for each course.']);
            exit;
        }

        $saved = saveToExamPanels($batch, $year, $term, $ac_id, $first_examiner, $second_examiner, $third_examiner, $fourth_examiner);

        if (!$saved) {
            echo json_encode(['success' => false, 'message' => 'Error saving data for course ID: ' . $ac_id]);
            exit;
        }
    }

    echo json_encode(['success' => true, 'message' => 'Exam panel successfully saved.']);
    exit;
}

function getCoursesByYearTerm($year, $term)
{
    global $conn;

    try {
        $query = "
            SELECT DISTINCT ac.course_id as ac_id, c.code, c.name 
            FROM assign_course ac
            JOIN course c ON ac.course_id = c.course_id
            WHERE ac.year = :year AND ac.term = :term
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':year', $year, PDO::PARAM_INT);
        $stmt->bindValue(':term', $term, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

function saveToExamPanels($batch, $year, $term, $ac_id, $first_examiner, $second_examiner, $third_examiner, $fourth_examiner)
{
    global $conn;

    try {
        $query = "
            INSERT INTO exam_panels (batch, year, term, ac_id, first_examiner, second_examiner, third_examiner, fourth_examiner)
            VALUES (:batch, :year, :term, :ac_id, :first_examiner, :second_examiner, :third_examiner, :fourth_examiner)
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':batch', $batch, PDO::PARAM_STR);
        $stmt->bindValue(':year', $year, PDO::PARAM_INT);
        $stmt->bindValue(':term', $term, PDO::PARAM_INT);
        $stmt->bindValue(':ac_id', $ac_id, PDO::PARAM_INT);
        $stmt->bindValue(':first_examiner', $first_examiner, PDO::PARAM_INT);
        $stmt->bindValue(':second_examiner', $second_examiner, PDO::PARAM_INT);
        $stmt->bindValue(':third_examiner', $third_examiner, PDO::PARAM_INT);
        $stmt->bindValue(':fourth_examiner', $fourth_examiner, PDO::PARAM_INT);
        $stmt->execute();

        return true;
    } catch (PDOException $e) {
        error_log("Database error while saving exam panel: " . $e->getMessage());
        return false;
    }
}

function getTeachers()
{
    global $conn;

    try {
        $query = "SELECT teacher_id, name FROM teacher";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

$teachers = getTeachers();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Exam Panel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
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
            text-align: center;
            padding: 10px;
        }

        .footer a {
            color: #f8d210;
            text-decoration: none;
        }

        .footer a:hover {
            color: #ff9f1c;
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
        <h1 class="text-center">Create Exam Panel</h1>

        <!-- Batch Selection -->
        <div class="form-group">
            <label for="batch-select">Batch:</label>
            <select id="batch-select" class="form-control">
                <option value="" disabled selected>Select Batch</option>
                <option value="03">03</option>
                <option value="04">04</option>
            </select>
        </div>

        <!-- Year Selection -->
        <div class="form-group">
            <label for="year-select">Year:</label>
            <select id="year-select" class="form-control">
                <option value="" disabled selected>Select Year</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        </div>

        <!-- Term Selection -->
        <div class="form-group">
            <label for="term-select">Term:</label>
            <select id="term-select" class="form-control">
                <option value="" disabled selected>Select Term</option>
                <option value="1">1</option>
                <option value="2">2</option>
            </select>
        </div>

        <!-- Load Courses -->
        <button class="btn btn-primary" onclick="fetchCourses()">Load Courses</button>

        <!-- Courses Table -->
        <table class="table table-bordered mt-4" id="course-table">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>First Examiner</th>
                    <th>Second Examiner</th>
                    <th>Third Examiner</th>
                    <th>External Examiner</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <!-- Save Button -->
        <button class="btn btn-success btn-block" onclick="saveExamPanel()">Save Panel</button>
    </div>
        <br>
    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 NSTU-QuickBill. All rights reserved.</p>
        <div class="social-icons">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-linkedin-in"></a>
            <a href="#" class="fab fa-instagram"></a>
        </div>
    </footer>

    <script>
        let teachers = <?= json_encode($teachers) ?>;

        function fetchCourses() {
            const year = document.getElementById('year-select').value;
            const term = document.getElementById('term-select').value;

            if (!year || !term) {
                alert("Please select year and term.");
                return;
            }

            fetch(`createExamPanel.php?year=${year}&term=${term}`)
                .then(response => response.json())
                .then(courses => {
                    const tableBody = document.querySelector('#course-table tbody');
                    tableBody.innerHTML = '';

                    if (courses.length === 0) {
                        tableBody.innerHTML = `<tr><td colspan="6">No courses found for the selected year and term.</td></tr>`;
                        return;
                    }

                    courses.forEach(course => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${course.code}</td>
                            <td>${course.name}</td>
                            <td>
                                <select class="form-control" data-ac-id="${course.ac_id}">
                                    <option value="" disabled selected>Select Examiner</option>
                                    ${teachers.map(t => `<option value="${t.teacher_id}">${t.name}</option>`).join('')}
                                </select>
                            </td>
                            <td>
                                <select class="form-control" data-ac-id="${course.ac_id}">
                                    <option value="" disabled selected>Select Examiner</option>
                                    ${teachers.map(t => `<option value="${t.teacher_id}">${t.name}</option>`).join('')}
                                </select>
                            </td>
                            <td>
                                <select class="form-control" data-ac-id="${course.ac_id}">
                                    <option value="" disabled selected>Select Examiner</option>
                                    ${teachers.map(t => `<option value="${t.teacher_id}">${t.name}</option>`).join('')}
                                </select>
                            </td>
                            <td>
                                <select class="form-control" data-ac-id="${course.ac_id}">
                                    <option value="" disabled selected>Select Examiner</option>
                                    ${teachers.map(t => `<option value="${t.teacher_id}">${t.name}</option>`).join('')}
                                </select>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error fetching courses:', error));
        }

        function saveExamPanel() {
    const year = document.getElementById('year-select').value;
    const term = document.getElementById('term-select').value;
    const batch = document.getElementById('batch-select').value;

    if (!year || !term || !batch) {
        alert("Please select year, term, and batch.");
        return;
    }

    const rows = document.querySelectorAll('#course-table tbody tr');
    const panels = [];

    for (let row of rows) {
        const ac_id = row.querySelector('select').getAttribute('data-ac-id');
        const first_examiner = row.querySelectorAll('select')[0].value;
        const second_examiner = row.querySelectorAll('select')[1].value;
        const third_examiner = row.querySelectorAll('select')[2].value;
        const fourth_examiner = row.querySelectorAll('select')[3].value;

        // Validate that all examiners are distinct
        const examiners = [first_examiner, second_examiner, third_examiner, fourth_examiner];
        const uniqueExaminers = new Set(examiners);

        if (uniqueExaminers.size !== examiners.length) {
            alert("Each examiner in a row must be unique. Please review your selection.");
            return;
        }

        panels.push({ ac_id, first_examiner, second_examiner, third_examiner, fourth_examiner });
    }

    // Proceed to save the panel if all validations pass
    fetch('createExamPanel.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ year, term, batch, panels })
    })
        .then(response => response.json())
        .then(result => alert(result.message))
        .catch(error => console.error('Error saving exam panel:', error));
    }

    </script>
</body>

</html>
