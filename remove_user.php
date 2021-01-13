<?php
    isAdministator();

	if(!isset($_POST['login'])) {
		header('Location: index.php');
		exit();
	}
	
	try {
        $login = $_POST['login'];
        require_once "connect.php";
        global $db;
        $query = $db->prepare("DELETE FROM users WHERE login = :login");
        $query->bindValue(':login', $login, PDO::PARAM_INT );
        $query->execute();

        header("Location: show_users_list.php");
        exit();
	} catch(Exception $e) {
		echo $e->getMessage();
	}