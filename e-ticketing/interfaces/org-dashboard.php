<?php

require_once  '../assets/php/organizer_functions.php';

require_once '../utils.php';


if (!isset($_SESSION['clientName']) && !isset($_SESSION['accRole'])) {

  Utils::redirect_to('../index.php');
}

$fullname = $_SESSION['clientName'];

$userId = $_SESSION['userId'];


$interfaces = new Organizer();

$events_data = $interfaces->fetchEvents($userId);

// $checkReservationsForAnEvent = $interfaces->fetchEvents();

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
    <a href="org-dashboard.php" class="logo">
      <img src="./img/logo-1.png" alt="" />
      <span>E-</span>Ticketing
    </a>
    <ul class="side-menu">
      <li class="active">
        <a href="org-dashboard.php"><i class="bx bxs-dashboard"></i>Dashboard</a>
      </li>

      <li>
      <a href="profile.php" class=""><i class='bx bx-user'></i>Profile</a>
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
      <div class="header">
        <!-- <h1>Upcoming Events</h1> -->
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





      <!--============= bottom Data Start ===============-->
      <div class="bottom_data">
        <div class="orders">
          <?php if ($events_data) : ?>
            <div class="header">
              <h3>My Events</h3>
            </div>
            <table class="events-table">
              <thead>
                <tr>
                  <th>Thumbnail</th>
                  <th>Event Title</th>
                  <th>Event Duration</th>
                  <th>Ticket Price (KSh)</th>
                  <th>Available Tickets</th>
                  <th>Status</th>
                  <th>Attendees</th>
                  <th>Sales</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($events_data as $event) : ?>
                  <tr data-id="<?= $event['event_id'] ?>">
                    <td class="img-content">
                      <img src="../assets/images/uploads/<?= basename($event['event_photo']) ?>" alt="Event Photo" />
                    </td>
                    <td><?= $event['title'] ?></td>
                    <td><?= date('D, M d, Y', strtotime($event['start_datetime'])) ?> - <?= date('D, M d, Y', strtotime($event['end_datetime'])) ?></td>
                    <td> <?= $event['ticket_price'] ?> </td>
                    <td> <?= $event['ticket_quantity_available'] ?></td>
                    <td>
                      <p class="status <?= strtolower($event['event_status']); ?>">
                        <strong><?= ucfirst(strtolower($event['event_status'])); ?></strong>
                      </p>
                    </td>

                    <td>
                      <a href="event-attendees.php?event_id=<?= $event['event_id'] ?>" target="_blank" style="display: flex; align-items: center; gap: 5px; color: #388e3c;"><i class='bx bx-group' style="text-decoration: none; font-size: 20px;"></i><span style="text-decoration: underline;">View</span></a>
                    </td>
                    <td>
                      <a href="event-sales.php?event_id=<?= $event['event_id'] ?>" target="_blank" style="display: flex; align-items: center; gap: 5px; color: #388e3c;"><i class='bx bx-dollar' style="text-decoration: none; font-size: 20px;"></i><span style="text-decoration: underline;">View</span></a>
                    </td>
                    <td>
                      <div class="actions">

                        <a href="#" class="edit" id='openEditModalButton' data-id="<?= $event['event_id'] ?>" data-title="<?= $event['title'] ?>" data-description="<?= $event['description'] ?>" data-location="<?= $event['location_details'] ?>" data-type="<?= $event['event_type'] ?>" data-start="<?= $event['start_datetime'] ?>" data-end="<?= $event['end_datetime'] ?>" data-price="<?= $event['ticket_price'] ?>" data-capacity="<?= $event['ticket_quantity_available'] ?>" data-poster="<?= $event['event_photo'] ?>">
                          Edit
                        </a>

                        <a href="#" class="delete" id='openDeleteModalButton' data-id="<?= $event['event_id'] ?>" data-title="<?= $event['title'] ?>">Delete</a>
                      </div>

                  </tr>
                <?php endforeach; ?>


              </tbody>
            </table>
        </div>

      <?php else : ?>
        <h3 style="margin-left: 20%;">No events created yet!</h3>

      <?php endif; ?>

      </div>
      <!--============= bottom Data End ===============-->

      <!-- create event modal Start -->

      <div id="createEventModal" class="modal">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h2>Create Event</h2>
            <span class="close-modal" onclick="closeModal()">&times;</span>
          </div>
          <form action="../assets/php/organizer_action.php" method="post" enctype="multipart/form-data" class="form">
            <?= Utils::insertCsrfToken() ?>
            <!-- Modal Body -->
            <div class="modal-body">
              <!-- Organizer Dropdown -->
              <label for="organizer">Who is organizing this event? <span class="asterisk">*</span></label>
              <input hidden name="organizer" value="<?= $userId ?>">
              <div class="dropdown-wrapper-modal">
                <i class="bx bx-group"></i>
                <select id="modal-organizer" name="organizer" disabled>
                  <option style="color: #363949;" selected><?= $fullname; ?></option>
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
                <input type="file" id="modal-eventPhoto" name="eventPhoto" accept="image/*" required>

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

      <!-- Edit Event Modal Start -->
      <div id="editEventModal" class="modal" style="display: none;">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h2>Edit Event</h2>
            <span class="close-modal" onclick="closeEditModal()">&times;</span>
          </div>
          <form action="../assets/php/organizer_action.php" method="post" enctype="multipart/form-data" class="form">
            <?= Utils::insertCsrfToken() ?>
            <!-- Modal Body -->
            <div class="modal-body">
              <input type="hidden" name="eventId" id="edit-event-id" name="event_id">

              <!-- Organizer Dropdown -->
              <label for="edit-organizer">Who is organizing this event? <span class="asterisk">*</span></label>
              <input hidden name="organizer" value="<?= $userId ?>">
              <div class="dropdown-wrapper-modal">
                <i class="bx bx-group"></i>
                <select id="edit-organizer" name="organizer" disabled>
                  <option style="color: #363949;" selected><?= $fullname; ?></option>
                </select>
              </div>

              <br>

              <div class="form-field">
                <!-- Name Input -->
                <label for="edit-eventName">Event Name <span class="asterisk">*</span></label>
                <input type="text" id="edit-eventName" name="eventName" placeholder="Enter event name" required>
                <small></small>
              </div>

              <!-- <label for="edit-eventPhoto">Event Photo <span class="asterisk">*</span></label>
              <div class="file-picker-wrapper">
                <input type="file" id="edit-eventPhoto" name="eventPhoto" accept="image/*">
              </div> -->

              <div class="form-field">
                <!-- Description (Rich Text) -->
                <label for="edit-description">Description</label>
                <textarea id="edit-description" name="description"></textarea>
                <small></small>
              </div>

              <div class="form-field">
                <!-- Event Location -->
                <label for="edit-eventLocation">Event Location <span class="asterisk">*</span></label>
                <input type="text" id="edit-eventLocation" name="eventLocation" placeholder="Enter event location" required>
                <small></small>
              </div>

              <div class="form-field">
                <!-- Event Type Dropdown -->
                <label for="edit-eventType">Event Type <span class="asterisk">*</span></label>
                <select id="edit-eventType" name="eventType" required>
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
                  <label for="edit-startDate">Start Date <span class="asterisk">*</span></label>
                  <input type="datetime-local" id="edit-startDate" name="startDate" required>
                </div>
                <div class="date-field">
                  <label for="edit-endDate">End Date <span class="asterisk">*</span></label>
                  <input type="datetime-local" id="edit-endDate" name="endDate" required>
                </div>
              </div>

              <div class="form-field">
                <label for="edit-ticketPrice">Ticket Price (KSh) <span class="asterisk">*</span></label>
                <input type="number" id="edit-ticketPrice" name="ticket_price" min="0" required>
                <small></small>
              </div>

              <div class="form-field">
                <label for="edit-ticketCapacity">Ticket Capacity <span class="asterisk">*</span></label>
                <input type="number" id="edit-ticketCapacity" name="tickets_available" min="1" required>
                <small></small>
              </div>

              <!-- Update Event Button -->
              <button type="submit" name="edit-event-btn" class="edit-event-btn">Update Event</button>
            </div>
          </form>
        </div>
      </div>
      <!-- Edit Event Modal Close -->

      <!-- Delete Event Modal Start -->
      <div id="deleteEventModal" class="modal" style="display: none;">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h2 id='del-event'>Delete Event</h2>
            <span class="close-modal" onclick="closeDeleteModal()">&times;</span>
          </div>
          <form action="../assets/php/organizer_action.php" method="post" class="form">
            <?= Utils::insertCsrfToken() ?>
            <input type="hidden" name="eventId" id="delete-event-id" name="event_id">
            <!-- Delete Event Button -->
            <button type="submit" name="delete-event-btn" class="delete-event-btn">Delete Event</button>
        </div>
        </form>
      </div>
  </div>
  <!-- Edit Event Modal Close -->

  </main>
  <!-- Main Close -->
  </div>
  <!-- =============Content CLose================ -->

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Set up event listeners
      setupModalListeners();
      setupEditModal(); // Init edit modal setup
    });

    // Function to toggle the modal (open/close)
    function toggleModal(modalId, action) {
      const modal = document.getElementById(modalId);
      modal.style.display = action === 'open' ? 'block' : 'none';
    }

    // Function to set up modal event listeners
    function setupModalListeners() {
      // Attach event listener to close the modal when the 'X' is clicked
      document.querySelectorAll('.close-modal').forEach(closeButton => {
        closeButton.addEventListener('click', function() {
          const modalId = this.closest('.modal').id;
          toggleModal(modalId, 'close');
        });
      });

      // Prevent closing the modal when clicking outside of it
      window.onclick = function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
          if (event.target == modal) {
            toggleModal(modal.id, 'close');
          }
        });
      };

      // Attach event listener to open the create event modal
      document.getElementById('openModalButton').addEventListener('click', function() {
        toggleModal('createEventModal', 'open');
      });
    }

    // Function to set up the edit modal
    function setupEditModal() {
      const openEditModalButtons = document.querySelectorAll('.edit');

      openEditModalButtons.forEach(button => {
        button.addEventListener('click', function(event) {
          event.preventDefault();
          openEditModal(this); // Pass the current btn to the openEditModal function
        });
      });
    }

    // Function to open the edit modal and populate fields
    function openEditModal(button) {
      // Get data attributes from the button element
      const id = button.getAttribute('data-id');
      const title = button.getAttribute('data-title');
      const description = button.getAttribute('data-description');
      const location = button.getAttribute('data-location');
      const type = button.getAttribute('data-type');
      const start = button.getAttribute('data-start');
      const end = button.getAttribute('data-end');
      const price = button.getAttribute('data-price');
      const capacity = button.getAttribute('data-capacity');

      // Populate the modal fields
      document.getElementById('edit-event-id').value = id;
      document.getElementById('edit-eventName').value = title;
      document.getElementById('edit-description').value = description;
      document.getElementById('edit-eventLocation').value = location;
      document.getElementById('edit-eventType').value = type;
      document.getElementById('edit-startDate').value = start;
      document.getElementById('edit-endDate').value = end;
      document.getElementById('edit-ticketPrice').value = price;
      document.getElementById('edit-ticketCapacity').value = capacity;

      // Open the modal
      toggleModal('editEventModal', 'open');
    }

    // Function to close the modal
    function closeEditModal() {
      toggleModal('editEventModal', 'close');
    }

    // Function to open the delete modal and populate data
    function openDeleteModal(event) {
      event.preventDefault();
      const button = event.currentTarget;
      const modal = document.getElementById('deleteEventModal');

      // Populate form fields
      document.getElementById('delete-event-id').value = button.dataset.id;
      document.getElementById('del-event').textContent = `Delete "${button.dataset.title}"`;

      toggleModal('deleteEventModal', 'open');
    }

    // Event listeners for delete modal
    document.addEventListener('DOMContentLoaded', function() {
      // Open create event modal
      document.getElementById('openModalButton').addEventListener('click', function() {
        toggleModal('createEventModal', 'open');
      });

      // Open edit event modal (for multiple events)
      document.querySelectorAll('.edit').forEach(button => {
        button.addEventListener('click', function(event) {
          event.preventDefault();
          openEditModal(this);
        });
      });

      // Open delete event modal (for multiple events)
      document.querySelectorAll('.delete').forEach(button => {
        button.addEventListener('click', function(event) {
          event.preventDefault();
          openDeleteModal(event);
        });
      });
    });
  </script>


  <script src="../assets/js/main.js"></script>

</body>

</html>