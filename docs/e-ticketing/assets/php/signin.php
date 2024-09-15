<?php

/* TO DO LIST FOR THIS FILE

1. Add comments on all pdo stuff
2. Explain session
3. Explain isset

*/

session_start();
include 'connect/config.php';

// Handle form submission
if (isset($_POST["signin"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Retrieve user from database
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $hashed_password = md5($password);

        if ($user['password'] == $hashed_password) {
             // Check user role
             if ($user['roles_id'] == 1) {
                // Redirect admin to admin dashboard
                header("Location: admin.php");
                exit();
            } else {
                // Redirect other users to user dashboard
                header("Location: events.php");
                exit();
            }
        } else {
            echo "Invalid password";
        }
    } else {
        echo "Invalid email";
    }
}
?>


