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

// Function to fetch courses based on year and term
if (isset($_GET['year']) && isset($_GET['term'])) {
    $year = $_GET['year'];
    $term = $_GET['term'];
    $courses = getCoursesByYearTerm($year, $term);

    // Return courses as JSON
    header('Content-Type: application/json');
    echo json_encode($courses);
    exit;
}

function getCoursesByYearTerm($year, $term)
{
    global $conn;

    try {
        $query = "SELECT course_id, code, name FROM course WHERE year = :year AND term = :term";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':year', $year, PDO::PARAM_STR);
        $stmt->bindValue(':term', $term, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

// Function to fetch all teachers
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

// Handle POST request to save data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data)) {
        echo json_encode(['success' => false, 'message' => 'No data received']);
        exit;
    }

    try {
        $conn->beginTransaction();

        // Query to check for existing assignments
        $checkQuery = "SELECT COUNT(*) FROM assign_course WHERE batch_id = ? AND course_id = ? AND year = ? AND term = ?";
        $checkStmt = $conn->prepare($checkQuery);

        // Query to insert new assignments
        $insertQuery = "INSERT INTO assign_course (batch_id, course_id, year, term, teacher_id, shared) VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);

        foreach ($data as $row) {
            // Check if the course is already assigned
            $checkStmt->execute([
                $row['batch'],
                $row['courseId'],
                $row['year'],
                $row['term']
            ]);
            $isAssigned = $checkStmt->fetchColumn();

            if ($isAssigned) {
                echo json_encode([
                    'success' => false,
                    'message' => "The courses are already assigned for '{$row['batch']}' batch!"
                ]);
                $conn->rollBack();
                exit;
            }

            // Insert the new assignment
            $insertStmt->execute([
                $row['batch'],
                $row['courseId'],
                $row['year'],
                $row['term'],
                $row['courseTeacher'],
                $row['shareCourseTeacher']
            ]);
        }

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error saving data']);
    }

    exit;
}

$courses = [];
$teachers = getTeachers();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Teacher Assignment</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        html,
        body {
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
    <div class="container mt-4">
        <h1 class="text-center">Assign Course Teachers</h1>
        <div class="form-group">
            <label for="batch-select">Batch:</label>
            <select id="batch-select" class="form-control">
                <option value="" disabled selected>Select Batch</option>
                <option value="3rd">03</option>
                <option value="4th">04</option>
            </select>
        </div>
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
        <div class="form-group">
            <label for="term-select">Term:</label>
            <select id="term-select" class="form-control">
                <option value="" disabled selected>Select Term</option>
                <option value="1">1</option>
                <option value="2">2</option>
            </select>
        </div>
        <button class="btn btn-primary" onclick="fetchCourses()">Load Courses</button>
        <br />
        <!-- Button to Add/Remove Share Teacher -->
        <button class="btn btn-warning mt-3" id="toggle-share-teacher-btn" onclick="toggleShareTeacher()">Add Share Teacher</button>

        <table class="table table-bordered mt-4" id="course-teacher-table">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Course Teacher</th>
                    <th class="share-teacher-column" style="display: none;">Share Teacher</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <button class="btn btn-success btn-block" onclick="saveData()">Save</button>
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
        let teachers = <?= json_encode($teachers) ?>;

        function fetchCourses() {
            const year = document.getElementById('year-select').value;
            const term = document.getElementById('term-select').value;

            if (!year || !term) {
                alert('Please select both Year and Term.');
                return;
            }

            fetch(`office.php?year=${year}&term=${term}`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.querySelector('#course-teacher-table tbody');
                    tableBody.innerHTML = '';

                    if (data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="4">No courses found for the selected year and term.</td></tr>';
                        return;
                    }

                    data.forEach(course => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                    <td>${course.code}</td>
                    <td>${course.name}</td>
                    <td>
                        <select class="form-control course-teacher-select" data-course-id="${course.course_id}" onchange="validateTeachers(this)">
                            <option value="" disabled selected>Select Teacher</option>
                            ${teachers.map(teacher => `<option value="${teacher.teacher_id}">${teacher.name}</option>`).join('')}
                        </select>
                    </td>
                    <td class="share-teacher-column" style="display: none;">
                        <div class="d-flex align-items-center">
                            <select class="form-control share-teacher-select" onchange="validateTeachers(this)">
                                <option value="" disabled selected>Select Share Teacher</option>
                                ${teachers.map(teacher => `<option value="${teacher.teacher_id}">${teacher.name}</option>`).join('')}
                            </select>
                            <button class="btn btn-danger btn-sm ml-2" onclick="removeShareTeacher(this)">Remove</button>
                        </div>
                    </td>
                `;
                        tableBody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error fetching courses:', error));
        }

        // Validation Function to Ensure Teachers Are Not the Same
        function validateTeachers(element) {
            const row = element.closest('tr');
            const courseTeacherSelect = row.querySelector('.course-teacher-select');
            const shareTeacherSelect = row.querySelector('.share-teacher-select');

            if (courseTeacherSelect.value && shareTeacherSelect.value && courseTeacherSelect.value === shareTeacherSelect.value) {
                alert('Course Teacher and Share Teacher cannot be the same!');
                element.value = ''; // Clear the invalid selection
            }
        }

        function toggleShareTeacher() {
            const shareTeacherColumns = document.querySelectorAll('.share-teacher-column');
            const isVisible = shareTeacherColumns[0]?.style.display !== 'none';

            // Toggle visibility for each Share Teacher column
            shareTeacherColumns.forEach(col => {
                col.style.display = isVisible ? 'none' : 'table-cell';
            });

            // Update button text
            const toggleButton = document.getElementById('toggle-share-teacher-btn');
            toggleButton.innerText = isVisible ? 'Add Share Teacher' : 'Remove Share Teacher';
        }


        // Remove Share Teacher Dropdown Selection
        function removeShareTeacher(button) {
            const shareTeacherSelect = button.previousElementSibling;
            shareTeacherSelect.value = ''; // Clear the selection
        }

        // Save Data Function with Validation
        function saveData() {
            const batch = document.getElementById('batch-select').value;
            const year = document.getElementById('year-select').value;
            const term = document.getElementById('term-select').value;

            const data = [];
            let valid = true;

            document.querySelectorAll('#course-teacher-table tbody tr').forEach(row => {
                const courseId = row.cells[2].querySelector('select').getAttribute('data-course-id');
                const courseTeacherId = row.cells[2].querySelector('select').value;
                const shareTeacherId = row.cells[3].querySelector('select')?.value || '';

                if (courseTeacherId === shareTeacherId && courseTeacherId) {
                    alert('Course Teacher and Share Teacher cannot be the same in any row.');
                    valid = false;
                    return;
                }

                data.push({
                    batch: batch,
                    year: year,
                    term: term,
                    courseId: courseId,
                    courseTeacher: courseTeacherId,
                    shareCourseTeacher: shareTeacherId
                });
            });

            if (!valid) return; // Stop if any validation error exists

            fetch('office.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Data saved successfully!');
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error saving data:', error));
        }
    </script>
</body>

</html>