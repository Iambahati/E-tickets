<?php
// buyer signin.php
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

// `unset()` function destroys a variable. Once a variable is unset, it's no longer accessible
unset($_SESSION['success'], $_SESSION['error']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Merchant Sign In</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>



<body>
    <div class="theme-dark">
        <div class="container">
            <form action="assets/php/action.php" method="post" class="form">
                <?= Utils::insertCsrfToken() ?>
                <h1>Buyer Sign In</h1>
                <?= $message ?>
                <div class="form-field">
                    <label for="name">Email:</label>
                    <input type="text" name="email" placeholder="Your Email address">
                    <small></small>
                </div>

                <div class="form-field">
                    <label for="password">Password:</label>
                    <input type="text" name="password" placeholder="Password">
                    <small></small>
                </div>
                <div class="form-field">
                    <button type="submit" name="client-signin-btn" class="btn">Sign In</button>
                </div>
            </form>
            <ul class="inline-links">
                <li class="inline-links-item">
                    <a href="forgot_password.php" class="link"><span class="text">Forgot Password?</span></a>
                </li>
                <li class="inline-links-item">
                <span>Dont have an account? &#160&#160&#160<a href="buyer_signup.php" class="link"><span class="text">Sign Up</span></a>
                </li>
            </ul>

        </div>

        <!--  JS validations -->
        <script>
            // get references to the form inputs &  form element
            const email = document.querySelector('[name="email"]'),
                passwordEl = document.querySelector('[name="password"]'),
                btn = document.querySelector('[name="client-signin-btn"]');

            // Map of input elements to their friendly field names
            const fieldLabels = {
                email: 'Email',
                password: 'Password'
            };


            // define a function to check if a value is required
            const isRequired = value => Boolean(value);

            const showSuccess = (input) => {
                const formField = input.parentElement;
                formField.classList.remove('error');
                formField.classList.add('success');
                const error = formField.querySelector('small');
                error.textContent = '';
            };
            // define a function to show an error message for an input element
            const showError = (input, message) => {
                const formField = input.parentElement;
                formField.classList.remove('success');
                formField.classList.add('error');
                const error = formField.querySelector('small');
                error.textContent = message;
            };

            const isValid = (input, pattern, message) => {
                const value = input.value.trim();
                const fieldLabel = fieldLabels[input.name];
                if (!isRequired(value)) {
                    showError(input, `${fieldLabel} is required.`);
                    return false;
                } else if (!pattern.test(value)) {
                    showError(input, message);
                    return false;
                } else {
                    showSuccess(input);
                    return true;
                }
            };

            
            // define a function to check the username, email and password inputs

            const checkEmail = () => isValid(email, /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/, 'Email is not valid.');
            const checkPassword = () => isValid(passwordEl, /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/, 'Password must have at least 8 characters that include at least 1 lowercase character, 1 uppercase character, 1 number, and 1 special character.');

            // Event listener for the form submit button
            btn.addEventListener('click', (e) => {

                // Check each field for errors and if any fields have errors, prevent form submission
                let isValidForm = true;

                if (!checkEmail()) {
                    isValidForm = false;
                }

                if (!checkPassword()) {
                    isValidForm = false;
                }

                // If all fields are valid, submit the form
                if (!isValidForm) {
                    e.preventDefault();
                }
            });
        </script>
</body>

</html>