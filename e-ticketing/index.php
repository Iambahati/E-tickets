<?php

require_once 'utils.php';

if (isset($_SESSION['accRole'])) {
    switch ($_SESSION['accRole']) {
        case 1:
            Utils::redirect_to("interfaces/admin.php");
            break;
        case 2:
            Utils::redirect_to("interfaces/org-dashboard.php");
            break;
        case 3:
            Utils::redirect_to("interfaces/events.php");
            break;
        default:
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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-ticketing App</title>
    <link href="https://fonts.googleapis.com/css?family=Bad+Script|Comfortaa|Amiri|Cormorant+Garamond|Rancho|Fredericka+the+Great|Handlee|Homemade+Apple|Philosopher|Playfair+Display+SC|Reenie+Beanie|Unna|Zilla+Slab" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Comfortaa, sans-serif;
        }
        body {
            line-height: 1.5;
        }
        .banner {
            width: 100%;
            height: 100vh;
            background-image: linear-gradient(rgba(0,0,0,0.25),rgba(0,0,0,0.35)),url("assets/images/p2.jpg");
            background-size: cover;
            background-position: center;
        }
        .navbar {
            width: 85%;
            margin: auto;
            padding: 55px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo {
            font-weight: lighter;
            color: #ffffff;
            font-family: 'Fredericka the Great', cursive;
            width: 250px;
            cursor: pointer;
        }
        .navbar ul {
            list-style: none;
            display: flex;
        }
        .navbar ul li {
            margin: 0 20px;
            position: relative;
        }
        .navbar ul li a {
            text-decoration: none;
            color: #fff;
            text-transform: uppercase;
        }
       
        .content {
            width: 100%;
            position: absolute;
            top: 44%;
            transform: translateY(-50%);
            text-align: center;
            color: #fff;
        }
        .content h1 {
            font-family: 'Fredericka the Great', cursive;
            font-size: 70px;
            margin-top: 80px;
            font-weight: lighter;
        }
        .content p {
            margin: 20px auto;
            font-weight: 50;
            line-height: 25px;
        }
        .button {
            display: inline-block;
            color: white;
            background-color: #0084ff;
            border: none;
            border-radius: 5px;
            padding: 12px 20px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #006dd6;
        }
        .login-dropdown {
            position: relative;
        }
        .login-btn {
            display: flex;
            align-items: center;
            background-color: #0084ff;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 5px;
        }
        .chevron-down {
            margin-left: 0.5rem;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #ffffff;
            min-width: 240px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
            overflow: hidden;
            padding: 8px 0;
        }
        .dropdown-content li {
            padding: 8px 16px;
            text-decoration: none;
            color: #333333;
        }
        .dropdown-content li:hover {
            background-color: #f1f1f1;
        }
        .dropdown-item-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .dropdown-item-header-left {
            display: flex;
            align-items: center;
        }
        .dropdown-content svg {
            margin-right: 10px;
            flex-shrink: 0;
        }
        .dropdown-content a p {
            margin: 0;
            font-size: 0.8rem;
            color: #666;
        }
        .about-heading {
            text-align: right;
            margin-right: 38%;
            margin-top: 30px;
        }
        .about-heading h1 {
            font-size: 60px;
            font-weight: lighter;
            color: #fcc201;
            font-family: 'Fredericka the Great', cursive;
            margin-bottom: 1px;
            margin-top: 20px;
        }
        .about-us {
            display: flex;
            align-items: center;
            width: 85%;
            margin: auto;
        }
        .about-us img {
            flex: 0 50%;
            max-width: 50%;
            height: auto;
        }
        .inner-section {
            padding: 40px;
        }
        .inner-section h2 {
            color: #1e1e1e;
            font-size: 50px;
            margin: 15px 1px;
        }
        .inner-section p {
            color: #999999;
            font-size: 18px;
            line-height: 1.5;
            margin: 15px 1px;
        }
        .contact-us {
            display: inline-block;
            color: white;
            font-size: 15px;
            background-color: #0084ff;
            border: none;
            border-radius: 5px;
            padding: 12px 20px;
            cursor: pointer;
        }
        .contact-us:hover {
            background-color: #006dd6;
        }
        .footer {
            background-color: #24262b;
            padding: 70px 0;
        }
        .container {
            max-width: 1170px;
            margin: auto;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
        .footer-col {
            width: 25%;
            padding: 0 15px;
        }
        .footer-col h4 {
            font-size: 18px;
            color: #ffffff;
            text-transform: capitalize;
            margin-bottom: 35px;
            font-weight: 500;
            position: relative;
        }
        .footer-col h4::before {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            background-color: #009688;
            height: 2px;
            width: 50px;
        }
        .footer-col ul li:not(:last-child) {
            margin-bottom: 10px;
        }
        .footer-col ul li a {
            font-size: 16px;
            text-transform: capitalize;
            color: #ffffff;
            text-decoration: none;
            font-weight: 300;
            display: block;
            transition: all 0.3s ease;
        }
        .footer-col ul li a:hover {
            color: #ffffff;
            padding-left: 8px;
        }
        @media(max-width: 767px) {
            .footer-col {
                width: 50%;
                margin-bottom: 30px;
            }
        }
        @media(max-width: 574px) {
            .footer-col {
                width: 100%;
            }
        }
        @media only screen and (max-width: 60em) {
            body {
                padding: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="banner">
        <div class="navbar">
            <h1 class="logo">E-ticketing</h1>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#" id="signupBtn">Sign Up</a></li>
                <li class="login-dropdown">
                    <a href="#" class="login-btn">
                        Login
                        <svg class="chevron-down" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </a>
                    <ul class="dropdown-content">
                        <a href="buyer_signin.php">
                        <li>
                            <div class="dropdown-item-header">
                                <div class="dropdown-item-header-left">
                                    <svg class="user-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                    <strong>Buyer Login</strong>
                                </div>
                                <svg class="forward-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                            <p>Access your account to make purchases</p> 
                        </li>
                        </a>
                       
                        <a href="organizer_signin.php">
                        <li>
                            <div class="dropdown-item-header">
                                <div class="dropdown-item-header-left">
                                    <svg class="shopping-bag-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                    <strong>Seller Login</strong>
                                </div>
                                <svg class="forward-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                            <p>Full control of your online store. Here you can view sales in real-time, set promotions, view analytics and more.</p>
                        </li>
                        </a>
                        
                    </ul>
                </li>
            </ul>
        </div>
        <div class="content">
            <h1>Fast & Convenient Ticketing</h1>
            <p>E-ticketing keeps you updated on any local events & gigs happening in the +254! Concerts from your favorite artists, Wine Tasting Events, Stand Up Comedy and so much more! <br>Book your tickets to the latest events effortlessly now by signing up with us today!</p>
        </div>
    </div>

    <div id="about">
        <div class="about-heading">
            <h1>About E-ticketing</h1>
        </div>
        <section class="about-us">
            <img src="assets/images/p3.jpg" alt="About Us Image">
            <div class="inner-section">
                <h2>We Are An E-ticketing Company</h2>
                <p>
                    As one of the top event ticket vendors in Kenya, we provide a platform where our target audience can buy tickets to various events in the most seamless way.
                    <br><br>Once a ticket to a particular event is purchased, a <strong>QR CODE</strong> can be generated and presented as the electronic ticket at the venue for scanning. <strong>The ticket is only valid for a ONE TIME use.</strong>
                </p>
                <h3>
                    For inquiries, contact us via email below at inquiries.E-ticketing@gmail.com today.
                </h3>
                <button type="button" id="contactUsBtn" class="contact-us">Contact Us</button>
            </div>
        </section>
    </div>



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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginBtn = document.querySelector('.login-btn');
            const dropdownContent = document.querySelector('.dropdown-content');

            loginBtn.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
            });

            document.addEventListener('click', function(event) {
                if (!loginBtn.contains(event.target) && !dropdownContent.contains(event.target)) {
                    dropdownContent.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>