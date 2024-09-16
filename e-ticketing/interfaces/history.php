<?php

require_once '../utils.php';

require_once  '../assets/php/client_functions.php';

if (!isset($_SESSION['client'])) {
  Utils::redirect_to('../history.php');
}

$currentlyLoggedInUser = $_SESSION['client'];

$interfaces = new Client();

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Get page via GET request (URL param: page), if non exists default the page to 1
$reservation_data = $interfaces->reservationsPerPage($page);

$reservationsCount = $interfaces->totalCount('reservations');

// coalescing operator `??`
// checks if a variable exists and is not null,
// and if it doesn't, it returns a default value
$message = $_SESSION['success'] ?? $_SESSION['error'] ?? null;
// `unset()` function destroys a variable. Once a variable is unset, it's no longer accessible
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['contactUsMessage']);

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="x-UTF-16LE-BOM">
  <meta name="viewport" content="width=device=width, initial-scale=1.0">
  <title>Booking History</title>
  <link href="https://fonts.googleapis.com/css?family=Bad+Script|Comfortaa|Amiri|Cormorant+Garamond|Rancho|Fredericka+the+Great|Handlee|Homemade+Apple|Philosopher|Playfair+Display+SC|Reenie+Beanie|Unna|Zilla+Slab" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/main.css">
  <link rel="stylesheet" href="../assets/css/modals.css">
  <link rel="stylesheet" href="../assets/css/cards.css">
  <link rel="stylesheet" href="../assets/css/tables.css">
  <link rel="stylesheet" href="../assets/css/footer.css">
</head>

<body>

  <div>
    <div class="navbar">
      <h1 class="logo" style="font-size: 40px;">TickeTok </h1>
      <ul style="font-size: 15px;">
        <li><a href="#">Hi, <?= $currentlyLoggedInUser ?></li>
        <li><a href="events.php">View events</a></li>
        <li><a href="../assets/php/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
  <br><br>
  <div>


  <br><br>
  <?php if (!$reservation_data) : ?>
    <p class="box1">No history data at the moment!</p>
  <?php else : ?>
    <div class="wrapper">
      <p class="box1">My history</p>
      <table>
        <thead>
          <tr>
            <th>Id</th>
            <th>Event Name</th>
            <th>Event Date</th>
            <th>Event Status</th>
            <th>Number of Tickets</th>
            <th>Total Payment</th>
          </tr>
        </thead>
        <?php foreach ($reservation_data as $reservation) : ?>
          <tr>
            <!-- < ?= ?> is a shorthand syntax for the < ? php echo ?> statement -->
            <td><?= $reservation['id'] ?></td>
            <td><?= $interfaces->getEventNameFromId($reservation['events_id']) ?></td>
            <td><?= ($eventDate = $interfaces->fetchEventDateById($reservation['events_id'])['date']) ? date('D, M d, Y', strtotime($eventDate)) : (($fromDate = $interfaces->fetchEventDateById($reservation['events_id'])['from_date']) && ($toDate = $interfaces->fetchEventDateById($reservation['events_id'])['to_date']) ? (date('D, M d, Y', strtotime($fromDate)) . ' - ' . date('D, M d, Y', strtotime($toDate))) : 'N/A') ?></td>
            <td>
              <?= ($interfaces->fetchEventDateById($reservation['events_id'])['hasPassed']) ? '<p class="not-active"><strong>Passed</strong></p>' : '<p class="active"><strong>Due</strong></p>' ?>
            </td>
            <td><?= $reservation['number_of_tickets'] ?></td>
            <td><?= $reservation['total_amount'] ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
      <div class="pagination">
        <?php if ($page > 1) : ?>
          <a href="history.php?page=<?= $page - 1 ?>">
            << Previous</a>
            <?php endif; ?>
            <?php if ($reservationsCount > ($page * 5)) : ?>
              <a href="history.php?page=<?= $page + 1 ?>">Next >></a>
            <?php endif; ?>
      </div>

      <?= str_repeat('<br>', 4); ?>

    </div>
  <?php endif; ?>

  <!--Footer-->
  <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="footer-col">
          <h4>company</h4>
          <ul>
            <li><a href="../assets/history.php">About Us</a></li>
            <li><a href="../assets/history.php">Our Services</a></li>
            <li><a href="#">Privacy Policy</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>get help</h4>
          <ul>
            <li><a href="#">FAQ</a></li>
            <li><a href="#">Refunds</a></li>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Payment Options</a></li>
          </ul>
        </div>

      </div>
    </div>
  </footer>
</body>

</html>