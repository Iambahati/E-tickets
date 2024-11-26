<?php
require_once '../../utils.php';
require_once '../../mailer.php';
require_once 'client_functions.php';


// Create a Client instance
$action = new Client();

//Handle [register] request of buyer user
if (isset($_POST["client-signup-btn"])) {
    try {

        /**
         *  ------------------------
         *   Verifying CSRF token
         *  ------------------------
         */
        if (!Utils::verifyCsrfToken()) {
            Utils::redirect_with_message('../../index.php', 'error', 'Request could not be validated.');
        }

        $fname = isset($_POST["fname"]) && !empty($_POST["fname"]) ? Utils::sanitizeInput(ucwords($_POST["fname"])) : Utils::redirect_with_message('../../buyer_signup.php', 'error', 'First name cannot be blank!');
        $lname = isset($_POST["lname"]) && !empty($_POST["lname"]) ? Utils::sanitizeInput(ucwords($_POST["lname"])) : Utils::redirect_with_message('../../buyer_signup.php', 'error', 'Last name cannot be blank!');
        $email = isset($_POST["email"]) && !empty($_POST["email"]) ? strtolower(Utils::sanitizeInput($_POST["email"])) : Utils::redirect_with_message('../../buyer_signup.php', 'error', 'Email cannot be blank!');
        $pass = isset($_POST["password"]) && !empty($_POST["password"]) ? Utils::sanitizeInput($_POST["password"]) : Utils::redirect_with_message('../../buyer_signup.php', 'error', 'Password cannot be blank!');
        $phone = isset($_POST["phone"]) && !empty($_POST["phone"]) ? Utils::sanitizeInput($_POST["phone"]) : Utils::redirect_with_message('../../buyer_signup.php', 'error', 'Phone cannot be blank!');

        $hpass = password_hash($pass, PASSWORD_DEFAULT);

        $userExists = $action->user_exists($email);
        if ($userExists && $userExists['email'] === $email) {
            Utils::redirect_with_message('../../buyer_signup.php', 'error', 'A user with this email is already registered');
            return;
        }

        // Call the [registerBuyerAcc] method
        if ($action->createUserAccount($fname, $lname, $email, $phone, $hpass, '3')) {
            Utils::redirect_with_message('../../buyer_signin.php', 'success', 'Account created successfully. Please login to continue');
            return;
        }
    } catch (Exception $e) {
        // Handle exceptions by returning an error mailBody to user
        Utils::redirect_with_message('../../buyer_signup.php', 'error', 'Opps...Some error occurred: ' . $e->getMessage());
    }
}


//Handle [login] request of buyer user
if (isset($_POST["client-signin-btn"])) {
    try {

        /**
         *  ------------------------
         *   Verifying CSRF token
         *  ------------------------
         */
        if (!Utils::verifyCsrfToken()) {
            Utils::redirect_with_message('../../buyer_signin.php', 'error', 'Request could not be validated.');
        }

        $email = isset($_POST["email"]) && !empty($_POST["email"]) ? Utils::sanitizeInput($_POST["email"]) :  Utils::redirect_with_message('../../buyer_signin.php',  'error', 'Email cannot be blank!');
        $pass = isset($_POST["password"]) && !empty($_POST["password"]) ? Utils::sanitizeInput($_POST["password"]) : Utils::redirect_with_message('../../buyer_signin.php', 'error', 'Password cannot be blank!');

        // Call the [login] method for buyer user
        $result = $action->loginIntoAccount($email);

        if (!empty($result)) {
            if ($result['role'] !== '3') {
                Utils::redirect_with_message('../../buyer_signin.php', 'error', 'User not found.');
            }
            // If passwords match, login the user and redirect to the appropriate dashboard
            if (password_verify($pass, $result['password'])) {
                $_SESSION['clientName'] = $result['first_name'] . ' ' . $result['last_name'];
                $_SESSION['userId'] = $result['id'];
                $_SESSION['accRole'] = $result['role'];
                // $logger->log('User  "' . $_SESSION['staffName'] . '" has logged in', 'Log In', $_SESSION['svcNo']);

                switch ($_SESSION['accRole']) {
                    case 1:
                        Utils::redirect_to("../../interfaces/admin.php");
                        break;
                    case 2:
                        Utils::redirect_to("../../interfaces/org-dashboard.php");
                        break;
                    case 3:
                        Utils::redirect_to("../../interfaces/events.php");
                        break;
                    default:
                        break;
                }
            } else {
                // If password doesn't match, redirect back to login page with error message
                Utils::redirect_with_message('../../buyer_signin.php', 'error', 'Wrong email or password.');
            }
        } else {
            // If no such user exists, redirect back to login page with error message
            Utils::redirect_with_message('../../buyer_signin.php', 'error', 'User not found.');
        }
    } catch (Exception $e) {
        // Handle exceptions by returning an error to the user
        Utils::redirect_with_message('../../buyer_signin.php', 'error', 'Oops... Some error occurred: ' . $e->getMessage());
    }
}

