<?php

require_once 'utils.php';


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
    <title>E-ticketing Forgot Password</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>



<body>
    <div class="theme-dark">
        <div class="container">
            <form action="assets/php/action.php" method="post" class="form">
                <?= Utils::insertCsrfToken() ?>
                <h1>Forgot Password</h1>
                <?= $message ?>
                <p> Enter your email address to reset your password</p>
                <br>
                <div class="form-field">
                    <input type="text" name="email" placeholder="Your Email address">
                    <small></small>
                </div>
                <div class="form-field">
                    <button type="submit" name="forgot-passwd-btn" class="btn">Continue</button>
                </div>
            </form>

            <ul class="inline-links">
            <li class="inline-links-item">
                <span>Already an event organizer? &#160&#160&#160<a class="link" href="organizer_signin.php">Sign in</a></span>
            </li>
            <li class="inline-links-item">
                <span>Want to views events? &#160&#160&#160<a class="link" href="buyer_signin.php">Sign in</a></span>
            </li>
        </ul>

        </div>

        <!--  JS validations -->
        <script>
            // get references to the form inputs &  form element
            const email = document.querySelector('[name="email"]'),
            btn = document.querySelector('[name="forgot-passwd-btn"]');
            ;

            // Map of input elements to their friendly field names
            const fieldLabels = {
                email: 'Email',
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

            // Event listener for the form submit button
            btn.addEventListener('click', (e) => {

                // Check each field for errors and if any fields have errors, prevent form submission
                let isValidForm = true;

                if (!checkEmail()) {
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