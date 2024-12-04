<?php
// Display PHP errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize response
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Include database connection
        require_once '../../config/database.php';

        // Collect form inputs
        $userName = $_POST['user_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $message = $_POST['message'] ?? '';

        // Validate required fields
        if (empty($userName) || empty($email) || empty($message)) {
            throw new Exception("All fields are required.");
        }

        // Handle file upload if provided
        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "../../public/assets";
            $fileName = uniqid() . "_" . basename($_FILES["image"]["name"]);
            $targetFilePath = $targetDir . $fileName;

            // Ensure uploads directory exists
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Move uploaded file
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                throw new Exception("Error uploading image.");
            }

            $imagePath = $targetFilePath;
        }

        // Insert feedback into the database
        $stmt = $pdo->prepare("
            INSERT INTO feedback (user_name, email, message, image_path)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$userName, $email, $message, $imagePath]);

        // Response
        $response['success'] = true;
        $response['message'] = "Thank you for your feedback!";
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
} else {
    $response['message'] = "Invalid request method.";
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
