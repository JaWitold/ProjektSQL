<?php
    require_once "checkPermissions.php";
    isLogged();

	if(!isset($_POST['productId'])) {
		header('Location:show_products_list.php');
		exit();
	}
	
	try {
        $productId = $_POST['productId'];
        require_once "connect.php";
        global $db;
        $query = $db->prepare("DELETE FROM products WHERE productId = :productId");
        $query->bindValue(':productId', $productId, PDO::PARAM_INT );
        $query->execute();
	
        header("Location: show_products_list.php");
        exit();
	} catch(Exception $e) {
		echo $e->getMessage();
	}
