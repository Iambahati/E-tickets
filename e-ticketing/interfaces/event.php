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
    <title> <?= $eventName ?? $event['title'] ?> </title>
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
                    <? if ($message) : ?>
                        <?= $message ?>
                    <? endif; ?>
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
                    <form action="../assets/php/action.php" method="post" class="form">
                         <?= Utils::insertCsrfToken() ?>
                        <input type="hidden" name="eventId" id="event-ticket-id">
                        <input type="hidden" name="userId" value='<?= $userId ?>'>
                        <div class="ticket-type">
                            <span class="ticket-name">Ticket</span>
                            <div class="counter">
                                <button type="button">-</button>
                                <input readonly type="number" id="ticketCount" value="1" min="1" name="number_of_tickets">
                                <button type="button">+</button>
                            </div>
                            <div id="error-message" style="display: none; color: red; margin-top: 10px;"></div>
                            <span class="ticket-price">Ksh. <span id="ticketPrice"></span></span>
                            <input type="hidden" name="ticket_price" id="hiddenTotalPrice">
                        </div>
                        <div class="total">Total: Ksh. <span id="displayTotalPrice">0</span></div>
                        <button type="submit" name="buy-ticket-btn" id="confirmBtn">Buy</button>
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
                e.preventDefault();
                descriptionText.classList.toggle('expanded');
                if (descriptionText.classList.contains('expanded')) {
                    toggleLink.textContent = 'Read Less';
                } else {
                    toggleLink.textContent = 'Read More';
                }
            });


        });

        // Ticket modal functionality
        const modal = document.getElementById("ticketModal");
        const ticketCountInput = document.getElementById("ticketCount");
        const eventTicketId = document.getElementById('event-ticket-id');
        const ticketPriceElement = document.getElementById('ticketPrice');
        const hiddenTotalPrice = document.getElementById("hiddenTotalPrice");
        const displayTotalPrice = document.getElementById("displayTotalPrice");
        const errorElement = document.getElementById("error-message");
        const form = modal.querySelector("form");
        btn = document.querySelector('[name="client-signin-btn"]');

        let ticketPrice = 0;

        function formatCurrency(amount) {
            return amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function updateTotal() {
            const count = parseInt(ticketCountInput.value);
            const total = count * ticketPrice;
            hiddenTotalPrice.value = total;
            displayTotalPrice.textContent = formatCurrency(total);
        }

        function showError(message) {
            errorElement.textContent = message;
            errorElement.style.display = "block";
        }

        function hideError() {
            errorElement.style.display = "none";
        }

        window.openModal = (button) => {
            ticketPrice = parseFloat(button.dataset.price);
            eventTicketId.value = button.dataset.eventId;
            ticketPriceElement.textContent = formatCurrency(ticketPrice);
            modal.style.display = "block";
            ticketCountInput.value = 1;
            updateTotal();
            hideError();
        };

        window.closeModal = () => {
            modal.style.display = "none";
            ticketCountInput.value = 1;
            updateTotal();
            hideError();
        };

        function updateCount(change) {
            let currentCount = parseInt(ticketCountInput.value);

            let newCount = currentCount + change;

            if (newCount < 1) {
                showError("Ticket count cannot be less than 1");
                return;
            }

            ticketCountInput.value = newCount;

            updateTotal();
            hideError();
        }

        function confirmSelection(event) {
            event.preventDefault();
            if (parseInt(ticketCountInput.value) < 1) {
                showError("Please select at least 1 ticket");
                return;
            }
            form.submit();
        }

        // Event listeners
        const decrementButton = modal.querySelector('.counter button:first-child');
        const incrementButton = modal.querySelector('.counter button:last-child');

        decrementButton.addEventListener('click', (event) => {
            event.preventDefault();
            updateCount(-1);
        });

        incrementButton.addEventListener('click', (event) => {
            event.preventDefault();
            updateCount(1);
        });

        document.getElementById('confirmBtn').addEventListener('click', confirmSelection);

        window.onclick = (event) => {
            if (event.target == modal) {
                closeModal();
            }
        };
    </script>


    <script src="../assets/js/main.js"></script>

</body>

</html>