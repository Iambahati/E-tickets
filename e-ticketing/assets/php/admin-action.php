<?php
// VALIDATIONS FOR ADMIN ACTIONS
require_once '../functions/admin.php';

// Create an instance of admin
$action = new Admin();


if (isset($_POST["addEventBtn"])) {

    $event_name = isset($_POST["event_name"]) && !empty($_POST["event_name"]) ? Utils::sanitizeInput(ucwords($_POST["event_name"])) : Utils::redirect_with_message('../interfaces/admin.php', 'error', 'Event Name cannot be blank!');
    $date = isset($_POST["date"]) && !empty($_POST["date"]) ? Utils::sanitizeInput(ucwords($_POST["date"])) : Utils::redirect_with_message('../interfaces/admin.php', 'error', 'Date cannot be blank!');
    $venue = isset($_POST["venue"]) && !empty($_POST["venue"]) ? Utils::sanitizeInput(ucwords($_POST["venue"])) : Utils::redirect_with_message('../interfaces/admin.php', 'error', 'Venue cannot be blank!');
    $time = isset($_POST["time"]) && !empty($_POST["time"]) ? Utils::sanitizeInput(ucwords($_POST["time"])) : Utils::redirect_with_message('../interfaces/admin.php', 'error', 'Time cannot be blank!');
    $ticket_price = isset($_POST["ticket_price"]) && !empty($_POST["ticket_price"]) ? Utils::sanitizeInput(ucwords($_POST["ticket_price"])) : Utils::redirect_with_message('../interfaces/admin.php', 'error', 'Ticket Price cannot be blank!');
    $tickets_available = isset($_POST["tickets_available"]) && !empty($_POST["tickets_available"]) ? Utils::sanitizeInput(ucwords($_POST["tickets_available"])) : Utils::redirect_with_message('../interfaces/admin.php', 'error', 'Tickets Available cannot be blank!');

    if ($action->addEvent($event_name, $date, $venue, $time, $ticket_price, $tickets_available)) {
        echo 'Event Successfully Added!'; // to be replaced

        Util::redirect_to('../functions/admin.php');

    }
}

if (isset($_POST["editEventBtn"])) {
    $event_name = isset($_POST["event_name"]) && !empty($_POST["event_name"]) ? Utils::sanitizeInput(ucwords($_POST["event_name"])) : Utils::redirect_with_message('../interfaces/admin.php', 'error', 'Event Name cannot be blank!');
    $date = isset($_POST["date"]) && !empty($_POST["date"]) ? Utils::sanitizeInput(ucwords($_POST["date"])) : Utils::redirect_with_message('../interfaces/admin.php', 'error', 'Date cannot be blank!');
    $venue = isset($_POST["venue"]) && !empty($_POST["venue"]) ? Utils::sanitizeInput(ucwords($_POST["venue"])) : Utils::redirect_with_message('../interfaces/admin.php', 'error', 'Venue cannot be blank!');
    $time = isset($_POST["time"]) && !empty($_POST["time"]) ? Utils::sanitizeInput(ucwords($_POST["time"])) : Utils::redirect_with_message('../interfaces/admin.php', 'error', 'Time cannot be blank!');
    $ticket_price = isset($_POST["ticket_price"]) && !empty($_POST["ticket_price"]) ? Utils::sanitizeInput(ucwords($_POST["ticket_price"])) : Utils::redirect_with_message('../interfaces/admin.php', 'error', 'Ticket Price cannot be blank!');
    $tickets_available = isset($_POST["tickets_available"]) && !empty($_POST["tickets_available"]) ? Utils::sanitizeInput(ucwords($_POST["tickets_available"])) : Utils::redirect_with_message('../interfaces/admin.php', 'error', 'Tickets Available cannot be blank!');

    if ($action->editEvent($event_name, $date, $venue, $time, $ticket_price, $tickets_available)) {
        echo 'Event Successfully Updated!'; // to be replaced

        Util::redirect_to('../functions/admin.php');

    } 
}

{
    if (isset($_POST["deleteEventBtn"])) {
        // Perform the event deletion here
        if ($action->deleteEvent()) {
            echo 'Event Successfully Deleted!';
    
            Util::redirect_to('../functions/admin.php');
        } else {
            echo 'Failed to delete the event.';
        }
    }
    
}