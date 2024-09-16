<?php
require_once '../../utils.php';
require_once '../../mailer.php';
require_once 'client_functions.php';

// Create a Client instance
$action = new Client();

$reservations = new Reservations();

// Decode the request body JSON data into an associative array
$requestBody = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestBody['formType'] == 'signup') {

    $response = null;
    try {
        // Sanitize the inputs
        $name = isset($requestBody['username']) ? Utils::sanitizeInput($requestBody['username']) : '';
        $email = isset($requestBody['email']) ? Utils::sanitizeInput($requestBody['email']) : '';
        $contact = isset($requestBody['contact']) ? Utils::sanitizeInput($requestBody['contact']) : '';
        $password = isset($requestBody['password']) ? Utils::sanitizeInput($requestBody['password']) : '';
        $hpass = password_hash($password, PASSWORD_DEFAULT);

        // Defined an array to hold the error messages
        $errorMessages = [];

        if (empty($name)) {
            $errorMessages[] = 'Username cannot be blank!';
        }
        if (empty($email)) {
            $errorMessages[] = 'Email cannot be blank!';
        }
        if (empty($password)) {
            $errorMessages[] = 'Password cannot be blank!';
        }
        if (empty($contact)) {
            $errorMessages[] = 'Contact cannot be blank!';
        }

        if (!empty($errorMessages)) {
            // Construct error messages by iterating through the array and appending them
            $errorMessage = '';
            foreach ($errorMessages as $message) {
                $errorMessage .= $message;
            }

            // Prepare the response with the error messages
            $response = [
                'success' => false,
                'message' => $errorMessage
            ];
        } else {
            // Check if a user with the provided email already exists
            $userExists = $action->user_exists($email);

            if ($userExists && $userExists['email'] === $email) {
                // Construct response if a user with the email already exists
                $response = [
                    'success' => false,
                    'message' => 'A user with this email is already registered'
                ];
            } else {
                // Create an account for a user
                if ($action->createUserAccount($name, $email, $contact, $hpass)) {
                    // Construct success response
                    $response = [
                        'success' => true,
                        'message' => 'Account created successfully. Please login'
                    ];
                } else {
                    // Construct response if account creation fails
                    $response = [
                        'success' => false,
                        'message' => 'Failed to create account. Please try again.'
                    ];
                }
            }
        }
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => 'Oops... Some error occurred: ' . $e->getMessage()
        ];
    }
    echo json_encode($response);
    die();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestBody['formType'] == 'signin') {
    $response = null;

    try {
        $email = isset($requestBody['email']) ? Utils::sanitizeInput($requestBody['email']) : '';
        $pass = isset($requestBody['password']) ? Utils::sanitizeInput($requestBody['password']) : '';

        $errorMessages = [];

        if (empty($email)) {
            $errorMessages[] = 'Email cannot be blank!';
        }

        if (empty($pass)) {
            $errorMessages[] = 'Password cannot be blank!';
        }

        if (!empty($errorMessages)) {
            $errorMessage = '';
            foreach ($errorMessages as $message) {
                $errorMessage .= $message;
            }
            $response = [
                'success' => false,
                'message' => $errorMessage
            ];
        }
        // Call the [loginIntoAccount] method for a user
        $result = $action->loginIntoAccount($email);

        $isAdmin = ($result['is_admin'] == 1) ? 'true' : 'false';

        if (!empty($result) && password_verify($pass, $result['password'])) {

            $isAdmin = ($result['is_admin'] == 1) ? true : false;

            // User provided correct password
            $redirectNode = $isAdmin ? 'admin' : 'client';
            $_SESSION['userType'] = $redirectNode;
            switch ($_SESSION['userType']) {
                case 'admin':
                    $_SESSION['admin'] =  $result['username'];
                    $_SESSION['adminEmail'] = $email;
                    break;
                case 'client':
                    $_SESSION['client'] = $result['username'];
                    $_SESSION['clientEmail'] = $email;
                    break;
                default:
                    break;
            }

            $response = [
                'success' => true,
                'message' => $redirectNode
            ];
        } else {
            // User provided incorrect email or password
            $response = [
                'success' => false,
                'message' => 'Invalid email or password.'
            ];
        }
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => 'Oops... Some error occurred: ' . $e->getMessage()
        ];
        return;
    }

    echo json_encode($response);

    die();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestBody['formType'] == 'contact') {
    try {
        $email = isset($requestBody["email_from"]) && !empty($requestBody["email_from"]) ? Utils::sanitizeInput($requestBody["email_from"]) : '';
        $sub = isset($requestBody["subject"]) && !empty($requestBody["subject"]) ? Utils::sanitizeInput($requestBody["subject"]) : '';
        $message = isset($requestBody["message"]) && !empty($requestBody["message"]) ? Utils::sanitizeInput($requestBody["message"]) : '';

        $errorMessages = [];

        if (empty($email)) {
            $errorMessages[] = 'Email cannot be blank!';
        }

        if (empty($sub)) {
            $errorMessages[] = 'Subject cannot be blank!';
        }

        if (empty($message)) {
            $errorMessages[] = 'Message cannot be blank!';
        }

        if (!empty($errorMessages)) {
            $errorMessage = '';
            foreach ($errorMessages as $message) {
                $errorMessage .= $message;
            }
            $response = [
                'success' => false,
                'message' => $errorMessage
            ];
        }

        $mailBody = "You have a new inquiry from" . $email . "<br><br>" . $message;

        if (Mailer::sendMail("adm1n.tickectok@gmail.com", $sub, $mailBody)) {
            $response = [
                'success' => true,
                'message' => 'We appreciate you contacting us. One of our colleagues will get back in touch with you soon!'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Email not sent. An error was encountered.'
            ];
        }
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => 'Oops... Some error occurred: ' . $e->getMessage()
        ];
    }

    echo json_encode($response);
    die();
}

