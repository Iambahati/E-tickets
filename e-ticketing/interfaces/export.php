<?php

require_once __DIR__ . '../../vendor/autoload.php';

require_once  '../assets/php/client_functions.php';

require_once '../utils.php';

// Assuming $userId is the ID of the user whose events you want to report
$userId = $_SESSION['userId'];

$interfaces = new Client();

$events = $interfaces->getAttendedEventsHistory($userId);

if(empty($events)) {
    Utils::redirect_with_message('history.php', 'error', 'No events found for this user.');
}

$mpdf = new \Mpdf\Mpdf();

// Create the HTML content for the PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Event Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
    </style>
</head>
<body>
    <h1>Event Attendance Report</h1>
    <table>
        <thead>
            <tr>
                <th>Event ID</th>
                <th>Event Title</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Purchase Date</th>
                <th>Ticket Status</th>
            </tr>
        </thead>
        <tbody>';

// Populate the table with event data
foreach ($events as $event) {
    $html .= '<tr>
                  <td>' . htmlspecialchars($event['event_id']) . '</td>
                  <td>' . htmlspecialchars($event['title']) . '</td>
                  <td>' . date('Y-m-d H:i', strtotime($event['start_datetime'])) . '</td>
                  <td>' . date('Y-m-d H:i', strtotime($event['end_datetime'])) . '</td>
                  <td>' . date('Y-m-d H:i', strtotime($event['purchase_date'])) . '</td>
                  <td>' . htmlspecialchars($event['ticket_status']) . '</td>
              </tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

// Write the HTML to PDF
$mpdf->WriteHTML($html);

// Output PDF
$mpdf->Output('event_attendance_report.pdf', 'D');
