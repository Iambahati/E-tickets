<?php
require_once 'utils.php';

include ("connect/config.php");

// Handle form submission
if (isset($_POST["signup"])){
    $username = $_POST["username"];
    $email = $_POST["email"];
    $contact = $_POST["contact"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $role = 2;

 // Check if email already exists in database
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

 if ($stmt->rowCount() > 0) {
     echo "Email already exists";
 } else {
     // Insert user into database
     $hashed_password = md5($password);
     $query = "INSERT INTO users (username, email, contact, password, roles_id) VALUES (:username, :email, :contact, :password, :roles_id )";
     $stmt = $conn->prepare($query);
     $stmt->bindParam(':username', $username);
     $stmt->bindParam(':email', $email);
     $stmt->bindParam(':contact', $contact);
     $stmt->bindParam(':password', $hashed_password);
     $stmt->bindParam(':roles_id', $role);

     if ($stmt->execute()) {
        header("Location: interfaces/events.php");
        $_SESSION["msg"] = "User Successfully Registered";
     } else {
         echo "Error: " . $stmt->errorInfo()[2];
     }
 }
}


?>










   

