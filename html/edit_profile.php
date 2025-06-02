<?php
session_start();
require 'db.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Get user data from session
$user = $_SESSION['user'];
$userId = $user['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'];
        $photo = $_FILES['photo'];
        $uploadDir = 'uploads/';

        // Handle file upload
        if ($photo['tmp_name']) {
            // Generate a unique name for the uploaded file
            $photoPath = $uploadDir . uniqid() . "_" . basename($photo['name']);
            if (!move_uploaded_file($photo['tmp_name'], $photoPath)) {
                throw new Exception("Failed to upload the photo.");
            }
        } else {
            $photoPath = $user['photo']; // Keep the existing photo if no new file is uploaded
        }

        // Update user information in the database
        $stmt = $conn->prepare("UPDATE user SET name = :name, photo = :photo WHERE user_id = :user_id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':photo', $photoPath);
        $stmt->bindParam(':user_id', $userId);

        if ($stmt->execute()) {
            // Update session data
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['photo'] = $photoPath;

            // Redirect to profile page
            header('Location: profile.php');
            exit;
        } else {
            throw new Exception("Failed to update user data.");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Pre-fill the form with current user data
$name = $user['name'];
$email = $user['email'];
$photo = $user['photo'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Profile</h2>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email (fixed)</label>
                <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label">Profile Photo</label>
                <input type="file" name="photo" id="photo" class="form-control">
                <?php if ($photo) : ?>
                    <img src="<?php echo htmlspecialchars($photo); ?>" alt="Current Photo" style="width: 100px; margin-top: 10px;">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-success">Save Changes</button>
            <a href="profile.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
