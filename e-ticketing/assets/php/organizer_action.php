<?php
require_once '../../utils.php';
require_once '../../mailer.php';
require_once 'organizer_functions.php';

// Create a Client instance
$action = new Organizer();


//Handle [register] request of organizer user
if (isset($_POST["organizer-signup-btn"])) {
    try {

        /**
         *   Verifying CSRF token
         *  ------------------------
         */
        if (!Utils::verifyCsrfToken()) {
            Utils::redirect_with_message('../../organizer_signup.php', 'error', 'Request could not be validated.');
        }

        $orgname = isset($_POST["orgname"]) && !empty($_POST["orgname"]) ? Utils::sanitizeInput(ucwords($_POST["orgname"])) : Utils::redirect_with_message('../../buyer_signup.php', 'error', 'Organisation name cannot be blank!');
        $email = isset($_POST["email"]) && !empty($_POST["email"]) ? strtolower(Utils::sanitizeInput($_POST["email"])) : Utils::redirect_with_message('../../buyer_signup.php', 'error', 'Email cannot be blank!');
        $pass = isset($_POST["password"]) && !empty($_POST["password"]) ? Utils::sanitizeInput($_POST["password"]) : Utils::redirect_with_message('../../buyer_signup.php', 'error', 'Password cannot be blank!');
        $phone = isset($_POST["phone"]) && !empty($_POST["phone"]) ? Utils::sanitizeInput($_POST["phone"]) : Utils::redirect_with_message('../../buyer_signup.php', 'error', 'Phone cannot be blank!');

        $hpass = password_hash($pass, PASSWORD_DEFAULT);

        $userExists = $action->user_exists($email);
        if ($userExists && $userExists['email'] === $email) {
            Utils::redirect_with_message('../../organizer_signup.php', 'error', 'A merchant with this email is already registered');
            return;
        }

        // Call the [registerBuyerAcc] method
        if ($action->createMerchantAccount($orgname, $email, $phone, $hpass, '2')) {
            Utils::redirect_with_message('../../organizer_signin.php', 'success', 'Merchant account created successfully. Please login to continue');
            return;
        }
    } catch (Exception $e) {
        // Handle exceptions by returning an error mailBody to user
        Utils::redirect_with_message('../../organizer_signup.php', 'error', 'Opps...Some error occurred: ' . $e->getMessage());
    }
}


