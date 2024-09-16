<?php

require_once 'utils.php';

if (isset($_SESSION['userType'])) {
    switch ($_SESSION['userType']) {
        case 'client':
            Utils::redirect_to('interfaces/events.php');
            break;
        case 'admin':
            Utils::redirect_to('interfaces/admin.php');
            break;
        default:
            // Handle the default case if needed
            break;
    }
} else {
    // Handle the case where 'userType' is not set, if needed
    // For example, redirect to a login page or do nothing
    // Utils::redirect_to('login.php');
}


// coalescing operator `??`
// checks if a variable exists and is not null,
// and if it doesn't, it returns a default value
$message = $_SESSION['success'] ?? $_SESSION['error'] ?? null;
$contactUsMsg = $_SESSION['contactUsMessage'] ?? null;

// `unset()` function destroys a variable. Once a variable is unset, it's no longer accessible
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['contactUsMessage']);

?>


<!DOCTYPE Html>
<html lang="en">

<head>
    <meta charset="x-UTF-16LE-BOM">
    <meta name="viewport" content="width=device=width, initial-scale=1.0">
    <title>E-ticketing App</title>
    <link href="https://fonts.googleapis.com/css?family=Bad+Script|Comfortaa|Amiri|Cormorant+Garamond|Rancho|Fredericka+the+Great|Handlee|Homemade+Apple|Philosopher|Playfair+Display+SC|Reenie+Beanie|Unna|Zilla+Slab" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/alerts.css">
    <link rel="stylesheet" href="assets/css/modals.css">
    <link rel="stylesheet" href="assets/css/aboutus.css">
    <link rel="stylesheet" href="assets/css/footer.css">

</head>

<body>
    <div class="banner">
        <div class="navbar">
            <h1 class="logo"> E-ticketing </h1>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#" id="signupBtn">Sign Up</a></li>
                <li><a href="#" id="signinBtn">Login</a></li>
            </ul>
        </div>
        <?= $message ?>
        <div class="content">
            <h1>Fast & Convenient Ticketing</h1>
            <p>E-ticketing keeps you updated on any local events & gigs happening in the +254! Concerts from your favorite artists, Wine Tasting Events, Stand Up Comedy and so much more! <br>Book your tickets to the latest events effortlessly now by signing up with us today!</p>
            <div><br>

            </div>
        </div>
    </div>

    <!--Signup Modal-->
    <div class="modal" id="signup">
        <div class="form-box">
            <h1>Sign Up</h1>
            <small></small>
            <form id="signupForm">
                <input type="hidden" name="formType-signup" value="signup">
                <div class="input-field">
                    <input type="text" name="username" placeholder="Username">
                    <div class="error"></div>
                </div>
                <div class="input-field">
                    <input type="text" name="email" placeholder="Email">
                    <div class="error"></div>
                </div>
                <div class="input-field">
                    <input type="text" name="contact" placeholder="Contact">
                    <div class="error"></div>
                </div>
                <div class="input-field">
                    <input type="text" name="password" placeholder="Password">
                    <div class="error"></div>
                </div>
                <div class="input-field">
                    <input type="text" name="confirm_password" placeholder="Confirm Password">
                    <div class="error"></div>
                </div>

                <div class="btn-field">
                    <button type="button" class="close" id="signupClose">Close</button>
                    <button type="submit" class="btn" name="signup-btn">Signup &rarr;</button>
                </div>
            </form>
        </div>
    </div>


    <!--Signin Modal-->
    <div class="modal" id="signin">
        <div class="form-box" >
            <h1>Login</h1>
            <small></small>
            <form id="signinForm">
                <input type="hidden" name="formType-signin" value="signin">
                <div class="input-field">
                    <input type="text" id="signinemail" name="signin-email" placeholder="Email">
                    <div class="error"></div>
                </div>
                <div class="input-field">
                    <input type="text" id="signinpassword" name="signin-password" placeholder="Password">
                    <div class="error"></div>
                </div>
                <ul class="inline-links">
                    <li class="inline-links-item">
                        <a href="#"><span class="text">Forgotten Password?</span></a>
                    </li>
                </ul>
                <div class="btn-field">
                    <button type="button" class="close" id="signinClose">Close</button>
                    <button type="submit" class="btn" name="signin-btn">Login &rarr;</button>
                </div>
            </form>

        </div>
    </div>

    <!--About Us Section-->
    <div id="about">
        <div class="about-heading">
            <h1>About E-ticketing</h1>
        </div>
        <section class="about-us">
            <img src="assets/images/p3.jpg">
            <div class="inner-section">
                <h2>We Are An E-ticketing Company</h2>
                <p>
                    As one of the top event ticket vendors in Kenya, we provide a platform where our target audience can buy tickets to various events in the most seamless way.
                    <br><br>Once a ticket to a particular event is purchased, a <strong>QR CODE</strong> can be generated and presented as the electronic ticket at the venue for scanning. <strong>The ticket is only valid for a ONE TIME use.</strong>
                </p>
                <br>
                <h3>
                    For inquiries, contact us via email below at inquiries.E-ticketing@gmail.com today .
                </h3>
                <br>
                <button type="button" id="contactUsBtn" class="contact-us">Contact Us </button>
            </div>
        </section>
    </div>
    <br><br><br>

    <!--Contact Us Modal-->
    <div class="modal" id="contactus">
        <div class="form-box">
            <h1>Any Inquiries</h1>
            <small></small>
            <form id="contactForm">
                <input type="hidden" name="formType-contact" value="contact">
                <div class="input-field">
                    <input type="text" name="email_from" placeholder="Your email">
                    <div class="error"></div>
                </div>
                <div class="input-field">
                    <input type="text" name="subject" placeholder="Subject:">
                    <div class="error"></div>
                </div>
                <div class="input-field">
                    <textarea type="text" name="message" placeholder="Message"></textarea>
                    <div class="error"></div>
                </div>
                <div class="btn-field">
                    <button type="button" class="close" id="contactClose">Close</button>
                    <button type="submit" name="contact-btn" class="btn-send">Send &rarr;</button>
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
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#about">Our Services</a></li>
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

    <script src="assets/js/signup.js"></script>
    <script src="assets/js/signin.js"></script>
    <script src="assets/js/modals.js"></script>
    <script src="assets/js/contact.js"></script>

</body>

</html>