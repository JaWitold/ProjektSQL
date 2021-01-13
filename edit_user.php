<?php
    require_once "checkPermissions.php";
    isAdministator();

	try {
	    if(!isset($_POST['login'])){
            header('Location:index.php');
            exit();
        }

        $user = new User($_POST['login']);
		if(isset($_POST["name"])) {

		    global  $roles;
			if(strlen($_POST['name']) > 2 && strlen($_POST['name']) < 20) $user->setName($_POST['name']); else throw new Exception("Nie poprawna wartosc pola imie");
			if(strlen($_POST['surname']) > 2 && strlen($_POST['surname']) < 20) $user->setName($_POST['surname']); else throw new Exception("Nie poprawna wartosc pola nazwisko");
			if(in_array($_POST['role'], $roles))  $user->setRole($_POST['role']); else throw new Exception("Nie poprawna wartosc pola stanowisko");
			
			//na wszelki wypadek niech imie i nazwisko bedzie pisane dużą literą programowo
			$user->setName(ucfirst($user->getName()));
			$user->setSurname(ucfirst($user->getSurname()));

            require_once "connect.php";

            global $db;
			$query = $db->prepare("UPDATE users SET userName = :userName, userSurname = :userSurname, role = :role WHERE login = :login");
			$query->bindValue(":userName", $user->getName(), PDO::PARAM_STR);
			$query->bindValue(":userSurname", $user->getSurname(), PDO::PARAM_STR);
			$query->bindValue(":role", $user->getRole(), PDO::PARAM_STR);
			$query->bindValue(":login", $user->getLogin(), PDO::PARAM_INT);
			$query->execute();

			header("Location: show_users_list.php");
			exit();
		}
	
		$_SESSION['name'] = $user->getName();
		$_SESSION['surname'] = $user->getSurname();
		$_SESSION['role'] = $user->getRole();
	} catch (Exception $e) {
		$_SESSION['error'] = $e->getMessage();
	}

    require_once "html_elements/head.php";
    require_once "html_elements/navbar.php";
    require_once "html_elements/currentUser.php";
?>

    <div class="container">
        <div class="row">
            <div class="h2 my-3 py-2 col-md-10 offset-md-1 text-center">Edytuj dane pracownika</div>
        </div>

        <form class="form-group col-md-10 offset-md-1" method="POST">
	
        <input type="hidden" name="login" <?php if(isset($_POST['login'])) {echo 'value="'.$_POST['login'].'"'; }?>>

            <div class="input-group mb-3 col">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="name">Imie</label>
                </div>
                <input type="text" name="name" id="name" <?php if(isset($_SESSION['name'])) {echo 'value="'.$_SESSION['name'].'"'; unset($_SESSION['name']); }?> placeholder="name" class="form-control">
            </div>

            <div class="input-group mb-3 col">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="surname">Nazwisko</label>
                </div>
                <input type="text" name="surname" id="surname" <?php if(isset($_SESSION['surname'])) {echo 'value="'.$_SESSION['surname'].'"'; unset($_SESSION['surname']);}?> placeholder="surname" class="form-control">
            </div>

            <div class="input-group mb-3 col">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="role">Stanowisko</label>
                </div>
                <select name="role" id="role" class="form-control">
                    <?php
                    echo '<option value="Administrator"'; if(isset($_SESSION['role']) && $_SESSION['role'] == 'Administrator') {echo "selected";}echo '>Administrator</option>';
                    echo '<option value="Ksiegowy"'; if(isset($_SESSION['role']) && $_SESSION['role'] == 'Ksiegowy') {echo "selected";}echo '>Ksiegowy</option>';
                    echo '<option value="Pracownik"'; if(isset($_SESSION['role']) && $_SESSION['role'] == 'Pracownik') {echo "selected";}echo '>Pracownik</option>';
                    if(isset($_SESSION['role'])) unset($_SESSION['role']);
                    ?>
                </select>
            </div>
            <div class="input-group col">
                <?php if(isset($_SESSION["error"])){echo '<span style="color:red;">'.$_SESSION["error"].'</span>'; unset($_SESSION["error"]);}?>

                <input type="submit" class="btn btn-outline-success btn-block my-3" value="Zapisz zmiany">
            </div>
        </form>
	</div>
	
<?php require_once "html_elements/ending.php";	?>