<?php

require_once '../utils.php';

require_once  '../assets/php/client_functions.php';

if (!isset($_SESSION['client'])) {
    Utils::redirect_to('../index.php');
}

$currentlyLoggedInUser = $_SESSION['client'];

// Create an instance of client
$interfaces = new Client();

$cards_data = $interfaces->fetchEvents();

// coalescing operator `??`
// checks if a variable exists and is not null,
// and if it doesn't, it returns a default value
$message = $_SESSION['success'] ?? $_SESSION['error'] ?? null;
// `unset()` function destroys a variable. Once a variable is unset, it's no longer accessible
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['contactUsMessage']);

?>


<!DOCTYPE Html>
<html lang="en">

<head>
    <meta charset="x-UTF-16LE-BOM">
    <meta name="viewport" content="width=device=width, initial-scale=1.0">
    <title>TickeTok </title>
    <link href="https://fonts.googleapis.com/css?family=Bad+Script|Comfortaa|Amiri|Cormorant+Garamond|Rancho|Fredericka+the+Great|Handlee|Homemade+Apple|Philosopher|Playfair+Display+SC|Reenie+Beanie|Unna|Zilla+Slab" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/modals.css">
    <link rel="stylesheet" href="../assets/css/modals.css">
    <link rel="stylesheet" href="../assets/css/alerts.css">
    <link rel="stylesheet" href="../assets/css/cards.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
</head>

