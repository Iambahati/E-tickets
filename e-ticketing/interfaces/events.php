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
    <title>Events </title>
    <link rel="stylesheet" href='../assets/css/dash.css'>
    <link rel="stylesheet" href='../assets/css/card.css'>
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
                <a href="events.php"><i class='bx bxs-home'></i></i>Home</a>
            </li>


            <li>
                <a href="history.php" class=""><i class='bx bx-history'></i>My History</a>
            </li>

            <!-- <li>
                <a href="profile.php" class=""><i class='bx bx-user'></i>Profile</a>
            </li> -->

            <li>
                <a href="../assets/php/logout.php" class="logout"><i class="bx bx-log-out-circle"></i>Logout</a>
            </li>
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
                    <!-- Search Wrapper -->
                    <div class="search-wrapper">
                        <input type="text" placeholder="Search Event by name..." />
                        <i class="bx bx-search"></i>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="date-picker-container">
                        <input type="date" id="start-date" placeholder="Start Date" />
                        <i class="bx bx-right-arrow-alt arrow-icon"></i>
                        <input type="date" id="end-date" placeholder="End Date" />
                        <i class="bx bx-x-circle cancel-icon" id="clear-dates"></i>
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

                    <!-- Sort By Dropdown -->
                    <select class="filter-dropdown">
                        <option value="">Sort By</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="this_weekend">This Weekend</option>
                        <option value="this_week">This Week</option>
                        <option value="next_week">Next Week</option>
                        <option value="this_month">This Month</option>
                        <option value="next_month">Next Month</option>
                    </select>

                    <!-- Filter Button -->
                    <button class="create-new-btn">
                        <i class='bx bx-filter'></i>
                        Filter
                    </button>
                </div>
            </div>
            <?php if ($events_data) : ?>

                <div class="event-grid">
                    <?php foreach ($events_data as $event) : ?>
                        <div class="event-card" data-id="<?= $event['event_id'] ?>">
                            <img class="event-poster" src="../assets/images/uploads/<?= basename($event['event_photo']) ?>" alt="<?= htmlspecialchars($event['title']) ?> Poster">
                            <div class="date-container">
                                <span class="event-day"><?= date('D', strtotime($event['start_datetime'])) ?></span>
                                <span class="event-date"><?= date('d', strtotime($event['start_datetime'])) ?></span>
                                <span class="event-month"><?= date('M', strtotime($event['start_datetime'])) ?></span>
                            </div>
                            <div class="details">
                                <h3><?= htmlspecialchars($event['title']) ?></h3>
                                <div class="icon-text">
                                    <i class='bx bx-calendar'></i>
                                    <div>
                                        <span><?= date('D, M d, Y', strtotime($event['start_datetime'])) ?> - <?= date('D, M d, Y', strtotime($event['end_datetime'])) ?></span>
                                        <br>
                                        <span><?= date('h:i A', strtotime($event['start_datetime'])) ?> - <?= date('h:i A', strtotime($event['end_datetime'])) ?></span>
                                    </div>
                                </div>

                                <div class="icon-text">
                                    <i class='bx bx-map'></i>
                                    <span><?= htmlspecialchars($event['location_details']) ?></span>
                                </div>

                                <a href="event.php?event_id=<?= $event['event_id'] ?>&?event_name=<?= $event['title'] ?>" class="view-event" target="_blank"></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>


            <?php else : ?>
                <h3 style="margin-left: 20%;">No events at the moment!</h3>

            <?php endif; ?>




            <!--============= bottom Data Start ===============-->




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


    <script>
        /**
         * Date Picker Functionality
         * This function initializes event listeners for the start and end date input fields,
         * shows the clear icon when both fields have values, and clears the fields when the
         * clear icon is clicked.
         */
        function initializeDatePicker() {
            const startDateInput = document.getElementById('start-date');
            const endDateInput = document.getElementById('end-date');
            const clearDatesIcon = document.getElementById('clear-dates');

            // Show clear icon when both fields are filled
            function checkForFilledFields() {
                if (startDateInput.value && endDateInput.value) {
                    clearDatesIcon.style.display = 'block';
                } else {
                    clearDatesIcon.style.display = 'none';
                }
            }

            // Add event listeners for both date fields
            startDateInput.addEventListener('change', checkForFilledFields);
            endDateInput.addEventListener('change', checkForFilledFields);

            // Clear the date fields when the cancel icon is clicked
            clearDatesIcon.addEventListener('click', () => {
                startDateInput.value = '';
                endDateInput.value = '';
                clearDatesIcon.style.display = 'none'; // Hide the cancel icon
            });
        }

        // Initialize the date picker functionality when the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', initializeDatePicker);
    </script>



    <script src="../assets/js/main.js"></script>

</body>

</html>