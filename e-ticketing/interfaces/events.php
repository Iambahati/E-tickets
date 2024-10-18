<?php

require_once  '../assets/php/client_functions.php';

require_once '../utils.php';


if (!isset($_SESSION['clientName']) && !isset($_SESSION['accRole'])) {

    Utils::redirect_to('../index.php');
}

$fullname = $_SESSION['clientName'];

$userId = $_SESSION['userId'];


$interfaces = new Client();

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
    <!-- <link rel="stylesheet" href='../assets/css/card.css'> -->
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
                <a href="../assets/php/logout.php" class="logout"><i class="bx bx-log-out-circle"></i>Logout</a>
            </li>
            <!-- <div class="side-menu">
      <ul>
        <li>
          <a href="#"><i class="bx bx-moon"></i>Dark / Light</a>
        </li>

      </ul>
    </div> -->
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

                </div>
            </div>


            
            <!--============= bottom Data Start ===============-->
            <div class="bottom_data">
                <div class="orders">
                    <?php if ($events_data) : ?>
                      
                                <?php foreach ($events_data as $event) : ?>
                                    <tr data-id="<?= $event['event_id'] ?>">
                                        <td class="img-content">
                                            <img src="../assets/images/uploads/<?= basename($event['event_photo']) ?>" alt="Event Photo" />
                                        </td>
                                        <td><?= $event['title'] ?></td>
                                        <td><?= date('d M Y, H:i A', strtotime($event['start_datetime'])) ?></td>
                                        <td><?= date('d M Y, H:i A', strtotime($event['end_datetime'])) ?></td>
                                        <td> <?= $event['ticket_price'] ?> </td>
                                        <td> <?= $event['ticket_quantity_available'] ?></td>
                                        <td>
                                            <p class="status <?= strtolower($event['event_status']); ?>">
                                                <strong><?= ucfirst(strtolower($event['event_status'])); ?></strong>
                                            </p>
                                        </td>

                                        <td>
                                            <div class="actions">
                                                <!-- <a href="#" id=""class="edit">View</a> -->

                                                <a href="#" class="edit" id='openEditModalButton' data-id="<?= $event['event_id'] ?>" data-title="<?= $event['title'] ?>" data-description="<?= $event['description'] ?>" data-location="<?= $event['location_details'] ?>" data-type="<?= $event['event_type'] ?>" data-start="<?= $event['start_datetime'] ?>" data-end="<?= $event['end_datetime'] ?>" data-price="<?= $event['ticket_price'] ?>" data-capacity="<?= $event['ticket_quantity_available'] ?>" data-poster="<?= $event['event_photo'] ?>">
                                                    Buy Ticket
                                                </a>

                                             
                                            </div>

                                    </tr>
                                <?php endforeach; ?>


                            </tbody>
                        </table>
                </div>

            <?php else : ?>
                <h3 style="margin-left: 20%;">No events at the moment!</h3>

            <?php endif; ?>

            </div>
            <!--============= bottom Data End ===============-->

            <!-- create event modal Start -->

            <div id="buyTicketModal" class="modal">
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



        </main>
        <!-- Main Close -->
    </div>
    <!-- =============Content CLose================ -->


    <script>
        /**
         * Toggle the modal (open/close)
         * @param {string} modalId - The ID of the modal to toggle.
         * @param {string} action - 'open' or 'close' to show or hide the modal.
         */
        function toggleModal(modalId, action) {
            const modal = document.getElementById(modalId);
            modal.style.display = action === 'open' ? 'block' : 'none';
        }

        /**
         * Close the edit modal.
         */
        function closeEditModal() {
            toggleModal('editEventModal', 'close');
        }

        /**
         * Open the edit modal and populate form fields with data.
         * @param {Event} event - The event object triggered by the button click.
         */
        function openEditModal(event) {
            event.preventDefault();
            const button = event.currentTarget;

            // Populate form fields with data from button's dataset
            document.getElementById('edit-event-id').value = button.dataset.id;
            document.getElementById('edit-eventName').value = button.dataset.title;
            document.getElementById('edit-description').value = button.dataset.description;
            document.getElementById('edit-eventLocation').value = button.dataset.location;
            document.getElementById('edit-startDate').value = button.dataset.start;
            document.getElementById('edit-endDate').value = button.dataset.end;
            document.getElementById('edit-ticketPrice').value = button.dataset.price;
            document.getElementById('edit-ticketCapacity').value = button.dataset.capacity;

            // Handle event type selection
            const eventTypeSelect = document.getElementById('edit-eventType');
            const eventType = button.dataset.type.toLowerCase();
            console.log('Setting event type to:', eventType);

            // Find and select the matching option
            const matchingOption = Array.from(eventTypeSelect.options).find(option =>
                option.value.toLowerCase() === eventType ||
                option.textContent.toLowerCase().includes(eventType)
            );

            if (matchingOption) {
                matchingOption.selected = true;
            } else {
                console.warn('No matching event type found for:', eventType);
                eventTypeSelect.selectedIndex = 0; // Select the default option
            }

            // Open the edit modal
            toggleModal('editEventModal', 'open');
        }


        /**
         * Add event listeners for modals and buttons once the DOM is loaded.
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Close modal when the close button ('X') is clicked
            document.querySelectorAll('.close-modal').forEach(closeButton => {
                closeButton.addEventListener('click', function() {
                    const modalId = this.closest('.modal').id;
                    toggleModal(modalId, 'close');
                });
            });

            // Prevent closing the modal when clicking inside its content area
            document.querySelectorAll('.modal-content').forEach(content => {
                content.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            });


            // Open create event modal
            document.getElementById('openModalButton').addEventListener('click', function() {
                toggleModal('buyTicketModal', 'open');
            });


        });


const eventGrid = document.getElementById('eventGrid');
eventsData.forEach(event => {
    eventGrid.appendChild(createEventCard(event));
});
    </script>



    <script src="../assets/js/main.js"></script>

</body>

</html>