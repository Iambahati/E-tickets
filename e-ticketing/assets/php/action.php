<?php
require_once '../../utils.php';
require_once '../../mailer.php';
require_once 'client_functions.php';

// Create a Client instance
$action = new Client();

$reservations = new Reservations();

//Handle [register] request of buyer user
if (isset($_POST["client-signup-btn"])) {
    try {

        /**
         *  ------------------------
         *   Verifying CSRF token
         *  ------------------------
         */
        if (!Utils::verifyCsrfToken()) {
            Utils::redirect_with_message('../../index.php', 'error', 'Request could not be validated.');
        }

        $fname = isset($_POST["fname"]) && !empty($_POST["fname"]) ? Utils::sanitizeInput(ucwords($_POST["fname"])) : Utils::redirect_with_message('../../buyer_signup.php', 'error', 'First name cannot be blank!');
        $lname = isset($_POST["lname"]) && !empty($_POST["lname"]) ? Utils::sanitizeInput(ucwords($_POST["lname"])) : Utils::redirect_with_message('../../buyer_signup.php', 'error', 'Last name cannot be blank!');
        $email = isset($_POST["email"]) && !empty($_POST["email"]) ? strtolower(Utils::sanitizeInput($_POST["email"])) : Utils::redirect_with_message('../../buyer_signup.php', 'error', 'Email cannot be blank!');
        $pass = isset($_POST["password"]) && !empty($_POST["password"]) ? Utils::sanitizeInput($_POST["password"]) : Utils::redirect_with_message('../../buyer_signup.php', 'error', 'Password cannot be blank!');
        $phone = isset($_POST["phone"]) && !empty($_POST["phone"]) ? Utils::sanitizeInput($_POST["phone"]) : Utils::redirect_with_message('../../buyer_signup.php', 'error', 'Phone cannot be blank!');

        $hpass = password_hash($pass, PASSWORD_DEFAULT);

        $userExists = $action->user_exists($email);
        if ($userExists && $userExists['email'] === $email) {
            Utils::redirect_with_message('../../buyer_signup.php', 'error', 'A user with this email is already registered');
            return;
        }

        // Call the [registerBuyerAcc] method
        if ($action->createUserAccount($fname, $lname, $email, $phone, $hpass, '3')) {
            Utils::redirect_with_message('../../buyer_signin.php', 'success', 'Account created successfully. Please login to continue');
            return;
        }
    } catch (Exception $e) {
        // Handle exceptions by returning an error mailBody to user
        Utils::redirect_with_message('../../buyer_signup.php', 'error', 'Opps...Some error occurred: ' . $e->getMessage());
    }
}


//Handle [login] request of buyer user
if (isset($_POST["client-signin-btn"])) {
    try {

        /**
         *  ------------------------
         *   Verifying CSRF token
         *  ------------------------
         */
        if (!Utils::verifyCsrfToken()) {
            Utils::redirect_with_message('../../buyer_signin.php', 'error', 'Request could not be validated.');
        }

        $email = isset($_POST["email"]) && !empty($_POST["email"]) ? Utils::sanitizeInput($_POST["email"]) :  Utils::redirect_with_message('../../buyer_signin.php',  'error', 'Email cannot be blank!');
        $pass = isset($_POST["password"]) && !empty($_POST["password"]) ? Utils::sanitizeInput($_POST["password"]) : Utils::redirect_with_message('../../buyer_signin.php', 'error', 'Password cannot be blank!');

        // Call the [login] method for buyer user
        $result = $action->loginIntoAccount($email);

        if (!empty($result)) {
            if( $result['role'] !== '3'){
                Utils::redirect_with_message('../../buyer_signin.php', 'error', 'User not found.');
            }
            // If passwords match, login the user and redirect to the appropriate dashboard
            if (password_verify($pass, $result['password'])) {
                $_SESSION['clientName'] = $result['first_name'] . ' ' . $result['last_name'];
                $_SESSION['userId'] = $result['id'];
                $_SESSION['accRole'] = $result['role'];
                // $logger->log('User  "' . $_SESSION['staffName'] . '" has logged in', 'Log In', $_SESSION['svcNo']);

                switch ($_SESSION['accRole']) {
                    case 1:
                        Utils::redirect_to("../../interfaces/admin.php");
                        break;
                    case 2:
                        Utils::redirect_to("../../interfaces/org-dashboard.php");
                        break;
                    case 3:
                        Utils::redirect_to("../../interfaces/events.php");
                        break;
                    default:
                        break;
                }
            } else {
                // If password doesn't match, redirect back to login page with error message
                Utils::redirect_with_message('../../buyer_signin.php', 'error', 'Wrong email or password.');
            }
        } else {
            // If no such user exists, redirect back to login page with error message
            Utils::redirect_with_message('../../buyer_signin.php', 'error', 'User not found.');
        }
    } catch (Exception $e) {
        // Handle exceptions by returning an error to the user
        Utils::redirect_with_message('../../buyer_signin.php', 'error', 'Oops... Some error occurred: ' . $e->getMessage());
    }
}


//Handle [login] request of buyer user
if (isset($_POST["client-signin-btn"])) {
    try {

        /**
         *  ------------------------
         *   Verifying CSRF token
         *  ------------------------
         */
        if (!Utils::verifyCsrfToken()) {
            Utils::redirect_with_message('../../buyer_signin.php', 'error', 'Request could not be validated.');
        }

        $email = isset($_POST["email"]) && !empty($_POST["email"]) ? Utils::sanitizeInput($_POST["email"]) :  Utils::redirect_with_message('../../buyer_signin.php',  'error', 'Email cannot be blank!');
        $pass = isset($_POST["password"]) && !empty($_POST["password"]) ? Utils::sanitizeInput($_POST["password"]) : Utils::redirect_with_message('../../buyer_signin.php', 'error', 'Password cannot be blank!');

        // Call the [login] method for buyer user
        $result = $action->loginIntoAccount($email);

        if (!empty($result)) {
            // If passwords match, login the user and redirect to the appropriate dashboard
            if (password_verify($pass, $result['password'])) {
                $_SESSION['clientName'] = $result['first_name'] . ' ' . $result['last_name'];
                $_SESSION['userId'] = $result['id'];
                $_SESSION['accRole'] = $result['role'];
                // $logger->log('User  "' . $_SESSION['staffName'] . '" has logged in', 'Log In', $_SESSION['svcNo']);

                switch ($_SESSION['accRole']) {
                    case 1:
                        Utils::redirect_to("../../interfaces/admin.php");
                        break;
                    case 2:
                        Utils::redirect_to("../../interfaces/org-dashboard.php");
                        break;
                    case 3:
                        Utils::redirect_to("../../interfaces/events.php");
                        break;
                    default:
                        break;
                }
            } else {
                // If password doesn't match, redirect back to login page with error message
                Utils::redirect_with_message('../../buyer_signin.php', 'error', 'Wrong email or password.');
            }
        } else {
            // If no such user exists, redirect back to login page with error message
            Utils::redirect_with_message('../../buyer_signin.php', 'error', 'User not found.');
        }
    } catch (Exception $e) {
        // Handle exceptions by returning an error to the user
        Utils::redirect_with_message('../../buyer_signin.php', 'error', 'Oops... Some error occurred: ' . $e->getMessage());
    }
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
