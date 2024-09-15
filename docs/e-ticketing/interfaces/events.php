<!DOCTYPE Html>
<html lang="en">
<head>
    <meta charset="x-UTF-16LE-BOM">
    <meta name="viewport" content="width=device=width, initial-scale=1.0">
    <title>TickeTok </title>
    <link href="https://fonts.googleapis.com/css?family=Bad+Script|Comfortaa|Amiri|Cormorant+Garamond|Rancho|Fredericka+the+Great|Handlee|Homemade+Apple|Philosopher|Playfair+Display+SC|Reenie+Beanie|Unna|Zilla+Slab" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/modals.css">
    <link rel="stylesheet" href="../css/cards.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

<div>
    <div class="navbar">
        <h1 class="logo" style="font-size: 40px;">TickeTok </h1>
        <ul style="font-size: 15px;">
            <li><a href="../interfaces/events.php">Home</a></li>
            <li><a href="../interfaces/history.php">My History</a></li>
            <li><a href="../../logout.php">Logout</a></li>
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
            <!-- Loop this card-->
        <div class="card" style="width: 33rem;">
                    <img class="card-img-top" src="../images/p1.jpg" alt="Event Image">
                    <div class="card-body">
                    <h5 class="card-title">Event Name</h5>
                    <ul class="card-text">
                        <li>Date:</li>
                        <li>Venue:</li>
                        <li>Time:</li>
                        <li>Ticket Price:</li>
                        <li>Tickets Available:</li>
                    </ul>
                    <br><br>
                <button type="button" id="buyingBtn" class="btn btn-primary" >Buy Ticket &rarr;</button>
                <br><br>
                <button type="submit" id="qrCodeBtn" class="btn btn-primary">Generate QR Code</button>
                    </div>
                </div>
        </div>
    </div>

<!--Modal for Buying Tickets-->
<div class="modal" id="buying">
<div class="form-box">
    <h1>Ticket Purchase</h1>
    <form method="POST" action="" onsubmit="">
            <select class="input-field" style="width:100%; height:40px;" name="number_of_tickets" >
                    <option disabled selected>Number Of Tickets (Maximum - 5)</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
            </select>
            <div class="error"></div>

            <select class="input-field" style="width:100%; height:40px;" name="mpesa">
                    <option disabled selected>Payment Method</option>
                    <option value="MPESA">M-PESA</option>
            
            </select>
            <div class="error"></div>
        <div class="btn-field">
        <button type="button" style="background-color: #999999; margin-top: 180px;" id="buyingClose">Close</button>
        <button type="submit" style="background-color: green; margin-top: 180px;" name= "pay" >Pay &rarr;</button>
    </div>
    </form>
</div>
</div>

<!--Modal for QR Code-->
<div class="modal" id="qr">
<div class="form-box">
    <h1>Your e-Ticket</h1>
    <h3 style="margin-top: 15px; color:red; margin-left: 10px; font-size:13px; text-align: center;">Do not close before scanning! Ticket will not be regenerated</h3>  
    <!--Should be displayed here-->
            <div class="error"></div>
        <div class="btn-field"> 
        <button type="button" class="btn btn-primary" style="margin-top: 260px; margin-left: 100px;" id="qrClose"> Ok &rarr;</button>
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
<script src='../js/clientmodals.js'></script>
</body>
</html>