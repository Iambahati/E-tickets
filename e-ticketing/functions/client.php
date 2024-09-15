<?php
session_start();

// Include the database connection file
require '../connect.php';

// Function to generate QR code
function generateQRCode($userId) {
    $pdo = connectToDatabase();

    // Retrieve the user's information from the database
    $stmt = $pdo->prepare("SELECT id, number_of_tickets, total_amount, users_id, events_id, transactions_id FROM reservations WHERE users_id = :userId");
    $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Generate the QR code content
    $content = json_encode($row);

    // Generate the QR code using a QR code library of your choice (e.g., QRCode for PHP)
    // Make sure to include the QR code library in your project
    require 'path_to_qrcode_library/phpqrcode.php';

    $qrCodeImagePath = 'path_to_save_qr_code/qrcode.png';

    QRcode::png($content, $qrCodeImagePath, QR_ECLEVEL_L, 10);

    // Output the QR code image
    echo '<img src="' . $qrCodeImagePath . '" alt="QR Code">';
}

// Assuming the user is logged in and their ID is stored in a session variable
$userId = $_SESSION['user_id'];

// Call the function to generate the QR code
generateQRCode($userId);
?>
