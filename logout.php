<?php
    require_once "checkPermissions.php";
    isLogged();

    if(isset($_SESSION['logged'])) unset($_SESSION['logged']);
    if(isset($_SESSION['user'])) unset($_SESSION['user']);

    session_unset();

    isLogged();
