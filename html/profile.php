<?php
session_start();
require 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Fetch user ID from session
$user = $_SESSION['user'];
$userId = $user['user_id'];

// Fetch user data from the database
$stmt = $conn->prepare("SELECT * FROM user WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle case where no user is found
if (!$userData) {
    echo "User not found! Please contact support.";
    exit;
}

// Sanitize data for output
$name = htmlspecialchars($userData['name']);
$email = htmlspecialchars($userData['email']);
$photo = !empty($userData['photo']) ? htmlspecialchars($userData['photo']) : 'default-photo.jpg';
$role = htmlspecialchars($userData['role']);
?>

<?php

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Fetch user ID from session
$user = $_SESSION['user'];
$userId = $user['user_id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $photo = $_FILES['photo'];

    // Update user data
    $query = "UPDATE user SET name = :name";
    
    // Handle photo upload if a file is provided
    if (!empty($photo['tmp_name'])) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($photo['name']);
        move_uploaded_file($photo['tmp_name'], $targetFile);
        $query .= ", photo = :photo";
    }

    $query .= " WHERE user_id = :user_id";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(':name', $name);
    if (!empty($photo['tmp_name'])) {
        $stmt->bindParam(':photo', $targetFile);
    }
    $stmt->bindParam(':user_id', $userId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// Fetch user data from the database
$stmt = $conn->prepare("SELECT * FROM user WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle case where no user is found
if (!$userData) {
    echo "User not found! Please contact support.";
    exit;
}

// Sanitize data for output
$name = htmlspecialchars($userData['name']);
$email = htmlspecialchars($userData['email']);
$photo = !empty($userData['photo']) ? htmlspecialchars($userData['photo']) : 'default-photo.jpg';
$role = htmlspecialchars($userData['role']);
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
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

        .profile-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .profile-header {
            text-align: center;
        }

        .profile-header img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        .profile-header h2 {
            font-weight: bold;
            color: #333;
        }

        .profile-header p {
            color: #555;
        }

        .actions {
            margin-top: 30px;
        }

        .btn {
            margin: 5px;
        }

        .footer {
            background: #333;
            color: white;
            padding: 20px 0;
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
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <a class="navbar-brand" href="#">
            <img src="../images/Logo.png" alt="Logo" style="width: 40px; height: 40px; margin-right: 10px;">
            NSTU-Quickbill
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" style="background-color: white;">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php"><i class="fas fa-info-circle"></i> About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contactus.php"><i class="fas fa-envelope"></i> Contact Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-in-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Profile Container -->
    <div class="container profile-container">
        <div class="text-center profile-header">
            <img src="<?php echo $photo; ?>" alt="Profile Photo">
            <h2><?php echo $name; ?></h2>
            <p><?php echo $email; ?></p>
            <p><strong>Role:</strong> <?php echo $role; ?></p>
        </div>

        <!-- Admin Actions -->
        <?php if ($role === 'Admin'): ?>
            <div id="admin-actions" class="actions">
                <h3>Admin Actions</h3>
                <button class="btn btn-primary">Add User</button>
                <button class="btn btn-warning">Update User</button>
                <button class="btn btn-danger">Delete User</button>
            </div>
        <?php endif; ?>

        <!-- Edit Profile -->
        <div class="actions">
            <h3>Edit Profile</h3>
            <a href="edit_profile.php" class="btn btn-info">Edit Profile</a>
        </div>

        <!-- Logout -->
        <div class="actions">
            <form id="logout-form" action="logout.php" method="POST">
                <button type="submit" class="btn btn-danger" id="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="text-center">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
