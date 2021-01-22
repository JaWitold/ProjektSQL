<?php
    session_start();

    function isLogged(){
        if(!isset($_SESSION['logged'])) {
            header('Location:login.php');
            exit();
        }
        if(!isset($_SESSION['user'])) {
            header("Location: logout.php");
            exit();
        }
    }

    function isAdministator(){
        require_once "c_user.php";
        $currentUser = unserialize($_SESSION['user']);
        isLogged();
        if(strcmp($currentUser->getRole(),"Administrator")) {
            header('Location:login.php');
            exit();
        }
    }

    function isAccountant() {
        isLogged();
        require_once "c_user.php";
        $currentUser = unserialize($_SESSION['user']);

        if(!showForAccountant() && !showForAdmin()) {
            header('Location:login.php');
            exit();
        }
    }

    function showForAdmin(): bool
    {
        isLogged();
        require_once "c_user.php";

        $currentUser = unserialize($_SESSION['user']);
        return !strcmp($currentUser->getRole(),"Administrator");
    }

    function showForAccountant(): bool
    {
        isLogged();
        require_once "c_user.php";

        $currentUser = unserialize($_SESSION['user']);
        return !strcmp($currentUser->getRole(),"Ksiegowy");
    }