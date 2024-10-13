<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set correct header for JSON response
header('Content-Type: application/json');

// Include the Google Authenticator library
require_once 'PHPGangsta/GoogleAuthenticator.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $secretKey = $_POST['secretKey'];

    if (empty($secretKey)) {
        echo json_encode(['error' => 'Secret key is required']);
        exit;
    }

    try {
        $gAuth = new PHPGangsta_GoogleAuthenticator();
        
        // Generate current TOTP based on the secret key
        $currentTimeSlice = floor(time() / 30);  // Current time slice
        $currentTOTP = $gAuth->getCode($secretKey, $currentTimeSlice);

        // Generate the next TOTP by moving to the next time slice
        $nextTimeSlice = $currentTimeSlice + 1;  // Move to the next time slice (30 seconds ahead)
        $nextTOTP = $gAuth->getCode($secretKey, $nextTimeSlice);

        // Return the current and next TOTP code as JSON
        echo json_encode(['currentTOTP' => $currentTOTP, 'nextTOTP' => $nextTOTP]);
    } catch (Exception $e) {
        // Return any errors as JSON
        echo json_encode(['error' => 'Error generating TOTP: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
