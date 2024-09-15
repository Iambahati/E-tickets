<?php

require_once  '../functions/admin.php';

$interfaces = new Admin();

$cards = $interfaces->fetchEvents();

?> 

<!DOCTYPE Html>
<html lang="en">
<head>
    <meta charset="x-UTF-16LE-BOM">
    <meta name="viewport" content="width=device=width, initial-scale=1.0">
    <title>TickeTok </title>
    <link href="https://fonts.googleapis.com/css?family=Bad+Script|Comfortaa|Amiri|Cormorant+Garamond|Rancho|Fredericka+the+Great|Handlee|Homemade+Apple|Philosopher|Playfair+Display+SC|Reenie+Beanie|Unna|Zilla+Slab" rel="stylesheet">
    <script src="https://kit.fontawesome.com/0d8179c479.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/modals.css">
    <link rel="stylesheet" href="../css/cards.css">
    <link rel="stylesheet" href="../css/admincards.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

<div>
    <div class="navbar">
        <h1 class="logo" style="font-size: 40px;">TickeTok </h1>
        <ul style="font-size: 15px;">
            <li><a href="../interfaces/admin.php">Home</a></li>
            <li><a href="#" id="addBtn">Add Event</a></li>
            <li><a href="#">Logout</a></li>
            <li><input type="text" id="search" name="search" placeholder="SEARCH"></li>
        </ul>
    </div>
</div>
<br><br>
<div>
    <p style="color: #fcc201; font-family: 'Fredericka the Great', cursive; font-size: 40px; margin-left: 130px">Upcoming Events</p>
</div>
<br><br>


<!-- this row is the container with the card-->
    <div class="card-container" style="margin-left: 130px; margin-bottom: 100px;">
        <div class="column-item">
        <div class="card" style="width: 33rem;">
                    <img class="card-img-top" src="../images/p1.jpg" alt="Event Image">
                    <div class="card-body">
                    <h3 class="card-title"><!--php code to fetch event name as added-->Event Name</h3>
                    <ul class="card-text">
                        <li>Date:</li>
                        <li>Venue:</li>
                        <li>Time:</li>
                        <li>Ticket Price:</li>
                        <li>Tickets Available:</li>
                    </ul>
                    <br><br>
                    <button id="editBtn" class="btn btn-primary" >Edit </button>
                <br><br>
                <button id="deleteBtn" class="btn btn-danger">Delete</button>
                <br><br>
                <!-- This button below should generate a pdf-->
                <button class="btn btn-success">View Report</button>
                    </div>
                </div>
        </div>
    </div>




<!--Modal for Adding Events-->
<div class="modal" id="addevent">
    <div class="form-box" style="height: 800px;">
        <h1>Add Event</h1>
        <form method="POST" action="../controller/action.php">
            <div class="input-group">
                <div class="input-field">
                    <input type="text"  name="event_name" placeholder="Event Name" value="<?= $card['event_name'] ?>">
                </div>
                <div class="input-field">
                    <input type="date"  name="date" placeholder="Event Date"value="<?= $card['date'] ?>">
                </div>
                <div class="input-field">
                    <input type="text"  name="venue" placeholder="Venue" value="<?= $card['venue'] ?>">
                </div>
                <div class="input-field">
                    <input type="time"  name="time" placeholder="Time" value="<?= $card['time'] ?>">
                </div>
                <div class="input-field">
                    <input type="number"  name="ticket_price" placeholder="Ticket Price" value="<?= $card['ticket_price'] ?>">
                </div>
                <div class="input-field">
                    <input type="number"  name="tickets_available" placeholder="Tickets Available" value="<?= $card['tickets_available'] ?>">
                </div>
                <div class="input-field">
                    <label for="banner">
                        <i class="fas fa-images" style="font-size: 2em; color: #3d3d3d;"></i>
                    </label>
                    <input type="file" required id="banner" name="banner" value="<?= $card['banner'] ?>">
                </div>
            </div>
            <div class="btn-field">
            <button type="button" style="background-color: #999999;margin-top: 220px" id="addEventClose">Close</button>
            <button type="submit" style="background-color: #0084ff;margin-top: 220px" name="addEventBtn" >Add &rarr;</button>
        </div>
        </form>
        <br>
    </div>
</div>


<!--Modal for Editing Events-->
<div class="modal" id="editEvent">
    <div class="form-box" style="height: 800px;">
        <h1>Edit Event</h1>
        <form method="POST" action="../controller/action.php">
            <div class="input-group">
                <div class="input-field">
                    <input type="text" name="event_name" placeholder="Event Name" value="<?= $card['event_name'] ?>">
                </div>
                <div class="input-field">
                    <input type="date"  name="date" placeholder="Event Date" value="<?= $card['date'] ?>">
                </div>
                <div class="input-field">
                    <input type="text"  name="venue" placeholder="Venue" value="<?= $card['venue'] ?>">
                </div>
                <div class="input-field">
                    <input type="time" name="time" placeholder="Time" value="<?= $card['time'] ?>">
                </div>
                <div class="input-field">
                    <input type="number"  name="ticket_price" placeholder="Ticket Price" value="<?= $card['ticket_price'] ?>">
                </div>
                <div class="input-field">
                    <input type="number"  name="tickets_available" placeholder="Tickets Available" value="<?= $card['tickets_available'] ?>">
                </div>
                <div class="input-field">
                    <label for="banner">
                        <i class="fas fa-images" style="font-size: 2em; color: #3d3d3d;"></i>
                    </label>
                    <input type="file" required id="banner" name="banner" value="<?= $card['banner'] ?>">
                </div>
            </div>
            <div class="btn-field">
            <button type="button" style="background-color: #999999;margin-top: 220px" id="editClose">Close</button>
            <button type="submit" name="editEventBtn" style="background-color: #0084ff;margin-top: 220px" onclick="">Update &rarr;</button>
        </div>
        </form>
        <br>
    </div>
</div>


<!-- Modal for Deleting Events-->
<div class="modal" id="deleteEvent">
    <div class="form-box" style="height: 460px;">
        <h1>Delete Event</h1>
        <form method="POST" action="../controller/action.php">
        <h2>Are you sure you want to delete this Event?</h2>
        <br>
        <div class="btn-field">
            <button type="button" style="background-color: #999999;margin-top: 120px" id="deleteClose">Close</button>
            <button type="submit" name="deleteEventBtn" style="background-color: #de0a26;margin-top: 120px" >Delete &rarr;</button>
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
                    <li><a href="../index.php">About Us</a></li>
                    <li><a href="../index.php">Our Services</a></li>
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
<script src="../js/adminmodals.js"></script>
</body>
</html>