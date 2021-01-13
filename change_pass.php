<?php
    require_once 'c_user.php';
	session_start();

	if(!isset($_SESSION['user'])) {
		header('Location:login.php');
		exit();
	} else {
		try {
//			print_r($_SESSION);
//			print_r($_SESSION['user']);
            $user_object = unserialize($_SESSION['user']);

			if(!$user_object->active) {
				if(isset($_POST['pass'])) {
					$all_ok = true;

					$login =  $user_object->getLogin();
					$pass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);
					$new_pass = filter_input(INPUT_POST, 'new_pass', FILTER_SANITIZE_STRING);
					$new_pass2 = filter_input(INPUT_POST, 'new_pass2', FILTER_SANITIZE_STRING);

					if(strlen($pass) < 8 || strlen($pass) > 25) {
						$all_ok = false;
						$_SESSION['e_pass'] = 'Nie poprawne hasło';
					}

					if(strlen($new_pass2) < 8 || strlen($new_pass2) > 25) {
						$all_ok = false;
						$_SESSION['e_new_pass'] = 'Powtórzone hasło nie jest poprawne';
					}

					if(strcmp($new_pass, $new_pass2) !== 0) {
						$all_ok = false;
						$_SESSION['e_new_pass'] = 'Hasła nie są identyczne';
					}

					if(strlen($new_pass) < 8 || strlen($new_pass) > 25) {
						$all_ok = false;
						$_SESSION['e_new_pass'] = 'Nowe hasło powinno zawierać od 8 do 25 znaków';
					}

					if(strcmp($pass, $new_pass) == 0) {
						$all_ok = false;
						$_SESSION['e_new_pass'] = 'Nowe hasło musi być różne od poprzedniego';
					}

					if($all_ok == true) {
						if(password_verify($pass, $user_object->getPassword())) {
							$date = new DateTime();
                            require_once "connect.php";
                            global $db;

							$query = $db->prepare('INSERT INTO logs VALUES (:login, :active)');
							$query->bindValue(':active', 1, PDO::PARAM_INT);
							$query->bindValue(':login',  $user_object->getLogin(), PDO::PARAM_STR);
							$query->execute();

                            $query = $db->prepare('UPDATE users SET password = :pass, activepassword = :active WHERE login = :login');
                            $query->bindValue(':pass', password_hash($new_pass, PASSWORD_DEFAULT), PDO::PARAM_STR);
                            $query->bindValue(':active', 1, PDO::PARAM_INT);
                            $query->bindValue(':login',  $user_object->getLogin(), PDO::PARAM_STR);
                            $query->execute();

                            $_SESSION['logged'] = true;
							$_SESSION['a_success'] = 'Hasło zostało zmienione';
							header('Location: index.php');
						} else {
							$_SESSION['e_pass'] = 'Nie poprawne hasło';
						}
					}

					$_SESSION['login'] = $login;
					$_SESSION['pass'] = $pass;
					$_SESSION['new_pass'] = $new_pass;
					$_SESSION['new_pass2'] = $new_pass2;
				}
			} else {
				header('Location:login.php');
				exit();
			}
		} catch(Exception $e) {
			$_SESSION['error'] = $e->getMessage();
		}
	}
	
	require_once 'html_elements/head.php';
	require_once 'html_elements/navbar.php';

?>

	<a href="logout.php" class="text-link">powrót do strony logowania</a>
	
	<div class="container">
		<div class="row">
			<div class="col-md-8 mx-auto">
				<h2 class="h2 text-center text-warning mt-5">Wymagana zmiana hasła</h2>
				<form method="POST">
				
					<div class="form-group">
						<label for="login" class="mb-1">Login</label>
						<input type="text" name="login" id="login" placeholder="login" class="form-control" disabled
						<?php if(isset($user_object)){echo 'value="'.$user_object->getLogin().'"'; unset($user_object);}?>>
					</div>
					
					<div class="form-group">
						<label for="pass" class="mb-1">Hasło</label>
						<input type="password" name="pass" id="pass" placeholder="hasło" class="form-control" <?php if(isset($_SESSION['pass'])){echo 'value="'.$_SESSION['pass'].'"'; unset($_SESSION['pass']);}?>>
						<?php if(isset($_SESSION['e_pass'])){echo '<span class="text-danger float-right mb-0 mt-1">'.$_SESSION['e_pass'].'</span>'; unset($_SESSION['e_pass']);}?>
					</div>
					
					<div class="form-group">
						<label for="new_pass" class="mb-1">Nowe hasło</label>
						<input type="password" name="new_pass" id="new_pass" placeholder="nowe hasło" class="form-control" <?php if(isset($_SESSION['new_pass'])){echo 'value="'.$_SESSION['new_pass'].'"'; unset($_SESSION['new_pass']);}?>>
						<?php if(isset($_SESSION['e_new_pass'])){echo '<span class="text-danger float-right mb-0 mt-1">'.$_SESSION['e_new_pass'].'</span>'; unset($_SESSION['e_new_pass']);}?>
					</div>
					
					<div class="form-group">
						<label for="new_pass2" class="mb-1">Powtórz hasło</label>
						<input type="password" name="new_pass2" id="new_pass2" placeholder="powtórz hasło" class="form-control" <?php if(isset($_SESSION['new_pass2'])){echo 'value="'.$_SESSION['new_pass2'].'"'; unset($_SESSION['new_pass2']);}?>>
					</div>	
					
					<input type="submit" class="btn btn-success my-2 float-right" value="Zmień hasło">
		
				</form>
				<?php if(isset($_SESSION['error'])){echo '<span class="text-danger">'.$_SESSION['error'].'</span>'; unset($_SESSION['error']);}?>
				
			</div>
		</div>
	</div>
	
<?php 	require_once 'html_elements/ending.php'; ?>