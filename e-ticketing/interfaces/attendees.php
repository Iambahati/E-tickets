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

$cards_data = $interfaces->fetchEvents();

$checkReservationsForAnEvent = $interfaces->fetchEvents();

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
  <title>Organizer Panel</title>
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
        <a href="#"><i class="bx bx-video"></i>Content</a>
      </li>
      <li>
        <a href="#"><i class="bx bx-bar-chart"></i>Analytics</a>
      </li>
      <li>
        <a href="#"><i class="bx bx-comment-detail"></i>Comments</a>
      </li>
      <li>
        <a href="#"><i class="bx bx-customize"></i>Customize</a>
      </li>
      <li>
        <a href="#"><i class="bx bx-group"></i>Users</a>
      </li>
      <li>
        <a href="#"><i class="bx bx-cog"></i>Settings</a>
      </li>
    </ul>

    <div class="side-menu">
      <ul>
        <li>
          <a href="#"><i class="bx bx-moon"></i>Dark / Light</a>
        </li>

      </ul>
    </div>
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
      <div class="header">
        <h1>Upcoming Events</h1>
        <?= $message ?>
      </div>

      <div class="search_event_card">
        <div class="search_event_items">
          <div class="search-wrapper">
            <input type="text" placeholder="Search Event by name..." />
            <i class="bx bx-search"></i>
          </div>

          <!-- Filter Dropdown -->
          <select class="filter-dropdown">
            <option value="">Filter by Category</option>
            <option value="entertainment">Entertainment</option>
            <option value="conferencing">Conferencing</option>
            <option value="movies_theatre">Movies & Theatre</option>
            <option value="sports">Sports</option>
            <option value="free_events">Free Events</option>
          </select>

          <!-- Create Event Button -->
          <button id="openModalButton" class="create-new-btn">
            <i class='bx bx-plus'></i> Create New <i class='bx bx-chevron-down'></i>
          </button>

        </div>
      </div>

      <!-- create event modal Start -->

      <div id="createEventModal" class="modal">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h2>Create Event</h2>
            <span class="close-modal" onclick="closeModal()">&times;</span>
          </div>
          <form action="assets/php/organizer_action.php" method="post" class="form">
            <!-- Modal Body -->
            <div class="modal-body">
              <!-- Organizer Dropdown -->
              <label for="organizer">Who is organizing this event? <span class="asterisk">*</span></label>
              <div class="dropdown-wrapper-modal">
                <i class="bx bx-group"></i>
                <select id="modal-organizer" name="organizer" disabled>
                  <option style="color: #363949;" value="<?= $userId ?>" selected><?= $fullname; ?></option>
                </select>
              </div>

              <br>

              <div class="form-field">
                <!-- Name Input -->
                <label for="modal-eventName">Event Name <span class="asterisk">*</span></label>
                <input type="text" id="modal-eventName" name="eventName" placeholder="Enter event name" required>
                <small></small>
              </div>

              <!-- Event Photo Picker -->
              <label for="modal-eventPhoto">Event Photo <span class="asterisk">*</span></label>
              <div class="file-picker-wrapper">
                <input type="file" id="modal-eventPhoto" name="eventPhoto" accept="image/*" onchange="previewImage(event)" required>

              </div>

              <div class="form-field">
                <!-- Description (Rich Text) -->
                <label for="modal-description">Description</label>
                <textarea id="modal-description" name="description"></textarea>
                <small></small>
              </div>

              <div class="form-field">
                <!-- Event Location -->
                <label for="modal-eventLocation">Event Location <span class="asterisk">*</span></label>
                <input type="text" name="eventLocation" placeholder="Enter event location" required>
                <small></small>
              </div>

              <div class="form-field">
                <!-- Event Type Dropdown -->
                <label for="modal-eventType">Event Type <span class="asterisk">*</span></label>
                <select id="modal-eventType" name="eventType" class="modal-event-type" style=" border: 1px solid #ccc; border-radius: 5px; font-size: 16px; transition: border-color 0.3s ease;" required>
                  <option value="">Select Event Type</option>
                  <option value="entertainment">Entertainment</option>
                  <option value="conferencing">Conferencing</option>
                  <option value="movies_theatre">Movies & Theatre</option>
                  <option value="sports">Sports</option>
                  <option value="free">Free Event</option>
                </select>
                <small></small>
              </div>

              <!-- Start and End Date -->
              <div class="date-row">
                <div class="date-field">
                  <label for="modal-startDate">Start Date <span class="asterisk">*</span></label>
                  <input type="datetime-local" id="modal-startDate" name="startDate" required>
                </div>
                <div class="date-field">
                  <label for="modal-endDate">End Date <span class="asterisk">*</span></label>
                  <input type="datetime-local" id="modal-endDate" name="endDate" required>
                </div>
              </div>

              <div class="form-field">
                <label for="modal-ticketPrice">Ticket Price (KSh) <span class="asterisk">*</span></label>
                <div class="ticket-price-control">
                  <input type="number" id="modal-ticketPrice" name="ticket_price" value="1" min="0" required>
                </div>
                <small></small>
              </div>

              <div class="form-field">
                <label for="modal-ticketCapacity">Ticket Capacity <span class="asterisk">*</span></label>
                <input type="number" id="modal-ticketCapacity" name="tickets_available" value="100" min="1" required>
                <small></small>
              </div>


              <!-- Create Event Button -->
              <button type="submit" name="create-event-btn" class="create-event-btn">Create Event</button>
            </div>
          </form>
        </div>
      </div>
      <!-- create event modal Close -->

      <!--============= bottom Data Start ===============-->
      <div class="bottom_data">
        <div class="orders">
          <div class="header">
            <h3>Recent Orders</h3>
          </div>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>103326</td>
                <td class="img_content">
                  <img src="./img/1.jpg" alt="" />
                  <p>John Doe</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status completed">Completed</span></td>
              </tr>
              <tr>
                <td>103626</td>
                <td class="img_content">
                  <img src="./img/2.jpg" alt="" />
                  <p>Jullee Smith</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td>103926</td>
                <td class="img_content">
                  <img src="./img/3.jpg" alt="" />
                  <p>Willims</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status processing">Pending</span></td>
              </tr>
              <tr>
                <td>103326</td>
                <td class="img_content">
                  <img src="./img/1.jpg" alt="" />
                  <p>John Doe</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status completed">Completed</span></td>
              </tr>
              <tr>
                <td>103626</td>
                <td class="img_content">
                  <img src="./img/2.jpg" alt="" />
                  <p>Jullee Smith</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td>103626</td>
                <td class="img_content">
                  <img src="./img/2.jpg" alt="" />
                  <p>Jullee Smith</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td>103626</td>
                <td class="img_content">
                  <img src="./img/2.jpg" alt="" />
                  <p>Jullee Smith</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td>103626</td>
                <td class="img_content">
                  <img src="./img/2.jpg" alt="" />
                  <p>Jullee Smith</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td>103626</td>
                <td class="img_content">
                  <img src="./img/2.jpg" alt="" />
                  <p>Jullee Smith</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td>103626</td>
                <td class="img_content">
                  <img src="./img/2.jpg" alt="" />
                  <p>Jullee Smith</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td>103626</td>
                <td class="img_content">
                  <img src="./img/2.jpg" alt="" />
                  <p>Jullee Smith</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td>103626</td>
                <td class="img_content">
                  <img src="./img/2.jpg" alt="" />
                  <p>Jullee Smith</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td>103626</td>
                <td class="img_content">
                  <img src="./img/2.jpg" alt="" />
                  <p>Jullee Smith</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td>103626</td>
                <td class="img_content">
                  <img src="./img/2.jpg" alt="" />
                  <p>Jullee Smith</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td>103626</td>
                <td class="img_content">
                  <img src="./img/2.jpg" alt="" />
                  <p>Jullee Smith</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td>103626</td>
                <td class="img_content">
                  <img src="./img/2.jpg" alt="" />
                  <p>Jullee Smith</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td>103626</td>
                <td class="img_content">
                  <img src="./img/2.jpg" alt="" />
                  <p>Jullee Smith</p>
                </td>
                <td>admin@onlineittuts.com</td>
                <td>6th Sep 2025</td>
                <td><span class="status pending">Pending</span></td>
              </tr>

            </tbody>
          </table>
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