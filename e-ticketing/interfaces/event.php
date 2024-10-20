<?php

require_once  '../assets/php/client_functions.php';

require_once '../utils.php';


if (!isset($_SESSION['clientName']) && !isset($_SESSION['accRole'])) {

    Utils::redirect_to('../index.php');
}

$fullname = $_SESSION['clientName'];

$userId = $_SESSION['userId'];

$eventId = $_GET['event_id'] ?? null;

$eventName = $_GET['event_name'] ?? null;

$interfaces = new Client();

$event = $interfaces->fetchEventById($eventId);

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
    <title> <?= $eventName ?> </title>
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
                <a href="history.php" class=""><i class='bx bx-history'></i>My History</a>
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

            <?php if ($event) : ?>
                <div class="event-container">
                    <!-- Left Column: Poster and Description -->
                    <div class="left-column">
                        <img src="../assets/images/uploads/<?= basename($event['event_photo']) ?>" alt="<?= htmlspecialchars($event['title']) ?> Poster">
                        <div class="event-description">
                            <h2>About</h2>
                            <p id="description-text" class="description">
                                <?= nl2br(htmlspecialchars($event['description'])) ?>
                            </p>
                            <a href="#" id="toggle-description" class="toggle-link">Read More</a>
                        </div>


                    </div>

                    <!-- Right Column: Event Details -->
                    <div class="right-column">
                        <h1><?= htmlspecialchars($event['title']) ?></h1>

                        <div class="date-card">
                            <span><?= date('D', strtotime($event['start_datetime'])) ?></span>
                            <span><?= date('d', strtotime($event['start_datetime'])) ?></span>
                            <span><?= date('M', strtotime($event['start_datetime'])) ?></span>
                        </div>

                        <div class="event-details">
                            <div>
                                <i class='bx bx-map'></i>
                                <span><?= htmlspecialchars($event['location_details']) ?></span>
                            </div>
                            <div>
                                <i class='bx bx-calendar'></i>
                                <span>
                                    <?= date('D, M d, Y', strtotime($event['start_datetime'])) ?> - <?= date('D, M d, Y', strtotime($event['end_datetime'])) ?>
                                </span>
                            </div>
                            <div>
                                <i class='bx bx-time'></i>
                                <span>
                                    <?= date('h:i A', strtotime($event['start_datetime'])) ?> - <?= date('h:i A', strtotime($event['end_datetime'])) ?>
                                </span>
                            </div>
                        </div>

                        <button class="button-buy-ticket" onclick="openModal(this)" data-event-id="<?= $event['event_id'] ?>" data-price="<?= $event['ticket_price'] ?>">Buy Ticket</button>

                    </div>
                </div>

            <?php else: ?>
                <div class="not-found-container">
                    <h1>Event Not Found</h1>
                    <p>Sorry, the event you're looking for doesn't exist or has been removed.</p>
                    <a href="events.php">Back to Events</a>
                </div>
            <?php endif; ?>


            <div id="ticketModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <h2>Select Tickets</h2>
                    <form action="../assets/php/action.php" method="post">
                        <?= Utils::insertCsrfToken() ?>
                        <input type="hidden" name="eventId" id="event-ticket-id">
                        <div class="ticket-type">
                            <span class="ticket-name">Ticket</span>
                            <div class="counter">
                                <button type="button" onclick="updateCount(-1)">-</button>
                                <input readonly type="number" id="ticketCount" value="1" min="1" name="number_of_tickets" onchange="updateTotal()">
                                <button type="button" onclick="updateCount(1)">+</button>
                            </div>
                            <div id="error-message" style="display: none; color: red; margin-top: 10px;"></div>
                            <span class="ticket-price">Ksh. <span id="ticketPrice"></span></span>
                            <input type="hidden" name="ticket_price" id="hiddenTotalPrice">
                        </div>
                        <div class="total">Total: Ksh. <span id="displayTotalPrice">0</span></div>
                        <button type="submit" name="purchase-ticket-btn" id="confirmBtn">Buy</button>
                    </form>

                </div>
            </div>

        </main>
        <!-- Main Close -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Toggle for description section
            const toggleLink = document.getElementById('toggle-description');
            const descriptionText = document.getElementById('description-text');

            toggleLink?.addEventListener('click', (e) => {
                e.preventDefault();
                descriptionText.classList.toggle('expanded');
                toggleLink.textContent = descriptionText.classList.contains('expanded') ? 'Read Less' : 'Read More';
            });

            // Ticket modal elements
            const modal = document.getElementById("ticketModal");
            const ticketCountInput = document.getElementById("ticketCount");
            const eventTicketId = document.getElementById('event-ticket-id');
            const ticketPriceElement = document.getElementById('ticketPrice');
            const hiddenTotalPrice = document.getElementById("hiddenTotalPrice");
            const displayTotalPrice = document.getElementById("displayTotalPrice");
            const errorElement = document.getElementById("error-message");
            const form = modal.querySelector("form");

            let ticketPrice = 0; // Holds the price of a single ticket for calculations

            // Format price as currency (with commas)
            function formatCurrency(amount) {
                return amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }

            // Update total price based on ticket count
            function updateTotal() {
                const total = parseInt(ticketCountInput.value) * ticketPrice;
                hiddenTotalPrice.value = total; // Set hidden input value
                displayTotalPrice.textContent = formatCurrency(total); // Display formatted price
            }

            // Display an error message
            function showError(message) {
                errorElement.textContent = message;
                errorElement.style.display = "block";
            }

            // Hide the error message
            function hideError() {
                errorElement.style.display = "none";
            }

            // Open the modal and initialize ticket data
            window.openModal = (button) => {
                ticketPrice = parseFloat(button.dataset.price); // Set ticket price from button's data attribute
                eventTicketId.value = button.dataset.eventId; // Set event ID from button's data attribute
                ticketPriceElement.textContent = formatCurrency(ticketPrice); // Display price in modal
                modal.style.display = "block"; // Show modal
                ticketCountInput.value = 1; // Reset ticket count to 1
                updateTotal(); // Update total price
                hideError(); // Hide any previous error
            };

            // Close the modal and reset values
            window.closeModal = () => {
                modal.style.display = "none"; // Hide modal
                ticketCountInput.value = 1; // Reset ticket count
                updateTotal(); // Update total price
                hideError(); // Hide any previous error
            };

            // Update ticket count based on the +/- button clicks
            function updateCount(change) {
                let count = parseInt(ticketCountInput.value) + change;
                if (count < 1) {
                    showError("Ticket count cannot be less than 1");
                    return;
                }
                ticketCountInput.value = count;
                updateTotal(); // Recalculate total price
                hideError(); // Hide error
            }

            // Confirm the ticket selection and submit the form
            function confirmSelection(event) {
                event.preventDefault();
                if (parseInt(ticketCountInput.value) < 1) {
                    showError("Please select at least 1 ticket");
                    return;
                }

                // Ensure ticketCount is passed to the form
                let ticketCountHiddenInput = form.querySelector('input[name="ticketCount"]');
                if (!ticketCountHiddenInput) {
                    ticketCountHiddenInput = document.createElement("input");
                    ticketCountHiddenInput.type = "hidden";
                    ticketCountHiddenInput.name = "ticketCount";
                    form.appendChild(ticketCountHiddenInput);
                }
                ticketCountHiddenInput.value = ticketCountInput.value;
                form.submit(); // Submit the form
            }

            // Add event listeners for counter buttons and confirm button
            modal.querySelector('.counter button:first-child').addEventListener('click', () => updateCount(-1));
            modal.querySelector('.counter button:last-child').addEventListener('click', () => updateCount(1));
            document.getElementById('confirmBtn').addEventListener('click', confirmSelection);

            // Close modal if clicking outside the modal content
            window.onclick = (event) => {
                if (event.target == modal) {
                    closeModal();
                }
            };
        });
    </script>


    <script src="../assets/js/main.js"></script>

</body>

</html>