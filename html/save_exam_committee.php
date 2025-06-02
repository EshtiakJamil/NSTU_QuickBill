<?php
// Start session
session_start();

// Redirect to login page if the session is not started or user is not logged in
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit;
// }
include_once 'db.php';

// Handle POST request to save committee data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data) || !isset($data['committee'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid data received']);
        exit;
    }

    try {
        $conn->beginTransaction();

        // Insert into exam_committee table
        $query = "INSERT INTO exam_committee (batch_id, term, session, chairman, member1, member2, member3, external_member) 
                  VALUES (:batch, :term, :session, :chairman, :member1, :member2, :member3, :external_member)";
        $stmt = $conn->prepare($query);

        // Bind values from the received data
        $stmt->bindValue(':batch', $data['batch'], PDO::PARAM_INT);
        $stmt->bindValue(':term', $data['term'], PDO::PARAM_STR);
        $stmt->bindValue(':session', $data['session'], PDO::PARAM_STR);
        $stmt->bindValue(':chairman', $data['committee']['Chairman'], PDO::PARAM_INT);
        $stmt->bindValue(':member1', $data['committee']['Member 1'], PDO::PARAM_INT);
        $stmt->bindValue(':member2', $data['committee']['Member 2'], PDO::PARAM_INT);
        $stmt->bindValue(':member3', $data['committee']['Member 3'], PDO::PARAM_INT);
        $stmt->bindValue(':external_member', $data['committee']['External Member'], PDO::PARAM_INT);

        $stmt->execute();

        // Update the role for the chairman in the user table
        if (!empty($data['committee']['Chairman'])) {
            $updateRoleQuery = "UPDATE user SET role = 'exam' WHERE user_id = :teacher_id";
            $updateRoleStmt = $conn->prepare($updateRoleQuery);
            $updateRoleStmt->bindValue(':teacher_id', $data['committee']['Chairman'], PDO::PARAM_INT);
            $updateRoleStmt->execute();
        }

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error saving data: ' . $e->getMessage()]);
    }

    exit;
}

?>

<?php
// Include database connection and PHPMailer
include 'db.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send email to the Chairman
function sendChairmanNotification($chairmanName, $chairmanEmail, $batch, $term)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'serverns787@gmail.com';
        $mail->Password = 'jgtsmdiaxtxajgaz'; // Replace with your email password or app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('your_email@example.com', 'NSTU QuickBill System'); // Replace with your email and name
        $mail->addAddress($chairmanEmail, $chairmanName);

        $mail->Subject = 'Exam Chairman Assignment Notification';
        $mail->Body = "Dear $chairmanName,\n\nYou have been assigned as the Chairman for Batch $batch, Term $term's exam.\n\nPlease arrange the exam panel as soon as possible.\n\nThank you,\nNSTU QuickBill Team";

        $mail->send();
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
    }
}

// Handle the POST request to save the exam committee
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate inputs
    if (empty($data['batch']) || empty($data['term']) || empty($data['session']) || empty($data['committee']['Chairman'])) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    $batch = $data['batch'];
    $term = $data['term'];
    $session = $data['session'];
    $committee = $data['committee'];

    try {
        // Insert committee into database
        $stmt = $conn->prepare("
            INSERT INTO exam_committee (batch_id, term, session, chairman, member1, member2, member3, external_member)
            VALUES (:batch_id, :term, :session, :chairman, :member1, :member2, :member3, :external_member)
        ");
        $stmt->execute([
            ':batch_id' => $batch,
            ':term' => $term,
            ':session' => $session,
            ':chairman' => $committee['Chairman'],
            ':member1' => $committee['Member 1'] ?? null,
            ':member2' => $committee['Member 2'] ?? null,
            ':member3' => $committee['Member 3'] ?? null,
            ':external_member' => $committee['External Member'] ?? null,
        ]);

        // Fetch chairman details
        $stmt = $conn->prepare("SELECT name, email FROM teacher WHERE teacher_id = :teacher_id");
        $stmt->bindValue(':teacher_id', $committee['Chairman'], PDO::PARAM_INT);
        $stmt->execute();
        $chairman = $stmt->fetch(PDO::FETCH_ASSOC);

        // Send email to the Chairman
        if ($chairman) {
            sendChairmanNotification($chairman['name'], $chairman['email'], $batch, $term);
        }

        echo json_encode(['success' => true, 'message' => 'Exam committee assigned successfully, and email sent to the Chairman.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error saving committee: ' . $e->getMessage()]);
    }
    exit;
}
?>





