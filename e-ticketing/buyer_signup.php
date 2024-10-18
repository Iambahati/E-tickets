<?php

require_once 'utils.php';

// Coalescing operator to check for success or error messages
$message = $_SESSION['success'] ?? $_SESSION['error'] ?? null;

// Unset the session messages after displaying them
unset($_SESSION['success'], $_SESSION['error']);

?>

<!DOCTYPE html>
<html>

<head>
    <title>E-ticketing Sign Up</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container">
        <form action="assets/php/action.php" method="post" id="signup" class="form">
            <?= Utils::insertCsrfToken() ?>
            <h1>Buyer Sign Up</h1>
            <?= $message ?>
            <div class="form-field">
                <label for="fname">First Name:</label>
                <input type="text" name="fname" id="fname" autocomplete="off" placeholder="James">
                <small></small>
            </div>
            <div class="form-field">
                <label for="lname">Last Name:</label>
                <input type="text" name="lname" id="lname" autocomplete="off" placeholder="Smith">
                <small></small>
            </div>

            <div class="form-field">
                <label for="email">Email:</label>
                <input type="text" name="email" id="email" autocomplete="off" placeholder="john.m@gmail.com">
                <small></small>
            </div>

            <div class="form-field">
                <label for="phone">Phone:</label>
                <input type="text" name="phone" id="phone" autocomplete="">
                <small></small>
            </div>

            <div class="form-field">
                <label for="password">Password:</label>
                <input type="text" name="password" id="password" autocomplete="off">
                <small></small>
            </div>

            <div class="form-field">
                <label for="confirm-password">Confirm Password:</label>
                <input type="text" name="confirm-password" id="confirm-password" autocomplete="">
                <small></small>
            </div>
            <div class="form-field">
                <button id="acc-login" type="submit" name="client-signup-btn" class="btn">Sign Up</button>
            </div>
        </form>
        <ul class="inline-links">
            <li class="inline-links-item">
                <span>Already got an account? &#160&#160&#160<a class="link" href="buyer_signin.php">Sign in</a></span>
            </li>
        </ul>
    </div>

    <!-- JS validations -->
    <script>
        // Get references to the form inputs
        const fnameEl = document.querySelector('[name="fname"]'),
            lnameEl = document.querySelector('[name="lname"]'),
            emailEl = document.querySelector('[name="email"]'),
            phoneEl = document.querySelector('[name="phone"]'),
            passwordEl = document.querySelector('[name="password"]'),
            confirmPasswordEl = document.querySelector('[name="confirm-password"]'),
            btn = document.querySelector('[name="client-signup-btn"]');

            const fieldLabels = {
                fname: 'First Name',
                lname: 'Last Name',
                email: 'Email',
                phone: 'Phone',
                password: 'Password',
                'confirm-password': 'Confirm Password'

            };

        // Define a function to check if a value is required
        const isRequired = value => Boolean(value);

        const showSuccess = (input) => {
            const formField = input.parentElement;
            formField.classList.remove('error');
            formField.classList.add('success');
            const error = formField.querySelector('small');
            error.textContent = '';
        };

        // Define a function to show an error message for an input element
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

        // Define validation functions for the input fields
        const checkFName = () => isValid(fnameEl, /^[a-zA-Z\s]+$/, 'First name should only contain letters.');
        const checkLName = () => isValid(lnameEl, /^[a-zA-Z\s]+$/, 'Last name should only contain letters.');
        const checkEmail = () => isValid(emailEl, /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/, 'Email is not valid.');
        const checkPhone = () => isValid(phoneEl, /^\d{10}$/, 'Phone number should be 10 digits.');
        const checkPassword = () => isValid(passwordEl, /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/, 'Password must have at least 8 characters including 1 lowercase, 1 uppercase, 1 number, and 1 special character.');

        const checkPasswordMatch = () => {
            const password = passwordEl.value.trim();
            const confirmPassword = confirmPasswordEl.value.trim();
            if (!isRequired(confirmPassword)) {
                showError(confirmPasswordEl, 'Confirm password cannot be blank.');
                return false;
            } else if (password !== confirmPassword) {
                showError(confirmPasswordEl, 'Passwords do not match.');
                return false;
            } else {
                showSuccess(confirmPasswordEl);
                return true;
            }
        };

        // Event listener for the form submit button
        btn.addEventListener('click', (e) => {
            let isValidForm = true;

            if (!checkFName()) isValidForm = false;
            if (!checkLName()) isValidForm = false;
            if (!checkEmail()) isValidForm = false;
            if (!checkPhone()) isValidForm = false;
            if (!checkPassword()) isValidForm = false;
            if (!checkPasswordMatch()) isValidForm = false;

            if (!isValidForm) {
                e.preventDefault(); // Prevent form submission if the form is invalid
            }
        });
    </script>
</body>

</html>
