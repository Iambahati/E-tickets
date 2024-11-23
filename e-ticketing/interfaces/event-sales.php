<?php

require_once  '../assets/php/organizer_functions.php';

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

$interfaces = new Organizer();

$eventId = $_GET['event_id'] ?? null;

$sales_data = $interfaces->getEventSales($eventId);

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
  <title>Attendees</title>
  <link rel="stylesheet" href='../assets/css/dash.css'>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>



</head>

<body>
  <!-- =============Sidebar Start================ -->
  <div class="sidebar">
    <a href="#" class="logo">
      <img src="./img/logo-1.png" alt="" />
      <span>E-</span>Ticketing
    </a>
    <ul class="side-menu">
      <li class="active">
        <a href="org-dashboard.php"><i class="bx bxs-dashboard"></i>Dashboard</a>
      </li>

      <li>
        <a href="../assets/php/logout.php" class="logout"><i class="bx bx-log-out-circle"></i>Logout</a>
      </li>
    </ul>
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
        <i class='fa-solid fa-caret-down'></i>
        <div class="dropdown-menu" id="dropdown-menu">
          <a class="dropdown-item" href="#">
            <i class='bx bxs-user bx-flip-horizontal'></i>
            <span class="links_name">Profile</span>
          </a>
          <a class="dropdown-item logout" href="../assets/php/logout.php">
            <i class="bx bx-log-out-circle"></i>
            <span class="links_name">Logout</span>
          </a>
        </div>
      </div>
    </nav>


    <!-- Main Start -->
    <main>
      <!--============= bottom Data Start ===============-->
      <div class="bottom_data">
        <div class="orders">
          <div class="header">
            <h3>Event Sales</h3>
          </div>
          <?php if ($sales_data) : ?>
            <table class="events-table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Tickets Bought</th>
                  <th>Total Amount</th>
                  <th>Order Date</th>
                  <!-- <th>Actions</th> -->
                </tr>
              </thead>
              <tbody>
                <?php foreach ($sales_data as $sale) : ?>
                  <tr>
                    <td><?= $sale['first_name'] . ' ' . $sale['last_name'] ?></td>
                    <td><?= $sale['email'] ?></td>
                    <td><?= $sale['total_tickets'] ?></td>
                    <td>Ksh. <?= number_format($sale['total_amount'], 2) ?></td>
                    <td><?= date('d M, Y \a\t h:i:s A', strtotime($sale['order_date'])) ?></td>
                    <!-- <td>
                      <a href="mailto:<?= $sale['email'] ?>" style="display: flex; align-items: center; gap: 5px; color: #388e3c;">
                        <i class='bx bx-envelope' style="text-decoration: none; font-size: 20px;"></i>
                        <span style="text-decoration: underline;">Email</span>
                      </a>
                    </td> -->
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else : ?>
            <h3 style="margin-left: 20%;">No sales found for this event!</h3>
          <?php endif; ?>
        </div>

      </div>
      <!--============= bottom Data Start ===============-->
    </main>
    <!-- Main Close -->
  </div>
  <!-- =============Content CLose================ -->

  <script>
    // Open modal
    function openModal() {
      document.getElementById('createEventModal').style.display = 'block';
    }

    // Close modal when the 'X' is clicked
    function closeModal() {
      document.getElementById('createEventModal').style.display = 'none';
    }

    // Prevent closing when clicking outside modal content
    window.onclick = function(event) {
      const modal = document.getElementById('createEventModal');
      if (event.target == modal) {
        event.stopPropagation();
      }
    }




    // Sample event submit function
    function submitEvent() {
      alert('Event created!');
      closeModal();
    }


    // Function to open the modal
    function openModal() {
      const modal = document.getElementById('createEventModal');
      modal.style.display = 'block';
    }

    // Function to close the modal
    function closeModal() {
      const modal = document.getElementById('createEventModal');
      modal.style.display = 'none';
    }

    // Prevent modal from closing when clicking outside the modal content
    window.onclick = function(event) {
      const modal = document.getElementById('createEventModal');
      if (event.target == modal) {
        event.stopPropagation(); // Prevent closing when clicking outside
      }
    }

    // Attach the event listener to a button that opens the modal
    document.getElementById('openModalButton').addEventListener('click', openModal);

    // Attach the event listener to the close button (the 'X')
    document.querySelector('.close-modal').addEventListener('click', closeModal);


    // Preview the image when selected
    function previewImage(event) {
      const preview = document.getElementById('preview-img');
      const photoPreview = document.getElementById('photo-preview');
      const file = event.target.files[0];

      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
          photoPreview.classList.add('active');
        };
        reader.readAsDataURL(file);
      }
    }

    // Remove the image from preview
    function removeImage() {
      const preview = document.getElementById('preview-img');
      const fileInput = document.getElementById('modal-eventPhoto');
      const photoPreview = document.getElementById('photo-preview');

      preview.src = '';
      preview.style.display = 'none';
      fileInput.value = '';
      photoPreview.classList.remove('active');
    }
  </script>


  <script src="../assets/js/main.js"></script>

</body>

</html>