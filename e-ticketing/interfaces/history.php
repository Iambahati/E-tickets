<?php

require_once  '../assets/php/client_functions.php';

require_once '../utils.php';


if (!isset($_SESSION['clientName']) && !isset($_SESSION['accRole'])) {

  Utils::redirect_to('../index.php');
}

$fullname = $_SESSION['clientName'];

$userId = $_SESSION['userId'];


// coalescing operator `??`
// checks if a variable exists and is not null,
// and if it doesn't, it returns a default value
$message = $_SESSION['success'] ?? $_SESSION['error'] ?? null;
// `unset()` function destroys a variable. Once a variable is unset, it's no longer accessible
unset($_SESSION['success'], $_SESSION['error']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>History </title>
  <link rel="stylesheet" href='../assets/css/dash.css'>
  <link rel="stylesheet" href='../assets/css/event.css'>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>



</head>

<body>
  <!-- =============Sidebar Start================ -->
  <div class="sidebar">
    <a href="events.php" class="logo">
      <img src="./img/logo-1.png" alt="" />
      <span>E-</span>Ticketing
    </a>
    <ul class="side-menu">
      <li class="active">
        <a href="events.php"><i class='bx bx-left-arrow-alt'></i>Events</a>
      </li>

      <li>
        <a href="../assets/php/logout.php" class="logout"><i class="bx bx-log-out-circle"></i>Logout</a>
      </li>
  </div>
  <!-- =============Sidebar Close================ -->
  <div class="content">
    <nav>
      <i class="bx bx-menu"></i>
      <form action="#">
        <div class="form-input">
          <input type="search" placeholder="Search................" />
          <button type="submit"><i class="bx bx-search"></i></button>
        </div>
      </form>

      <div class="profile-details">
        <img src="../assets/images/avatar.png" alt="" class="rounded-circle">
        <span class="full_name"><?= $fullname; ?></span>

      </div>
    </nav>


    <!-- Main Start -->
    <main>

      <div class="bottom_data">
        <div class="container">
          <h1>Download History Report</h1>
          <?= $message ?>
          <form action="export.php" method="post" class="form">
            <?= Utils::insertCsrfToken() ?>

            <button class="button-buy-ticket">Download Report</button>
        </div>
      </div>





    </main>
    <!-- Main Close -->
  </div>


  <script>
    const clearDates = document.getElementById('clear-dates');
    const startDate = document.getElementById('start-date');
    const endDate = document.getElementById('end-date');

    clearDates.addEventListener('click', () => {
      startDate.value = '';
      endDate.value = '';
    });
  </script>



  <script src="../assets/js/main.js"></script>

</body>

</html>