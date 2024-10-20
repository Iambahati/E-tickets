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
                                <button onclick="updateCount(-1)">-</button>
                                <input readonly type="number" id="ticketCount" value="1" min="1" name="number_of_tickets" onchange="updateTotal()">
                                <button onclick="updateCount(1)">+</button>
                            </div>
                            <div id="error-message" style="display: none; color: red; margin-top: 10px;"></div>
                            <span class="ticket-price">Ksh.<span id="ticketPrice"></span></span>
                            <input type="hidden" name="ticket_price" id="totalPrice">
                        </div>
                        <div class="total">Total: Ksh. <span id="totalPrice">0</span></div>
                        <button type="submit" name="purchase-ticket-btn" id="confirmBtn">Buy</button>
                    </form>

                </div>
            </div>


        </main>
        <!-- Main Close -->
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleLink = document.getElementById('toggle-description');
            const descriptionText = document.getElementById('description-text');

            toggleLink.addEventListener('click', (e) => {
                e.preventDefault(); // Prevent default anchor behavior
                descriptionText.classList.toggle('expanded');
                if (descriptionText.classList.contains('expanded')) {
                    toggleLink.textContent = 'Read Less';
                } else {
                    toggleLink.textContent = 'Read More';
                }
            });


        });

        const modal = document.getElementById("ticketModal");
        let ticketPrice = 0;

        function openModal(button) {
            ticketPrice = parseFloat(button.getAttribute('data-price'));
            document.getElementById('event-ticket-id').value = button.getAttribute('data-event-id');
            document.getElementById('ticketPrice').textContent = formatCurrency(ticketPrice);
            modal.style.display = "block";
            document.getElementById('ticketCount').value = 1;
            updateTotal();
            hideError();
        }

        function closeModal() {
            modal.style.display = "none";
            document.getElementById('ticketCount').value = 1;
            updateTotal();
            hideError();
        }

        function updateCount(change, event) {
            event.preventDefault(); // Prevent form submission
            const input = document.getElementById("ticketCount");
            let count = parseInt(input.value) + change;

            if (count < 1) {
                showError("Ticket count cannot be less than 1");
                return;
            }

            input.value = count;
            updateTotal();
            hideError();
        }

        function updateTotal() {
            const ticketCount = parseInt(document.getElementById("ticketCount").value);
            const total = ticketCount * ticketPrice;
            document.getElementById("totalPrice").textContent = formatCurrency(total);
        }

        function formatCurrency(amount) {
            return amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function showError(message) {
            const errorElement = document.getElementById("error-message");
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = "block";
            } else {
                console.error(message);
            }
        }

        function hideError() {
            const errorElement = document.getElementById("error-message");
            if (errorElement) {
                errorElement.style.display = "none";
            }
        }

        function confirmSelection(event) {
            event.preventDefault();

            const ticketCount = parseInt(document.getElementById("ticketCount").value);
            if (ticketCount < 1) {
                showError("Please select at least 1 ticket");
                return;
            }

            // Add ticket count to the form
            const form = document.querySelector("#ticketModal form");
            let ticketCountInput = form.querySelector('input[name="ticketCount"]');
            if (!ticketCountInput) {
                ticketCountInput = document.createElement("input");
                ticketCountInput.type = "hidden";
                ticketCountInput.name = "ticketCount";
                form.appendChild(ticketCountInput);
            }
            ticketCountInput.value = ticketCount;

            // If everything is okay, submit the form
            form.submit();
        }

        // Close the modal if clicked outside the content
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        // Add event listeners after the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            const decrementBtn = document.querySelector('.counter button:first-child');
            const incrementBtn = document.querySelector('.counter button:last-child');
            const confirmBtn = document.getElementById('confirmBtn');

            decrementBtn.addEventListener('click', (e) => updateCount(-1, e));
            incrementBtn.addEventListener('click', (e) => updateCount(1, e));
            confirmBtn.addEventListener('click', confirmSelection);
        });
    </script>

    <script src="../assets/js/main.js"></script>

</body>

</html>