<?php

require_once  '../../utils.php';

if (isset($_SESSION['clientName']) && isset($_SESSION['accRole'])) {

    // Remove only the session variables specific to the current user
    unset($_SESSION['clientName']);
    unset($_SESSION['accRole']);

    session_destroy();

    Utils::redirect_to("../../index.php");
}
