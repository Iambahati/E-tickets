<?php

require_once  '../assets/php/organizer_functions.php';
require_once  '../assets/php/client_functions.php';

require_once '../utils.php';


if (!isset($_SESSION['clientName']) && !isset($_SESSION['accRole'])) {

  Utils::redirect_to('../index.php');
}

$fullname = $_SESSION['clientName'];

$userId = $_SESSION['userId'];

$role = $_SESSION['accRole'];
// coalescing operator `??`
// checks if a variable exists and is not null,
// and if it doesn't, it returns a default value
$message = $_SESSION['success'] ?? $_SESSION['error'] ?? null;
// `unset()` function destroys a variable. Once a variable is unset, it's no longer accessible
unset($_SESSION['success'], $_SESSION['error']);

$interfaces = $role == 2 ? new Organizer() : new Client();

$userData = $interfaces->fetchUserDetails($userId);

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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendees</title>
  <link rel="stylesheet" href='../assets/css/dash.css'>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>
   /* Form Container */
.profile-form {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

/* Form Row Layout */
.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

/* Form Groups */
.form-group {
    flex: 1;
    margin-bottom: 20px;
}

/* Labels */
.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-weight: 500;
    color: #666;
}

/* Inputs */
.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    transition: all 0.3s ease;
}

/* Readonly state */
.form-group input[readonly] {
    background-color: #f5f5f5;
    color: #666;
    cursor: default;
    border: 1px solid #ddd;
}

/* Active/Editable state */
.form-group input:not([readonly]) {
    background-color: white;
    border-color: #4CAF50;
}

.form-group input:hover:not([readonly]) {
    border-color: #4CAF50;
}

.form-group input:focus:not([readonly]) {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
}

/* Buttons */
.btn-edit, .btn-submit {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

/* Edit button */
.btn-edit {
    background-color: #2196F3;
    color: white;
}

.btn-edit:hover {
    background-color: #1976D2;
}

/* Submit button */
.btn-submit {
    background-color: #4CAF50;
    color: white;
}

.btn-submit:hover {
    background-color: #45a049;
}

/* Icons in buttons */
.btn-edit i, .btn-submit i {
    font-size: 20px;
}
  </style>


</head>

<body>
  <!-- =============Sidebar Start================ -->
  <div class="sidebar">
    <a href="#" class="logo">
      <img src="./img/logo-1.png" alt="" />
      <span>E-</span>Ticketing
    </a>
    <ul class="side-menu">
      <li class="active">
        <a href="<?= $role == 2 ? 'org-dashboard.php' : 'events.php' ?>"><i class="bx bxs-dashboard"></i>Dashboard</a>
      </li>

      <li>
        <a href="../assets/php/logout.php" class="logout"><i class="bx bx-log-out-circle"></i>Logout</a>
      </li>
    </ul>
  </div>
  <!-- =============Sidebar Close================ -->
  <div class="content">
    <nav>
      <i class="bx bx-menu"></i>
      <form action="#">
        <div class="form-input">
          <input type="search" placeholder="Search................" />
          <button type="submit"><i class="bx bx-search"></i></button>
        </div>
      </form>

      <div class="profile-details">
        <img src="../assets/images/avatar.png" alt="" class="rounded-circle">
        <span class="full_name"><?= $fullname; ?></span>
        <i class='fa-solid fa-caret-down'></i>
        <div class="dropdown-menu" id="dropdown-menu">
          <a class="dropdown-item" href="#">
            <i class='bx bxs-user bx-flip-horizontal'></i>
            <span class="links_name">Profile</span>
          </a>
          <a class="dropdown-item logout" href="../assets/php/logout.php">
            <i class="bx bx-log-out-circle"></i>
            <span class="links_name">Logout</span>
          </a>
        </div>
      </div>
    </nav>


    <!-- Main Start -->
    <main>
      <!--============= bottom Data Start ===============-->
      <div class="bottom_data profile">
        <div class="orders">
          <div class="header">
            <h3>Profile</h3>
            <?= $message ?>
          </div>
          <form class="profile-form" action="<?= $role == 2 ? '../assets/php/organizer_action.php' : '../assets/php/action.php' ?>" method="POST">
    <?= Utils::insertCsrfToken() ?>  
    <input type="hidden" name="userId" value="<?= $userId ?>">

    <?php if ($role == 2) : ?>
      <div class="form-row">
        <div class="form-group">
            <label for="organisationName">Organisation Name:</label>
            <input type="text" id="orgName" name="orgName" value="<?= $userData['organization_name'] ?>" required readonly>
        </div>
    </div>

    <?php else : ?>

      <div class="form-row">
        <div class="form-group">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" value="<?= $userData['first_name'] ?>" required readonly>
        </div>
        <div class="form-group">
            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" value="<?= $userData['last_name'] ?>" required readonly>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= $userData['email'] ?>" required readonly>
    </div>

    <div class="form-group">
        <label for="contact">Contact:</label>
        <input type="text" id="contact" name="contact" value="<?= $userData['contact'] ?>" required readonly>
    </div>

    <div class="form-group">
        <button type="button" id="editButton" class="btn-edit">
            <i class='bx bx-edit'></i> Edit Profile
        </button>
        <button type="submit" name="update-profile-btn" class="btn-submit" style="display: none;">
            <i class='bx bx-save'></i> Update Profile
        </button>
    </div>
</form>

        </div>

      </div>
      <!--============= bottom Data Start ===============-->
    </main>
    <!-- Main Close -->
  </div>
  <!-- =============Content CLose================ -->

  <script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.profile-form');
    const inputs = form.querySelectorAll('input:not([type="hidden"])');
    const editButton = document.getElementById('editButton');
    const submitButton = document.querySelector('.btn-submit');

    editButton.addEventListener('click', function() {
        const isEditing = editButton.classList.contains('editing');
        
        if (!isEditing) {
            // Enable editing
            inputs.forEach(input => {
                input.removeAttribute('readonly');
            });
            editButton.style.display = 'none';
            submitButton.style.display = 'flex';
            editButton.classList.add('editing');
        }
    });

    // Optional: Cancel edit
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && editButton.classList.contains('editing')) {
            inputs.forEach(input => {
                input.setAttribute('readonly', true);
            });
            editButton.style.display = 'flex';
            submitButton.style.display = 'none';
            editButton.classList.remove('editing');
        }
    });
});
</script>




</body>

</html>