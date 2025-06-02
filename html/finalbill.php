<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detailed Bill Form - NSTU_QuickBill</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
            color: black;
        }
        .navbar-custom {
            background-color:#82c0ab;
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: black;
            font-weight: bold;
        }
        .navbar-custom .nav-link:hover {
            color: #cccccc;
        }
        .container {
            margin-top: 50px;
        }
        .footer {
            background-color: #061e6d;
            color: rgb(247, 246, 246);
            padding: 10px 0;
            font-size: 14px;
        }
        .footer a {
            color: rgb(238, 234, 234);
        }
        .footer a:hover {
            color: #cccccc;
            text-decoration: none;
        }
        .footer .social-icons a {
            margin: 0 10px;
            color: rgb(235, 221, 221);
        }
        .footer .social-icons a:hover {
            color: #cccccc;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <a class="navbar-brand" href="#">
            <img src="../images/Logo.png" alt="Logo" style="width: 40px; height: 40px; margin-right: 10px;">
            NSTU-Quickbill
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.html"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.html"><i class="fas fa-info-circle"></i> About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contactus.html"><i class="fas fa-envelope"></i> Contact Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="trackbilllogin.html"><i class="fas fa-search"></i> Track Bill</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell"></i> Notifications <span class="badge badge-danger" id="notification-count">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown" id="notification-list">
                        <a class="dropdown-item" href="#" data-notification-id="1">Director Submitted the bills</a>
                        <a class="dropdown-item" href="#" data-notification-id="2">Exam Control Office forward the bill</a>
                        <a class="dropdown-item" href="#" data-notification-id="3">Treasurer submitted the bill.</a>
                    </div>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="search" placeholder="Search Bill History" aria-label="Search">
                <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </nav>

    <header>
        <h2>Enter Detailed Bill Information</h2>
    </header>

    <main class="container">
        <form action="submit_bill.php" method="POST">
            <!-- Use hidden inputs to carry forward the data from the first form -->
            <input type="hidden" name="examiner_name" value="<?php echo htmlspecialchars($_GET['examiner_name']); ?>">
            <input type="hidden" name="year" value="<?php echo htmlspecialchars($_GET['year']); ?>">
            <input type="hidden" name="tutorial_number" value="<?php echo htmlspecialchars($_GET['tutorial_number']); ?>">
            <input type="hidden" name="money" value="<?php echo htmlspecialchars($_GET['money']); ?>">
            <input type="hidden" name="exam_year" value="<?php echo htmlspecialchars($_GET['exam_year']); ?>">
            <input type="hidden" name="exam_date" value="<?php echo htmlspecialchars($_GET['exam_date']); ?>">
            <input type="hidden" name="course_code_1" value="<?php echo htmlspecialchars($_GET['course_code_1']); ?>">
            <input type="hidden" name="course_title_1" value="<?php echo htmlspecialchars($_GET['course_title_1']); ?>">
            <input type="hidden" name="hour_1" value="<?php echo htmlspecialchars($_GET['hour_1']); ?>">
            <input type="hidden" name="course_code_2" value="<?php echo htmlspecialchars($_GET['course_code_2']); ?>">
            <input type="hidden" name="course_title_2" value="<?php echo htmlspecialchars($_GET['course_title_2']); ?>">
            <input type="hidden" name="hour_2" value="<?php echo htmlspecialchars($_GET['hour_2']); ?>">
            <input type="hidden" name="question_course_codes" value="<?php echo htmlspecialchars($_GET['question_course_codes']); ?>">
            <input type="hidden" name="number_of_questions" value="<?php echo htmlspecialchars($_GET['number_of_questions']); ?>">
            <input type="hidden" name="number_of_members" value="<?php echo htmlspecialchars($_GET['number_of_members']); ?>">

            <h3>৩. মূল্যায়ন</h3>
            <table class="table table-bordered">
                <tr>
                    <th>Course Codes</th>
                    <th># of Scripts</th>
                    <th># of Exams</th>
                </tr>
                <tr>
                    <td><input type="text" class="form-control" name="evaluation_course_codes" value=""></td>
                    <td><input type="number" class="form-control" name="number_of_scripts" value=""></td>
                    <td><input type="number" class="form-control" name="number_of_exams" value=""></td>
                </tr>
            </table>

            <h3>৪. পরীক্ষার ফলাফল</h3>
            <table class="table table-bordered">
                <tr>
                    <th>Course Codes</th>
                    <th># of Results</th>
                </tr>
                <tr>
                    <td><input type="text" class="form-control" name="result_course_codes" value=""></td>
                    <td><input type="number" class="form-control" name="number_of_results" value=""></td>
                </tr>
            </table>

            <div class="next-button-container">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </main>

    <footer class="footer">
        <div class="container text-center">
            <p>&copy; 2024 NSTU-Quickbill. All rights reserved.</p>
            <p><a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
            <div class="social-icons">
                <a href="#" class="fab fa-facebook-f"></a>
                <a href="#" class="fab fa-twitter"></a>
                <a href="#" class="fab fa-linkedin-in"></a>
                <a href="#" class="fab fa-instagram"></a>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
