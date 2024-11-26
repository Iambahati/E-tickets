<?php

require_once 'utils.php';
require_once  'assets/php/client_functions.php';
require_once  'assets/php/organizer_functions.php';

$token = $_GET['token'] ?? null;
$email = $_GET['email'] ?? null;
$ref = $_GET['ref'] ?? null;
$missingFields = [];
if (!$token) {
    $missingFields[] = 'token';
}
if (!$email) {
    $missingFields[] = 'email';
}
if (!$ref) {
    $missingFields[] = 'ref';
}
if (!empty($missingFields)) {
    $_SESSION['error'] = 'Missing fields: ' . implode(', ', $missingFields);
}

$client = $interfaces = $ref === 'organizer' ? new Organizer() : new Client();

$tokenStatus = $client->validateResetToken($token);



if ($tokenStatus !== 0) {
    $errorMessages = [
        1 => 'Token has already been used.',
        2 => 'Token has expired.',
        3 => 'Invalid token.',
        4 => 'An error occurred.'
    ];

    $_SESSION['error'] = Utils::showMessage('error', $errorMessages[$tokenStatus]);
}

// Coalescing operator to check for success or error messages
$message = $_SESSION['success'] ?? $_SESSION['error'] ?? null;

// Unset the session messages after displaying them
unset($_SESSION['success'], $_SESSION['error']);

?>

<!DOCTYPE html>
<html>

<head>
    <title>E-ticketing Reset Password</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container">
        <form action="<?=  $ref === 'organizer' ? 'assets/php/organizer_action.php' : 'assets/php/action.php'?>" method="post" id="reset-passwd" class="form">
            <?= Utils::insertCsrfToken() ?>
            <h1>Reset Password</h1>
            <?= $message ?>

            <input type="hidden" name="email" value="<?= $email ?>">


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
                <button id="acc-login" type="submit" name="reset-passwd-btn" class="btn">Reset Password</button>
            </div>
        </form>
       
    </div>

    <!-- JS validations -->
    <script>
        // Get references to the form inputs
        const fnameEl =
            passwordEl = document.querySelector('[name="password"]'),
            confirmPasswordEl = document.querySelector('[name="confirm-password"]'),
            btn = document.querySelector('[name="reset-passwd-btn"]');

            const fieldLabels = {
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

            if (!checkPassword()) isValidForm = false;
            if (!checkPasswordMatch()) isValidForm = false;

            if (!isValidForm) {
                e.preventDefault(); // Prevent form submission if the form is invalid
            }
        });
    </script>
</body>

</html>
