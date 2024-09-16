<?php

require_once '../../utils.php';
require_once 'admin/admin_functions.php';


// Create an instance of admin
$action = new Admin();


if (isset($_POST["addEventBtn"])) {
    try {
        $imageDir = '../images/banners/';
        $imageName = $_FILES['banner']['name'];

        // Generate a unique file name
        $fileExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $uniqueName = uniqid() . '.' . $fileExtension;

        $imagePath = $imageDir . $uniqueName;
        $thumbnailPath = '../assets/images/banners/' . $uniqueName;

        // Define the allowed banner extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        // Check if the banner extension is valid
        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            Utils::redirect_with_message('../../interfaces/admin.php', 'error', "Invalid banner file extension. Only JPG, JPEG, and PNG files are allowed.");
        }

        // Move the uploaded image to the defined dir
        if (move_uploaded_file($_FILES['banner']['tmp_name'], $imagePath)) {
            $event_name = isset($_POST["event_name"]) && !empty($_POST["event_name"]) ? Utils::sanitizeInput(ucwords($_POST["event_name"])) : Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Event Name cannot be blank!');
            $venue = isset($_POST["venue"]) && !empty($_POST["venue"]) ? Utils::sanitizeInput(ucwords($_POST["venue"])) : Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Venue cannot be blank!');
            $time = isset($_POST["time"]) && !empty($_POST["time"]) ? date('H:i:s', strtotime(Utils::sanitizeInput($_POST["time"]))) : Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Time cannot be blank!');
            $ticket_price = isset($_POST["ticket_price"]) && !empty($_POST["ticket_price"]) ? Utils::sanitizeInput($_POST["ticket_price"]) : Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Ticket Price cannot be blank!');
            $tickets_available = isset($_POST["tickets_available"]) && !empty($_POST["tickets_available"]) ? Utils::sanitizeInput((int)$_POST["tickets_available"]) : Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Tickets Available cannot be blank!');

            $event_duration = isset($_POST["event_duration"]) ? $_POST["event_duration"] : '';
            $event_type = isset($_POST["event_type"]) ? $_POST["event_type"] : '';

            // Adjust the variables based on the event duration
            if ($event_duration === 'one_day') {
                $date = isset($_POST["date"]) && !empty($_POST["date"]) ? Utils::sanitizeInput($_POST["date"]) : Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Date cannot be blank!');
                // For a one-day event, set the from_date and to_date to null
                $from_date = null;
                $to_date = null;
            } else if ($event_duration === 'date_range') {
                // For an event with a date range, validate and set the from_date and to_date variables
                $from_date = isset($_POST["from_date"]) && !empty($_POST["from_date"]) ? Utils::sanitizeInput($_POST["from_date"]) : null;
                $to_date = isset($_POST["to_date"]) && !empty($_POST["to_date"]) ? Utils::sanitizeInput($_POST["to_date"]) : null;
                $date = null;
            }

            if ($action->addEvent($event_name, $date, $venue, $time, $ticket_price, $tickets_available, $thumbnailPath, $from_date, $to_date)) {
                Utils::redirect_with_message('../../interfaces/admin.php', 'success', 'New event added successfully');
            } else {
                // Failed to insert into the database
                Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Failed to add a new event.');
            }
        } else {
            // Failed to move the uploaded image
            Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Failed to upload the image.');
        }
    } catch (Exception $e) {
        // Handle exceptions by returning an error to the user
        Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Oops... Some error occurred: ' . $e->getMessage());
        return;
    }
}

if (isset($_POST["editEventBtn"])) {

    try {
        $eventId = isset($_POST["eventId"]) && !empty($_POST["eventId"]) ? Utils::sanitizeInput($_POST["eventId"]) : Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Event Id cannot be blank!');
        $eventName = isset($_POST["event_name"]) && !empty($_POST["event_name"]) ? Utils::sanitizeInput($_POST["event_name"]) : Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Event name cannot be blank!');
        $date = isset($_POST["date"]) && !empty($_POST["date"]) ? Utils::sanitizeInput($_POST["date"]) : '0000-00-00';
        $venue = isset($_POST["venue"]) && !empty($_POST["venue"]) ? Utils::sanitizeInput(ucwords($_POST["venue"])) : Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Venue cannot be blank!');
        $time = isset($_POST["time"]) && !empty($_POST["time"]) ? date('H:i:s', strtotime(Utils::sanitizeInput($_POST["time"]))) : Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Time cannot be blank!');
        $ticket_price = isset($_POST["ticket_price"]) && !empty($_POST["ticket_price"]) ? Utils::sanitizeInput($_POST["ticket_price"]) : Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Ticket price cannot be blank!');
        $ticket_capacity = isset($_POST["ticket_capacity"]) && !empty($_POST["ticket_capacity"]) ? Utils::sanitizeInput(ucwords($_POST["ticket_capacity"])) : Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Ticket capacity cannot be blank!');
        $from_date = isset($_POST["from_date"]) && !empty($_POST["from_date"]) ? Utils::sanitizeInput($_POST["from_date"]) : '0000-00-00';
        $to_date = isset($_POST["to_date"]) && !empty($_POST["to_date"]) ? Utils::sanitizeInput($_POST["to_date"]) : '0000-00-00';

        if ($action->editEvent($eventId, $eventName, $venue, $date, $from_date, $to_date, $time, $ticket_price, $ticket_capacity)) {
            Utils::redirect_with_message('../../interfaces/admin.php', 'success', 'Event data updated successfully');
        } else {
            Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Failed to edit event data.');
        }
    } catch (Exception $e) {
        Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Opps...Some error occurred: ' . $e->getMessage());
        return;
    }
}


if (isset($_POST["deleteEventBtn"])) {
    try {
        $eventId = isset($_POST["eventId"]) && !empty($_POST["eventId"]) ? Utils::sanitizeInput($_POST["eventId"]) : Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Event Id cannot be blank!');
        // Perform the event deletion here
        $result = $action->deleteEvent($eventId);
        if ($result) {
            Utils::redirect_with_message('../../interfaces/admin.php', 'success', 'Event Deleted!');
        } else {
            Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'You cannot delete an event that has reservations made by users!');
        }
    } catch (Exception $e) {
        // Handle exceptions by returning error to the user
        Utils::redirect_with_message('../../interfaces/admin.php', 'error', 'Opps...Some error occurred: ' . $e->getMessage());
        return;
    }
}