<body>

    <div class="navbar">
        <h1 class="logo" style="font-size: 40px;">TickeTok </h1>
        <ul style="font-size: 15px;">
            <li><a href="#">Hi, <?= $currentlyLoggedInUser ?></li>
            <li><a href="history.php">My History</a></li>
            <li><a href="../assets/php/logout.php">Logout</a></li>
        </ul>
    </div>


    <br><br>
    <?= $message ?>


    <?php if (!$cards_data) : ?>
        <p class="box">No upcoming Events</p>
    <?php else : ?>
        <div>
            <p class="box">Upcoming Events</p>

            <!-- this row is the container with the card-->
            <div class="card-container" style="margin-left: 50px; margin-bottom: 70px;">
                <?php $rowCount = 0; ?>
                <table class="cards-table">
                    <?php foreach ($cards_data as $card) : ?>
                        <?php if ($rowCount % 3 === 0) : ?>
                            <tr>
                            <?php endif; ?>
                            <td data-id="<?= $card['id'] ?>">
                                <div class="column-item">
                                    <div class="card" style="width: 30rem">
                                        <img class="card-img-top" src="<?= $card['banner'] ?>" alt="Event Image">
                                        <div class="card-body">
                                            <h2 class="card-title"><?= $card['event_name'] ?></h2>
                                            <ul class="card-text">
                                                <li class="mb-11">
                                                    <svg width="16" height="16" viewBox="0 0 24 24">
                                                        <g transform="translate(-202 -198)">
                                                            <rect width="24" height="24" transform="translate(202 198)" fill="rgba(0,0,0,0)"></rect>
                                                            <path d="M222,221H206a2,2,0,0,1-2-2V203a2,2,0,0,1,2-2h1v-2h2v2h10v-2h2v2h1a2,2,0,0,1,2,2v16A2,2,0,0,1,222,221Zm-16-13v11h16V208H206Zm0-5v3h16v-3H206Z"></path>
                                                        </g>
                                                    </svg>
                                                    <?= ($card['date'] === null) ? date('d', strtotime($card['from_date'])) . ' - ' . date('d F Y', strtotime($card['to_date'])) : date('D, M d, Y', strtotime($card['date'])) ?>
                                                </li>
                                                <li class="mb-11">
                                                    <svg width="18" height="18" viewBox="0 0 24 24">
                                                        <g transform="translate(-644 -1260)">
                                                            <rect width="24" height="24" transform="translate(644 1260)" fill="rgba(0,0,0,0)"></rect>
                                                            <path d="M656,1282h0a45.794,45.794,0,0,1-3.5-4.53c-1.6-2.366-3.5-5.757-3.5-8.469a7,7,0,1,1,14,0c0,2.712-1.9,6.1-3.5,8.469A45.794,45.794,0,0,1,656,1282Zm0-18a5.006,5.006,0,0,0-5,5c0,3.124,3.5,7.95,5,9.879,1.5-1.906,5-6.683,5-9.879A5.006,5.006,0,0,0,656,1264Z"></path>
                                                            <circle cx="2.5" cy="2.5" r="2.5" transform="translate(653.5 1266.5)"></circle>
                                                        </g>
                                                    </svg>
                                                    <?= $card['venue'] ?>
                                                </li>
                                                <li class="mb-11">
                                                    <svg width="18" height="18" viewBox="0 0 24 24">
                                                        <g transform="translate(-576 -550)">
                                                            <rect width="24" height="24" transform="translate(576 550)" fill="rgba(0,0,0,0)"></rect>
                                                            <path d="M588,572a10,10,0,1,1,10-10A10.012,10.012,0,0,1,588,572Zm0-18a8,8,0,1,0,8,8A8.009,8.009,0,0,0,588,554Zm4.2,12.2h0L587,563v-6h1.5v5.2l4.5,2.7-.8,1.3Z"></path>
                                                        </g>
                                                    </svg>
                                                    <?= date('h:i a', strtotime($card['time'])) . ' - ' . date('h:i a', strtotime($card['time'] . ' + 9 hours')) ?>
                                                </li>
                                                <li class="mb-11">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512">
                                                        <path d="M64 64C28.7 64 0 92.7 0 128v64c0 8.8 7.4 15.7 15.7 18.6C34.5 217.1 48 235 48 256s-13.5 38.9-32.3 45.4C7.4 304.3 0 311.2 0 320v64c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V320c0-8.8-7.4-15.7-15.7-18.6C541.5 294.9 528 277 528 256s13.5-38.9 32.3-45.4c8.3-2.9 15.7-9.8 15.7-18.6V128c0-35.3-28.7-64-64-64H64zm64 112l0 160c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16V176c0-8.8-7.2-16-16-16H144c-8.8 0-16 7.2-16 16zM96 160c0-17.7 14.3-32 32-32H448c17.7 0 32 14.3 32 32V352c0 17.7-14.3 32-32 32H128c-17.7 0-32-14.3-32-32V160z" />
                                                    </svg>
                                                    KES. <?= number_format($card['ticket_price']) ?>
                                                </li>
                                            </ul>
                                            <br><br>
                                            <button type="button" id="buyingBtn" class="btn btn-primary">Buy Ticket &rarr;</button>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <?php $rowCount++; ?>

                            <?php if ($rowCount % 3 === 0 || $rowCount === count($cards_data)) : ?>
                            </tr>
                        <?php endif; ?>

                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    <?php endif; ?>
    <!--Modal for Buying Tickets-->
    <div class="modal" id="buying">
        <div class="form-box">
            <h2 id="event-name-buy"></h2>
            <form id="purchaseTicketForm" method="POST" action="../assets/php/action.php">
                <input type="hidden" name="eventId" value="<?= $card['id'] ?>">
                <?= str_repeat('<br>', 3); ?>
                <label for="" style="font-size: 15px;">No of tickets to buy</label>
                <div class="quantity-selector">
                    <svg class="decrease-quantity" width="18" height="18" viewBox="0 0 448 512">
                        <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                    </svg>

                    <input readonly name="number_of_tickets" value="1" min="1" max="5" class="input-field quantity" />

                    <svg class="increase-quantity" width="18" height="18">
                        <g transform="translate(-100 -1482)">
                            <rect width="24" height="24" transform="translate(100 1482)" fill="rgba(0,0,0,0)"></rect>
                            <path d="M119,1495h-6v6h-2v-6h-6v-2h6v-6h2v6h6Z"></path>
                        </g>
                    </svg>
                </div>
                <div class="total-price">Total price: KES. <span id="total-price-display">0</span></div>

                <input type="hidden" name="ticket_price" value="<?= $card['ticket_price'] ?>">
                <div class="btn-field">
                    <button type="button" class="close" id="buyingClose">Close</button>
                    <button type="submit" class="btn" name="purchase-ticket-btn">Pay &rarr;</button>
                </div>
            </form>
        </div>
    </div>

    <!--Footer-->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="footer-col">
                    <h4>company</h4>
                    <ul>
                        <li><a href="../assets/index.php">About Us</a></li>
                        <li><a href="../assets/index.php">Our Services</a></li>
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
    <script src='../assets/js/clientmodals.js'></script>
</body>

</html>