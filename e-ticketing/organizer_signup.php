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
        <form action="assets/php/organizer_action.php" method="post" id="signup" class="form">
            <?= Utils::insertCsrfToken() ?>
            <h1>Merchant Sign Up</h1>
            <?= $message ?>

            <label>An organizer is the company or person who is hosting the event</label>
            <?= str_repeat('<br>', 2); ?>

            <div class="form-field">
                <label for="orgname">Organizer Name:</label>
                <input type="text" name="orgname" id="orgname" autocomplete="off" placeholder="Awesome Organizer Ltd">
                <small></small>
            </div>

            <div class="form-field">
                <label for="email">Email:</label>
                <input type="text" name="email" id="org-email" autocomplete="off" placeholder="hello@awesome.com">
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
                <button id="acc-login" type="submit" name="organizer-signup-btn" class="btn">Sign Up</button>
            </div>
        </form>
        <ul class="inline-links">
            <li class="inline-links-item">
                <span>Already got an account? &#160&#160&#160<a class="link" href="organizer_signin.php">Sign in</a></span>
            </li>
        </ul>
    </div>

    <!-- JS validations -->
    <script>
    // Get references to the form inputs and form element
    const orgNameEl = document.querySelector('[name="orgname"]'),
        orgEmailEl = document.querySelector('[name="email"]'),
        phoneEl = document.querySelector('[name="phone"]'),
        passwordEl = document.querySelector('[name="password"]'),
        confirmPasswordEl = document.querySelector('[name="confirm-password"]'),
        btn = document.querySelector('[name="organizer-signup-btn"]');

    // Map of input elements to their friendly field names
    const fieldLabels = {
        orgname: 'Organizer Name',
        email: 'Email',
        phone: 'Phone',
        password: 'Password',
        'confirm-password': 'Confirm Password'
    };

    // Define a function to check if a value is required
    const isRequired = value => Boolean(value);

    // Show success message
    const showSuccess = (input) => {
        const formField = input.parentElement;
        formField.classList.remove('error');
        formField.classList.add('success');
        const error = formField.querySelector('small');
        error.textContent = '';
    };

    // Show an error message using the field label
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

    // Validation functions for each field
    const checkOrgName = () => isValid(orgNameEl, /^[a-zA-Z\s]+$/, 'Organizer Name should only contain letters.');
    const checkOrgEmail = () => isValid(orgEmailEl, /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/, 'Email is not valid.');
    const checkPhone = () => isValid(phoneEl, /^\d{10}$/, 'Phone number should be 10 digits.');
    const checkPassword = () => isValid(passwordEl, /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/, 'Password must have at least 8 characters including 1 lowercase, 1 uppercase, 1 number, and 1 special character.');

    const checkPasswordMatch = () => {
        const password = passwordEl.value.trim();
        const confirmPassword = confirmPasswordEl.value.trim();
        const fieldLabel = fieldLabels['confirm-password'];
        if (!isRequired(confirmPassword)) {
            showError(confirmPasswordEl, `${fieldLabel} is required.`);
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

        if (!checkOrgName()) isValidForm = false;
        if (!checkOrgEmail()) isValidForm = false;
        if (!checkPhone()) isValidForm = false;
        if (!checkPassword()) isValidForm = false;
        if (!checkPasswordMatch()) isValidForm = false;

        if (!isValidForm) {
            e.preventDefault(); // Prevent form submission if there are validation errors
        }
    });
</script>

</body>

</html>
