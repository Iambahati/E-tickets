<?php

require_once '../../mailer.php';
require_once 'organizer_functions.php';

if ($argc < 10) {
    die("Usage: php send_email.php <email> <eventName> <venue> <description> <ticketPrice> <orderHash> <totalAmount> <totalTickets>\n");
}

$email = $argv[1];
$eventName = $argv[2];
$venue = $argv[3];
$description = $argv[4];
$ticketPrice = $argv[5];
$orderHash = $argv[6];
$totalAmount = $argv[7];
$totalTickets = $argv[8];

Mailer::sendPaymentReceiptByEmail($email, $eventName, $venue, $description, $ticketPrice, $orderHash, $totalAmount, $totalTickets);