<?php
    require_once "checkPermissions.php";
    isLogged();
	
	try {
		$user = new User($_GET["login"]);

        require_once "connect.php";
		global $db;
		$query = $db->prepare("SELECT * FROM users WHERE login = :login");
		$query->bindValue(':login', $_GET['login'], PDO::PARAM_INT );
		$query->execute();
		
		if($query->rowCount() != 0) {
			$result = $query->fetch(PDO::FETCH_ASSOC);
		} else {
		    header("Location: index.php");
//			echo "Nie znaleziono pracownika o podanym loginie<br>";
//			echo '<a href="index.php">Powrót do strony głównej</a>';
			exit();
		}
	} catch(Exception $e) {
		echo $e->getMessage();
	}


    require_once "html_elements/head.php";
    require_once "html_elements/navbar.php";
    require_once "html_elements/currentUser.php";
?>

	<div class ="container">
	
		<div class="row">
			<div class="h2 col-12 text-center my-3">Dane pracownika</div>
		</div>
		
		<div class="row">
            <div class="col-md-10 offset-md-1">
                <?php echo $user->printUserData(); ?>
		    </div>
		</div>

		<div class ="row col-md-10 offset-md-1">
		    <?php if(!strcmp($currentUser->role, "Administrator") ) {
		    echo '<form class="col-md-6 d-block" method="POST" action="remove_user.php"><input type="hidden" name="login" value="'.$_GET["login"].'"><input type="submit" class="btn btn-outline-danger btn-block" value="usuń dane pracownika"></form>';
		    echo '<form class="col-md-6 d-block" method="POST" action="edit_user.php"><input type="hidden" name="login" value="'.$_GET["login"].'"><input type="submit" class="btn btn-outline-primary btn-block" value="edytuj dane pracownika"></form>'; }?>
		</div>
	</div>
	
<?php require_once "html_elements/ending.php";	?>