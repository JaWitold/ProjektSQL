<?php
    require_once "checkPermissions.php";
    isAdministator();

    require_once "c_user.php";
    $currentUser = unserialize($_SESSION['user']);


	try {
		if(isset($_POST["name"])) {

			$all_ok = true;
			$user = new User();
			//na wszelki wypadek niech imie i nazwisko bedzie pisane dużą literą programowo
			$user->setName(ucfirst(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING)));
			$user->setSurname(ucfirst(filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING)));
			$user->setRole(filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING));
			$user->setDate((new DateTime())->format("Y-m-d H:i:s"));
			$user->deactivate();
			
			$_SESSION['name'] = $user->getName();
			$_SESSION['surname'] = $user->getSurname();
			$_SESSION['role'] = $user->getRole();
			
			if($user->getName() != ucfirst($_POST["name"])) {
                $all_ok = false;
				throw new Exception("Nie poprawne imie");
			}
			
			if(strlen($user->getName()) < 3) {
                $all_ok = false;
				throw new Exception("Podane imie jest za krotkie");
			}

			if($user->getSurname() != ucfirst($_POST["surname"])) {
                $all_ok = false;
				throw new Exception("Nie poprawne nazwisko");
			}
			
			if(strlen($user->getSurname()) < 3) {
                $all_ok = false;
                throw new Exception("Podane nazwisko jest za krotkie");
			}

			global $roles;
			if(!in_array($user->getRole(), $roles)) {
                $all_ok = false;
                throw new Exception("Podano błędne stanowisko pracy");
			}
			
			if($all_ok) {
				$user->generateLogin();
				$user->generatePass();
				
				require_once "connect.php";
				global $db;
				$query = $db->prepare("INSERT INTO users(userName, userSurname, login, password, role, dateOfAdd, activePassword) VALUES (:name, :surname, :login, :pass, :role, :date, :active)");

                $_SESSION["login"] = $user->getLogin();
                $_SESSION["pass"] = $user->getPassword();

                $date = new DateTime();

				$query->bindValue(":name", $user->getName(), PDO::PARAM_STR);
				$query->bindValue(":surname", $user->getSurname(), PDO::PARAM_STR);
				$query->bindValue(":login", $_SESSION["login"], PDO::PARAM_STR);
				$query->bindValue(":pass", password_hash($_SESSION["pass"], PASSWORD_DEFAULT), PDO::PARAM_STR);
				$query->bindValue(":role", $user->getRole(), PDO::PARAM_STR);
				$query->bindValue(":date", $date->format("Y-m-d"), PDO::PARAM_STR);
				$query->bindValue(":active", $user->isActive(), PDO::PARAM_INT);
				$query->execute();

				unset($_SESSION["name"]);
				unset($_SESSION["surname"]);
				unset($_SESSION["role"]);
				
				header("Location: show_credentials.php");
			}
		}
	} catch (Exception $e) {
		$_SESSION['error'] = $e->getMessage();
	}

    require_once "html_elements/head.php";
    require_once "html_elements/navbar.php";
    require_once "html_elements/currentUser.php";
?>
	<div class="container">
        <div class="row">
            <div class="h2 my-3 py-2 col-md-10 offset-md-1 text-center">Dodaj nowego pracownika</div>
        </div>

        <form class="form-group col-md-10 offset-md-1" method="POST">

            <div class="form-row">
                <div class="input-group mb-3 col">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="name">Imie</label>
                    </div>
                    <input type="text" name="name" id="name" <?php if(isset($_SESSION['name'])) {echo 'value="'.$_SESSION['name'].'"'; unset($_SESSION['name']); }?> placeholder="name" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="input-group mb-3 col">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="surname">Nazwisko</label>
                    </div>
                    <input type="text" name="surname" id="surname" <?php if(isset($_SESSION['surname'])) {echo 'value="'.$_SESSION['surname'].'"'; unset($_SESSION['surname']);}?> placeholder="surname" class="form-control">
                </div>
            </div>

            <div class="form-row">
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
            </div>

            <div class="form-row">
                <div class="input-group col">
                    <?php if(isset($_SESSION["error"])){echo '<span style="color:red;">'.$_SESSION["error"].'</span>'; unset($_SESSION["error"]);}?>

                    <input type="submit" class="btn btn-outline-success btn-block my-3" value="Dodaj nowego pracownika">
                </div>
            </div>
        </form>
    </div>

	<?php require_once "html_elements/ending.php"; ?>