//Handle [login] request of organizer 
if (isset($_POST["organizer-signin-btn"])) {
    try {

        /**
         *  ------------------------
         *   Verifying CSRF token
         *  ------------------------
         */
        if (!Utils::verifyCsrfToken()) {
            Utils::redirect_with_message('../..organizer_signin.php', 'error', 'Request could not be validated.');
        }

        $email = isset($_POST["email"]) && !empty($_POST["email"]) ? Utils::sanitizeInput($_POST["email"]) :  Utils::redirect_with_message('../../buyer_signin.php',  'error', 'Email cannot be blank!');
        $pass = isset($_POST["password"]) && !empty($_POST["password"]) ? Utils::sanitizeInput($_POST["password"]) : Utils::redirect_with_message('../../buyer_signin.php', 'error', 'Password cannot be blank!');

        // Call the [login] method for buyer user
        $result = $action->loginIntoMerchantAccount($email);

        if (!empty($result)) {
            // If passwords match, login the user and redirect to the appropriate dashboard
            if (password_verify($pass, $result['password'])) {
                $_SESSION['clientName'] = (is_null($result['first_name']) && is_null($result['last_name'])) ? $result['organization_name'] : $result['first_name'] . ' ' . $result['last_name'];

                $_SESSION['userId'] = $result['id'];
                $_SESSION['accRole'] = $result['role'];
                // $logger->log('User  "' . $_SESSION['staffName'] . '" has logged in', 'Log In', $_SESSION['svcNo']);

                switch ($_SESSION['accRole']) {
                    case 1:
                        Utils::redirect_to("../../interfaces/org-dashboard.php");
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
                Utils::redirect_with_message('../../organizer_signin.php', 'error', 'Wrong email or password.');
            }
        } else {
            // If no such user exists, redirect back to login page with error message
            Utils::redirect_with_message('../../organizer_signin.php', 'error', 'User not found.');
        }
    } catch (Exception $e) {
        // Handle exceptions by returning an error to the user
        Utils::redirect_with_message('../../organizer_signin.php', 'error', 'Oops... Some error occurred: ' . $e->getMessage());
    }
}

// Handle add event request
if (isset($_POST['create-event-btn'])) {
    try {
        // Check if the CSRF token is valid
        if (!Utils::verifyCsrfToken()) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Request could not be validated.');
        }

        // Required fields
        $required_fields = ['organizer', 'eventName', 'description', 'eventLocation', 'eventType', 'startDate', 'endDate', 'ticket_price', 'tickets_available'];

        // Check for missing fields
        $missing_fields = array_filter($required_fields, fn($field) => empty($_POST[$field]));

        // If there are missing fields, redirect with an error message
        if ($missing_fields) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'The following fields are required: ' . implode(', ', $missing_fields) . '.');
            return;
        }
        // Check if the event start date is not in the past
        if (strtotime($_POST['startDate']) < strtotime(date('Y-m-d'))) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Event start date cannot be in the past.');
            return;
        }

        // Check if the event end date is not before the start date
        if (strtotime($_POST['endDate']) < strtotime($_POST['startDate'])) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Event end date cannot be before the start date.');
            return;
        }

        // Check if the ticket price is not negative
        if ($_POST['ticket_price'] < 0) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Ticket price cannot be negative.');
            return;
        }

        // Check if the ticket quantity available is not negative
        if ($_POST['tickets_available'] < 0) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Tickets available cannot be negative.');
            return;
        }

        // Check if the event photo Fis not empty and has been uploaded successfully
        if (empty($_FILES['eventPhoto']['name']) || $_FILES['eventPhoto']['error'] !== UPLOAD_ERR_OK) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Event photo is required.');
            return;
        }


        // Sanitize input
        $organizer_id = filter_var($_POST['organizer'], FILTER_SANITIZE_NUMBER_INT);
        $title = filter_var($_POST['eventName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $location_details = filter_var($_POST['eventLocation'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $event_type = filter_var($_POST['eventType'], FILTER_SANITIZE_NUMBER_INT);
        $start_datetime = filter_var($_POST['startDate'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $end_datetime = filter_var($_POST['endDate'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $ticket_price = filter_var($_POST['ticket_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $ticket_quantity_available = filter_var($_POST['tickets_available'], FILTER_SANITIZE_NUMBER_INT);

        // Handle file upload
        $event_photo = '';
        $target_dir = "../images/uploads/";

        // Sanitize the title: replace spaces with underscores and make it URL-friendly
        $sanitized_title = str_replace(' ', '_', $title);
        $sanitized_title = preg_replace('/[^A-Za-z0-9_\-]/', '', $sanitized_title); // Remove invalid characters

        // Generate a unique file name
        $file_extension = pathinfo($_FILES["eventPhoto"]["name"], PATHINFO_EXTENSION);
        $unique_name = $sanitized_title . '_' . uniqid() . '.' . $file_extension; // Append unique ID to the title
        $target_file = $target_dir . $unique_name;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["eventPhoto"]["tmp_name"], $target_file)) {
            $event_photo = $target_file; // Store the path of the uploaded photo
        } else {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Failed to upload image.');
            return;
        }
        // Add event to database
        $event_id = $action->addEvent(
            $organizer_id,
            $title,
            $description,
            $location_details,
            $event_type,
            $start_datetime,
            $end_datetime,
            $ticket_price,
            $ticket_quantity_available,
            $event_photo
        );

        if ($event_id) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'success', 'Event created successfully.');
        } else {
            // Error adding event
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Failed to create event.');
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'An unexpected error occurred' . $e->getMessage());
    }
}



// Handle edit event request
if (isset($_POST['edit-event-btn'])) {
    try {
        // Check if the CSRF token is valid
        if (!Utils::verifyCsrfToken()) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Request could not be validated.');
        }

        // Required fields
        $required_fields = ['eventId', 'organizer', 'eventName', 'description', 'eventLocation', 'eventType', 'startDate', 'endDate', 'ticket_price', 'tickets_available'];

        // Check for missing fields
        $missing_fields = array_filter($required_fields, fn($field) => empty($_POST[$field]));

        // If there are missing fields, redirect with an error message
        if ($missing_fields) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'The following fields are required: ' . implode(', ', $missing_fields) . '.');
            return;
        }
        // Check if the event start date is not in the past
        if (strtotime($_POST['startDate']) < strtotime(date('Y-m-d'))) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Event start date cannot be in the past.');
            return;
        }

        // Check if the event end date is not before the start date
        if (strtotime($_POST['endDate']) < strtotime($_POST['startDate'])) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Event end date cannot be before the start date.');
            return;
        }

        // Check if the ticket price is not negative
        if ($_POST['ticket_price'] < 0) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Ticket price cannot be negative.');
            return;
        }

        // Check if the ticket quantity available is not negative
        if ($_POST['tickets_available'] < 0) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Tickets available cannot be negative.');
            return;
        }

        // Check if the event photo Fis not empty and has been uploaded successfully
        //    uncomment to fix later
        // if (empty($_FILES['eventPhoto']['name']) || $_FILES['eventPhoto']['error'] !== UPLOAD_ERR_OK) {
        //     Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Event photo is required.');
        //     return;
        // }


        // Sanitize input
        $event_id = filter_var($_POST['eventId'], FILTER_SANITIZE_NUMBER_INT);
        $organizer_id = filter_var($_POST['organizer'], FILTER_SANITIZE_NUMBER_INT);
        $title = filter_var($_POST['eventName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $location_details = filter_var($_POST['eventLocation'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $event_type = filter_var($_POST['eventType'], FILTER_SANITIZE_NUMBER_INT);
        $start_datetime = filter_var($_POST['startDate'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $end_datetime = filter_var($_POST['endDate'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $ticket_price = filter_var($_POST['ticket_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $ticket_quantity_available = filter_var($_POST['tickets_available'], FILTER_SANITIZE_NUMBER_INT);

        // Handle file upload
        // $event_photo = '';
        // $target_dir = "../images/uploads/";

        // // Sanitize the title: replace spaces with underscores and make it URL-friendly
        // $sanitized_title = str_replace(' ', '_', $title);
        // $sanitized_title = preg_replace('/[^A-Za-z0-9_\-]/', '', $sanitized_title); // Remove invalid characters

        // // Generate a unique file name
        // $file_extension = pathinfo($_FILES["eventPhoto"]["name"], PATHINFO_EXTENSION);
        // $unique_name = $sanitized_title . '_' . uniqid() . '.' . $file_extension; // Append unique ID to the title
        // $target_file = $target_dir . $unique_name;

        // // Move the uploaded file to the target directory
        // if (move_uploaded_file($_FILES["eventPhoto"]["tmp_name"], $target_file)) {
        //     $event_photo = $target_file; // Store the path of the uploaded photo
        // } else {
        //     Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Failed to upload image.');
        //     return;
        // }
        // Add event to database

        //         echo "<pre>"; // Optional for better formatting of the output
        // var_dump([
        //     'event_id' => $event_id,
        //     'organizer_id' => $organizer_id,
        //     'title' => $title,
        //     'description' => $description,
        //     'location_details' => $location_details,
        //     'event_type' => $event_type,
        //     'start_datetime' => $start_datetime,
        //     'end_datetime' => $end_datetime,
        //     'ticket_price' => $ticket_price,
        //     'ticket_quantity_available' => $ticket_quantity_available
        // ]);
        // echo "</pre>";

        if ($action->updateEvent(
            $event_id,
            $organizer_id,
            $title,
            $description,
            $location_details,
            $event_type,
            $start_datetime,
            $end_datetime,
            $ticket_price,
            $ticket_quantity_available,
        )) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'success', 'Event updated successfully.');
        } else {
            // Error adding event
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Failed to create event.');
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'An unexpected error occurred' . $e->getMessage());
    }
}


if (isset($_POST["delete-event-btn"])) {
    try {
        $eventId = isset($_POST["eventId"]) && !empty($_POST["eventId"]) ? Utils::sanitizeInput($_POST["eventId"]) : Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Event Id cannot be blank!');
        // Perform the event deletion here
        $result = $action->deleteEvent($eventId);
        if ($result) {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'success', 'Event Deleted!');
        } else {
            Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'You cannot delete an event that has reservations made by users!');
        }
    } catch (Exception $e) {
        // Handle exceptions by returning error to the user
        Utils::redirect_with_message('../../interfaces/org-dashboard.php', 'error', 'Opps...Some error occurred: ' . $e->getMessage());
        return;
    }
}
