<?php
    require_once "checkPermissions.php";
    isAdministator();

	if(!isset($_SESSION['login'])) {
		header("Location: login.php");
		exit();
	}

	$login = $_SESSION["login"];
	$pass = $_SESSION["pass"];

	unset($_SESSION["login"]);
	unset($_SESSION["pass"]);

	require_once "html_elements/head.php";
	require_once "html_elements/navbar.php";

    require_once "html_elements/head.php";
    require_once "html_elements/navbar.php";
    require_once "html_elements/currentUser.php";
?>
	
	<div class ="container">
		<div class="row mt-3">
			<div class="col-12 text-center mx-auto">
				<span class="h1 text-success">Sukces!</span><br>
				<p class="text-success">Dodano pracownika</p>
				<p><?php echo "login: ".$login. " hasło: ".$pass;?></p>
				<small>Podaj te dane pracownikowi aby mógł aktywować swoje konto</small>
			</div>
		</div>
	</div>
	
<?php require_once "html_elements/ending.php";	?>