if (isset($_POST['update-profile-btn'])) {
    try {
        if (!Utils::verifyCsrfToken()) {
            Utils::redirect_with_message('../../interfaces/profile.php', 'error', 'Request could not be validated.');
        }

        $fields = [
            'userId' => 'User ID',
            'firstName' => 'First name',
            'lastName' => 'Last name',
            'email' => 'Email',
            'contact' => 'Contact'
        ];

        $data = [];
        foreach ($fields as $field => $label) {
            $data[$field] = isset($_POST[$field]) && !empty($_POST[$field])
                ? Utils::sanitizeInput($_POST[$field])
                : Utils::redirect_with_message('../../interfaces/profile.php', 'error', $label . "cannot be blank!");
        }

        extract($data);


        $client = new Client();
        if ($client->updateUserDetails($userId, $firstName, $lastName, $email, $contact)) {
            Utils::redirect_with_message('../../interfaces/profile.php', 'success', 'Profile updated successfully.');
        } else {
            Utils::redirect_with_message('../../interfaces/profile.php', 'error', 'Failed to update profile.');
        }
    } catch (Exception $e) {
        Utils::redirect_with_message('../../interfaces/profile.php', 'error', 'An error occurred: ' . $e->getMessage());
    }
}

if (isset($_POST["forgot-passwd-btn"])) {
    try {
        if (!Utils::verifyCsrfToken()) {
            Utils::redirect_with_message('../../forgot_password.php', 'error', 'Request could not be validated.');
        }

        $email = isset($_POST["email"]) && !empty($_POST["email"]) ? Utils::sanitizeInput($_POST["email"]) : Utils::redirect_with_message('../../forgot_password.php', 'error', 'Email cannot be blank!');

        $client = new Client();
        $user = $client->user_exists($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $client->passwordResetToken($email, $token);

            $mailBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; color: #333;'>
                <div style='text-align: center; margin-bottom: 30px;'>
                    <h2 style='color: #4CAF50; margin-bottom: 10px;'>Password Reset Request</h2>
                </div>
            
                <p style='margin-bottom: 20px; line-height: 1.5;'>Hello,</p>
            
                <p style='margin-bottom: 20px; line-height: 1.5;'>
                    We have received a request to reset your password for your E-Tickets account. 
                    To proceed with resetting your password, please click the button below:
                </p>
            
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='http://localhost/E-tickets/e-ticketing/reset_password.php?token=" . $token . "&email=" . $email . "&ref=client' 
                       style='background-color: #4CAF50; 
                              color: white; 
                              padding: 12px 30px; 
                              text-decoration: none; 
                              border-radius: 5px; 
                              display: inline-block; 
                              font-weight: bold;
                              font-size: 16px;'>
                        Reset Password
                    </a>
                </div>
            
                <p style='margin-bottom: 20px; line-height: 1.5; color: #666; font-size: 14px;'>
                    If you did not request this password reset, please ignore this email and your password will remain unchanged.
                </p>
            
                <div style='margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #666; font-size: 12px;'>
                    <p>This is an automated email, please do not reply.</p>
                </div>
            </div>";

            if (Mailer::sendMail($email, 'Password Reset', $mailBody)) {
                Utils::redirect_with_message('../../forgot_password.php', 'success', 'If an account with this email exists, a password reset link will be sent to it.');
            } else {
                Utils::redirect_with_message('../../forgot_password.php', 'error', 'Failed to send password reset link.');
            }
        } else {
            Utils::redirect_with_message('../../forgot_password.php', 'error', 'If an account with this email exists, a password reset link will be sent to it.');
        }
    } catch (Exception $e) {
        Utils::redirect_with_message('../../forgot_password.php', 'error', 'An error occurred: ' . $e->getMessage());
    }
}


