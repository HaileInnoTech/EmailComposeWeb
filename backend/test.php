<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Accept');

$previewHtml = null;
$receiverEmail = null;
$senderEmail = null;
$senderNickname = null;
$subject = null;
$senderPassword = null;

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve the data from the FormData
    $previewHtml = $_POST['previewHtml'];
    $senderEmail = $_POST['senderEmail'];
    $subject = $_POST['subject'];
    $senderNickname = $_POST['senderNickname'];
    $senderPassword = $_POST['senderPassword'];
    $receiverEmail = $_POST['receiverEmail'];

    $uploadDir = 'uploads/';
    if (file_exists($uploadDir)) {
        // Delete all files within the directory and the directory itself
        deleteDirectory($uploadDir);
    }
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            die("Failed to create the upload directory.");
        }
    }
    $images = [];
    if (isset($_FILES['images']) && is_array($_FILES['images'])) {
        $imageCount = count($_FILES['images']['name']);
        for ($i = 0; $i < $imageCount; $i++) {
            $imageName = $_FILES['images']['name'][$i];
            $imageTmpName = $_FILES['images']['tmp_name'][$i];
            $imageType = $_FILES['images']['type'][$i];
            $imageSize = $_FILES['images']['size'][$i];
            $imageError = $_FILES['images']['error'][$i];
            $targetFilePath = $uploadDir . $imageName;
            move_uploaded_file($imageTmpName, $targetFilePath);
            $images[] = [
                'name' => $imageName,
                'type' => $imageType,
                'size' => $imageSize,
                'error' => $imageError,
                'path' => $targetFilePath,
            ];
        }
    }
    $response = ['status' => 'success', 'message' => 'Data received successfully.'];

    // Output all the received data for debugging purposes
    echo "<h2>Received Data:</h2>";
    echo "<pre>";
    echo "Preview HTML: " . $previewHtml . "\n";
    echo "Sender Email: " . $senderEmail . "\n";
    echo "Subject: " . $subject . "\n";
    echo "Sender Nickname: " . $senderNickname . "\n";
    echo "Sender Password: " . $senderPassword . "\n";
    echo "Receiver Email: " . $receiverEmail . "\n";
    echo "Uploaded Images:\n";
    print_r($images);
    echo "</pre>";
} else {
    // Return an error response if the request method is not POST
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}


try {
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
    // Server settings for Gmail
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $senderEmail; // Replace with your Gmail username (email address)
    $mail->Password = $senderPassword; // Replace with your Gmail password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // Sender
    $mail->setFrom($senderEmail, $senderNickname);

    // Recipients
    $emailList = explode(', ', $receiverEmail);
    foreach ($emailList as $email) {
        $mail->addAddress($email);
    }

    $numImages = count($images); // Number of images to process
    $imagePaths = [];

    for ($i = 1; $i <= $numImages; $i++) {
        $imageKey = 'image' . $i;
        $imagePaths[$imageKey] = __DIR__ . '/uploads/' . $imageKey . '.png';
        $mail->addEmbeddedImage($imagePaths[$imageKey], $imageKey);
        $placeholder = '{' . $imageKey . '}';
        $previewHtml = str_replace($placeholder, '<img src="cid:image' . $i . '">', $previewHtml);
    }

    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $previewHtml;
    $mail->send();

    // Add a success message to the response
    $response['message'] = 'Email sent successfully';
} catch (Exception $e) {
    // Add an error message to the response
    $response['error'] = 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
}
echo json_encode($response);

?>

<?php
function deleteDirectory($dir)
{
    if (!is_dir($dir)) {
        return;
    }

    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $filePath = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($filePath)) {
            deleteDirectory($filePath);
        } else {
            unlink($filePath);
        }
    }

    rmdir($dir);
}
?>