if (isset($_POST["purchase-ticket-btn"])) {
    try {
        $eventId = isset($_POST["eventId"]) && !empty($_POST["eventId"]) ? Utils::sanitizeInput($_POST["eventId"]) : Utils::redirect_with_message('../../interfaces/events.php', 'error', 'Event Id cannot be blank!');
        $userEmail = $_SESSION['clientEmail'];
        $userId = $action->fetchUserIdByEmail($userEmail);
        $no_of_tckts = isset($_POST["number_of_tickets"]) ? Utils::sanitizeInput((int)$_POST["number_of_tickets"]) : 0;

        $price = isset($_POST["ticket_price"]) ? Utils::sanitizeInput((float)$_POST["ticket_price"]) : 0.0;
        $total_price = $no_of_tckts * $price;

        $eventName = $action->fetchEventNameById($eventId);

        // Fetch the event date information based on the event ID
        $eventDateResult = $action->fetchEventDateById($eventId);

        // Extract individual date components from the result
        $eventDate = $eventDateResult['date']; // Single event date
        $fromDate = $eventDateResult['from_date']; // Start date of the date range
        $toDate = $eventDateResult['to_date']; // End date of the date range

        // Generate the validity dates based on event date or date range
        $validityDates = ($eventDate !== null)
            ? date('D, M d, Y', strtotime($eventDate)) // If event date exists, format it as a single date
            : (($fromDate !== null && $toDate !== null)
                ? (date('D, M d, Y', strtotime($fromDate)) . ' - ' . date('D, M d, Y', strtotime($toDate))) // If from_date and to_date exist, format them as a date range
                : 'N/A'); // If both dates are null, set validityDates as 'N/A'

        if ($reservations->confirmReservationAndSendTickets($no_of_tckts, $total_price, $userEmail, $userId, $eventId, $eventName, $validityDates)) {
            Utils::redirect_with_message('../../interfaces/events.php', 'success', 'Purchase successful. Please check email for your tickets');
        } else {
            Utils::redirect_with_message('../../interfaces/events.php', 'error', 'Purchase Failed');
            return;
        }
    } catch (Exception $e) {
        // Handle exceptions by returning an error mailBody to user
        Utils::redirect_with_message('../../index.php', 'error', 'Opps...Some error occurred: ' . $e->getMessage());
    }
}