if (isset($_POST["reset-passwd-btn"])) {
    try {
        if (!Utils::verifyCsrfToken()) {
            Utils::redirect_with_message('../../forgot_password.php', 'error', 'Request could not be validated.');
        }

        $email = isset($_POST["email"]) && !empty($_POST["email"]) ? Utils::sanitizeInput($_POST["email"]) : Utils::redirect_with_message('../../reset_password.php', 'error', 'Email cannot be blank!');

        $pass = isset($_POST["password"]) && !empty($_POST["password"]) ? Utils::sanitizeInput($_POST["password"]) : Utils::redirect_with_message('../../reset_password.php', 'error', 'Password cannot be blank!');
        $hpass = password_hash($pass, PASSWORD_DEFAULT);

        $client = new Client();
        if ($client->resetPassword($email, $hpass)) {
            Utils::redirect_with_message('../../buyer_signin.php', 'success', 'Password reset successful. Please login to continue.');
        } else {
            Utils::redirect_with_message('../../reset_password.php', 'error', 'Failed to reset password.');
        }
    } catch (Exception $e) {
        Utils::redirect_with_message('../../forgot_password.php', 'error', 'An error occurred: ' . $e->getMessage());
    }
}


if (isset($_POST["eventId"]) && isset($_POST["userId"]) && isset($_POST["number_of_tickets"])) {
    try {
        if (!Utils::verifyCsrfToken()) {
            Utils::redirect_with_message('../../interfaces/events.php', 'error', 'Request could not be validated.');
            exit;
        }

        $userId = Utils::sanitizeInput($_POST["userId"]);
        $eventId = Utils::sanitizeInput($_POST["eventId"]);
        $no_of_tickets = Utils::sanitizeInput((int)$_POST["number_of_tickets"]);

        // Validate that all required fields have values
        if (empty($userId) || empty($eventId) || $no_of_tickets < 1) {
            $missingFields = [];
            if (empty($userId)) $missingFields[] = 'userId';
            if (empty($eventId)) $missingFields[] = 'eventId';
            if ($no_of_tickets < 1) $missingFields[] = 'number_of_tickets';

            Utils::redirect_with_message('../../interfaces/event.php', 'error', 'The following fields are invalid: ' . implode(', ', $missingFields));
            exit;
        }

        // Process the order
        if ($action->orders($userId, $eventId, $no_of_tickets)) {
            Utils::redirect_with_message('../../interfaces/events.php', 'success', 'Purchase successful.');
        } else {
            Utils::redirect_with_message('../../interfaces/events.php', 'error', 'Purchase failed.');
        }
    } catch (Exception $e) {
        Utils::redirect_with_message('../../interfaces/event.php', 'error', 'An error occurred: ' . $e->getMessage());
    }
} else {
    $eventId = isset($_POST["eventId"]) ? Utils::sanitizeInput($_POST["eventId"]) : '';
    Utils::redirect_with_message('../../interfaces/event.php?event_id=' . $eventId, 'error', 'An error occurred: Missing required form data');
}
