<?php
    session_start();

    function isLogged(){
        if(!isset($_SESSION['logged'])) {
            header('Location:login.php');
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

    function isAccountant(){
        require_once "c_user.php";
        $currentUser = unserialize($_SESSION['user']);
        isLogged();
        if(!strcmp($currentUser->getRole(),"Ksiegowy") || !strcmp($currentUser->getRole(),"Administrator")) {
            header('Location:login.php');
            exit();
        }
    }