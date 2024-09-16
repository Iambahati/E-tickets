<?php

require_once  '../assets/php/admin/admin_functions.php';

require_once '../utils.php';

if (!isset($_SESSION['admin'])) {
    Utils::redirect_to('../index.php');
}

$admin = $_SESSION['admin'];

$interfaces = new Admin();

$cards_data = $interfaces->fetchEvents();

$checkReservationsForAnEvent = $interfaces->fetchEvents();

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
    <script src="https://kit.fontawesome.com/0d8179c479.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/modals.css">
    <link rel="stylesheet" href="../assets/css/alerts.css">
    <link rel="stylesheet" href="../assets/css/cards.css">
    <link rel="stylesheet" href="../assets/css/admincards.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
</head>

<body>

    <div>
        <div class="navbar">
            <h1 class="logo" style="font-size: 40px;">TickeTok </h1>
            <ul style="font-size: 15px;">
                <li><a href="#">Hi, <?= $admin ?></li>
                <li><a href="#" id="addBtn">Create Event</a></li>
                <li><a href="../assets/php/admin/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
    <br><br>
    <?= $message ?>

    <?php if (!$cards_data) : ?>
        <div>
            <p class="box">No Events at the moments!</p>
        </div>
    <?php else : ?>
        <!-- Start of Filter & Search Container -->
        <div class="filter-search-container">
            <div class="filter-container">
                <label for="event-filter">Sorted By:</label>
                <select name="event-filter">
                    <option value="Entertainment">Entertainment</option>
                    <option value="Conferencing">Conferencing</option>
                    <option value="Movies &amp; Theatre">Movies &amp; Theatre</option>
                    <option value="Sports">Sports</option>
                    <option value="Free events">Free events</option>
                </select>
            </div>
            <div class="search-container">
                <label>Search:</label> <input type="text" id="searchInput" class="search-input">
            </div>
        </div>
        <!-- End of Filter & Search Container -->
        <?= str_repeat('<br>', 4); ?>
        <p class="box">Events Line up</p>
        <!-- this row is the container with the card-->
        <div class="card-container" style="margin-left: 130px; margin-bottom: 100px">
            <?php $rowCount = 0; ?>
            <table id="cardsContainer" class="cards-table">
                <?php foreach ($cards_data as $card) : ?>
                    <?php if ($rowCount % 3 === 0) : ?>
                        <tr>
                        <?php endif; ?>
                        <td data-id="<?= $card['id'] ?>">
                            <div class="column-item">
                                <div class="card" style="width: 30rem;">
                                    <input type="hidden" name="eventId">
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
                                                <?php if ($card['date'] === null) : ?>
                                                    <input type="hidden" name="from_date" value="<?= $card['from_date'] ?>">
                                                    <input type="hidden" name="to_date" value="<?= $card['to_date'] ?>">
                                                    <?= date('d', strtotime($card['from_date'])) . ' - ' . date('d F Y', strtotime($card['to_date'])) ?>
                                                <?php else : ?>
                                                    <input type="hidden" name="date" value="<?= $card['date'] ?>">
                                                    <?= date('D, M d, Y', strtotime($card['date'])) ?>
                                                <?php endif; ?>
                                            </li>
                                            <li class="mb-11">
                                                <svg width="18" height="18" viewBox="0 0 24 24">
                                                    <g transform="translate(-644 -1260)">
                                                        <rect width="24" height="24" transform="translate(644 1260)" fill="rgba(0,0,0,0)"></rect>
                                                        <path d="M656,1282h0a45.794,45.794,0,0,1-3.5-4.53c-1.6-2.366-3.5-5.757-3.5-8.469a7,7,0,1,1,14,0c0,2.712-1.9,6.1-3.5,8.469A45.794,45.794,0,0,1,656,1282Zm0-18a5.006,5.006,0,0,0-5,5c0,3.124,3.5,7.95,5,9.879,1.5-1.906,5-6.683,5-9.879A5.006,5.006,0,0,0,656,1264Z"></path>
                                                        <circle cx="2.5" cy="2.5" r="2.5" transform="translate(653.5 1266.5)"></circle>
                                                    </g>
                                                </svg>
                                                <span class="venue"><?= $card['venue'] ?></span>
                                            </li>
                                            <li class="mb-11">
                                                <svg width="18" height="18" viewBox="0 0 24 24">
                                                    <g transform="translate(-576 -550)">
                                                        <rect width="24" height="24" transform="translate(576 550)" fill="rgba(0,0,0,0)"></rect>
                                                        <path d="M588,572a10,10,0,1,1,10-10A10.012,10.012,0,0,1,588,572Zm0-18a8,8,0,1,0,8,8A8.009,8.009,0,0,0,588,554Zm4.2,12.2h0L587,563v-6h1.5v5.2l4.5,2.7-.8,1.3Z"></path>
                                                    </g>
                                                </svg>
                                                <input type="hidden" name="time" value="<?= $card['time'] ?>">
                                                <?= date('h:i a', strtotime($card['time'])) . ' - ' . date('h:i a', strtotime($card['time'] . ' + 9 hours')) ?>
                                            </li>
                                            <li class="mb-11">KES. <?= number_format($card['ticket_price']) ?></li>
                                            <li class="mb-11">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512">
                                                    <path d="M64 64C28.7 64 0 92.7 0 128v64c0 8.8 7.4 15.7 15.7 18.6C34.5 217.1 48 235 48 256s-13.5 38.9-32.3 45.4C7.4 304.3 0 311.2 0 320v64c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V320c0-8.8-7.4-15.7-15.7-18.6C541.5 294.9 528 277 528 256s13.5-38.9 32.3-45.4c8.3-2.9 15.7-9.8 15.7-18.6V128c0-35.3-28.7-64-64-64H64zm64 112l0 160c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16V176c0-8.8-7.2-16-16-16H144c-8.8 0-16 7.2-16 16zM96 160c0-17.7 14.3-32 32-32H448c17.7 0 32 14.3 32 32V352c0 17.7-14.3 32-32 32H128c-17.7 0-32-14.3-32-32V160z" />
                                                </svg>
                                                Capacity: <?= $card['tickets_capacity'] ?>
                                            </li>
                                        </ul>
                                        <br><br>
                                        <button id="editBtn" class="btn btn-primary">Edit</button>
                                        <br><br>
                                        <button id="deleteBtn" class="btn btn-danger">Delete</button>
                                        <br><br>
                                        <a href="reports.php?evid=<?= $card['id'] ?>" class="btn btn-success">View Report</a>
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
    <?php endif; ?>

    <!--Modal for Adding Events-->
    <div class="modal" id="addevent">
        <div class="form-box">
            <h1>Add Event</h1>
            <form method="POST" action="../assets/php/admin-action.php" enctype="multipart/form-data">
                <div class="form-field">
                    <label>Event Name:</label>
                    <input type="text" name="event_name" placeholder="Event Name">
                </div>
                <div class="form-field">
                    <label>Event type:</label>
                    <select name="event_type">
                        <option value="Entertainment">Entertainment</option>
                        <option value="Conferencing">Conferencing</option>
                        <option value="Movies &amp; Theatre">Movies &amp; Theatre</option>
                        <option value="Sports">Sports</option>
                        <option value="Free events">Free events</option>
                    </select>
                </div>

                <div class="form-field">
                    <label>Event Duration:</label>
                    <select name="event_duration" id="event_duration">
                        <option value="one_day">One-Day Event</option>
                        <option value="date_range">Event with Date Range</option>
                    </select>
                </div>
                <div class="form-field" id="date_field">
                    <label>Event Date:</label>
                    <input type="date" name="date" placeholder="Event Date" min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+1 year')) ?>">
                </div>
                <div class="form-field" id="from_date_field" style="display: none;">
                    <label>From Date:</label>
                    <input type="date" name="from_date" placeholder="From Date" min="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d', strtotime('+1 year')); ?>">
                </div>
                <div class="form-field" id="to_date_field" style="display: none;">
                    <label>To Date:</label>
                    <input type="date" name="to_date" placeholder="To Date" min="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d', strtotime('+1 year')); ?>">
                </div>
                <div class="form-field">
                    <label>Event Venue:</label>
                    <input type="text" name="venue" placeholder="Venue">
                </div>
                <div class="form-field">
                    <label>Event Time:</label>
                    <input type="time" name="time" placeholder="Time">
                </div>
                <div class="form-field">
                    <label>Ticket Price:</label>
                    <input type="number" name="ticket_price" placeholder="Ticket Price">
                </div>
                <div class="form-field">
                    <label>Tickets Available:</label>
                    <input type="number" name="tickets_available" placeholder="Tickets Available">
                </div>
                <div class="form-field">
                    <input type="file" name="banner" accept="image/*">
                </div>
                <div class="btn-field">
                    <button type="button" class="close" id="addEventClose">Close</button>
                    <button type="submit" class="btn" name="addEventBtn">Add Event &rarr;</button>
                </div>
            </form>
        </div>
    </div>


    <div class="modal" id="editEvent">
        <div class="form-box">
            <h2 id="event-name-edt"></h2>
            <form method="POST" action="../assets/php/admin-action.php">
                <input type="hidden" name="eventId" value="<?= $card['id'] ?>">
                <div class="form-field">
                    <label>Event Name:</label>
                    <input type="text" name="event_name" placeholder="Event Name" value="<?= $card['event_name'] ?>">
                </div>
                <div class="form-field">
                    <label for="date">Date:</label>
                    <input type="date" name="date" value="" min="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d', strtotime('+1 year')); ?>">
                </div>

                <div class="form-field">
                    <label for="from_date">From Date:</label>
                    <input type="date" name="from_date" value="" min="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d', strtotime('+1 year')); ?>">
                </div>

                <div class="form-field">
                    <label for="to_date">To Date:</label>

                    <input type="date" name="to_date" value="" min="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d', strtotime('+1 year')); ?>">
                </div>

                <div class="form-field">
                    <label>Event Venue:</label>
                    <input type="text" name="venue" placeholder="Venue" value="<?= $card['venue'] ?>">
                </div>
                <div class="form-field">
                    <label>Event Time:</label>
                    <input type="time" name="time" placeholder="Time" value="<?= $card['time'] ?>">
                </div>
                <div class="form-field">
                    <label>Tickets price:</label>
                    <input type="number" name="ticket_price" placeholder="Ticket Price" value="<?= $card['ticket_price'] ?>">
                </div>
                <div class="form-field">
                    <label>Tickets capacity:</label>
                    <input readonly type="number" name="ticket_capacity" placeholder="Tickets Available" value="<?= $card['tickets_capacity'] ?>">

                </div>
                <div class="btn-field">
                    <button type="button" class="close" id="editClose">Close</button>
                    <button type="submit" class="btn editEvent-btn" name="editEventBtn">Update &rarr;</button>
                </div>
            </form>
            <br>
        </div>
    </div>

    <div class="modal" id="deleteEvent">
        <div class="form-box" style="height: 280px;">
            <h2 id="event-name-edt-del"></h2>
            <?= str_repeat('<br>', 2); ?>
            <form method="POST" action="../assets/php/admin-action.php">
                <input type="hidden" name="eventId" value="<?= $card['id'] ?>">
                <h2>Are you sure you want to delete this Event?</h2>
                <br>
                <div class="btn-field">
                    <button type="button" class="close" id="deleteClose">Close</button>
                    <button type="submit" class="btn" name="deleteEventBtn">Delete &rarr;</button>
                </div>
            </form>
        </div>
    </div>

    <!--Footer-->
    <?= str_repeat('<br>', 4); ?>
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
    <script>
        /**
         * Initializes live search functionality.
         * Binds an event listener to the search input 
         * and filters the table rows based on the entered search query.
         */
        function initializeLiveSearch() {
            const searchInput = document.getElementById('searchInput');
            const cardsContainer = document.getElementById('cardsContainer');
            const tableRows = cardsContainer.getElementsByTagName('tr');

            searchInput.addEventListener('input', function() {
                const query = searchInput.value.trim().toLowerCase();

                for (let i = 0; i < tableRows.length; i++) {
                    const cardTitle = tableRows[i].getElementsByClassName('card-title')[0].textContent.toLowerCase();

                    if (query === '' || cardTitle.includes(query)) {
                        tableRows[i].style.display = '';
                    } else {
                        tableRows[i].style.display = 'none';
                    }
                }

                // Show all cards if the search query is empty
                if (query === '') {
                    for (let i = 0; i < tableRows.length; i++) {
                        tableRows[i].style.display = '';
                    }
                }

                // Show a message when no matches are found
                const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
                if (visibleRows.length === 0) {
                    cardsContainer.innerHTML = '<p class="box">No matching events found.</p>';
                } else {
                    cardsContainer.innerHTML = ''; // Clear the message if matches are found
                }
            });
        }

        initializeLiveSearch();
    </script>
    <script src="../assets/js/adminmodals.js"></script>
</body>

</html>