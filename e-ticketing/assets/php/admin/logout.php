<?php
require_once '../../utils.php';

if (isset($_SESSION['accRole'])) {

    // Remove only the session variables specific to the current user
    unset($_SESSION['accRole']);
    unset($_SESSION['clientName']);
    session_destroy();
    Utils::redirect_to("../../index.php");
}
