<?php
require_once "c_user.php";
session_start();
	
	if(isset($_SESSION["user"]) && !isset($_SESSION["logged"])) {
		header("Location:change_pass.php");
		exit();
	} else if(isset($_SESSION['logged'])) {
		header('Location:index.php');
		exit();
	} else {
		if(isset($_POST['login'])) {
            $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
            $password =  filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

            $log_in = true;

            try {
                if(strlen($login) < 4 || strlen($login) > 20) {
                    $log_in = false;
                    throw new Exception("Nie poprawny login lub hasło");
                }

                if(strlen($password) < 8 || strlen($password) > 25) {
                    $log_in = false;
                    throw new Exception("Nie poprawny login lub hasło");
                }

                if($log_in == true) {
                    require_once "connect.php";
                    global $db;
                    $querry = $db->prepare("SELECT * FROM users WHERE login = :login");
                    $querry->bindValue(':login', $login, PDO::PARAM_STR);
                    $querry->execute();

                    if($querry->rowCount() !== 1) {
                        $log_in = false;
                        throw new Exception("Nie poprawny login lub hasło" );
                    } else {
                        $result = $querry->fetch(PDO::FETCH_ASSOC);

                        if(password_verify($password, $result['password'])) {
                            require_once 'connect.php';
                            $query = $db->prepare("SELECT DISTINCT * FROM logs WHERE login = :login ORDER BY dateOfLastLogin DESC LIMIT 1");
                            $query->bindValue(':login', $login, PDO::PARAM_STR);
                            $query->execute();
                            $resultDate = $query->fetch(PDO::FETCH_ASSOC);

                            $diff = 0;
                            $date = new DateTime();
                            if($query->rowCount() == 1) {
                                $prev_login = DateTime::createFromFormat("Y-m-d H:i:s", $resultDate["dateOfLastLogin"]);
                                $diff = $date->diff($prev_login, true);
                            } else {
                                $diff = $date->diff($date, true);
                            }
//                          echo $diff->format("%R%a");
//                          exit();

                            $_SESSION['user'] = serialize(new User($login));
                            $_SESSION['logged'] = true;

                            if(!$result["activePassword"]) {
                                unset($_SESSION["logged"]);
                                header("Location:change_pass.php");
                            } else if($diff->format("%R%a") > 30) {
                                unset($_SESSION["logged"]);
                                $query = $db->prepare("UPDATE users SET activePassword = 0 WHERE login = :login");
                                $query->bindValue(":login", $result['login'], PDO::PARAM_STR);
                                $query->execute();

                                header("Location:change_pass.php");
                            } else {
                                $query = $db->prepare("INSERT INTO logs VALUES (:login, :date)");
                                $query->bindValue(":login", $login, PDO::PARAM_STR);
                                $query->bindValue(":date", $date->format("Y-m-d H:i:s"), PDO::PARAM_STR);
                                $query->execute();
                                header("Location:index.php");
                            }
                            exit();
                        } else {
                            //echo password_hash($password, PASSWORD_DEFAULT);
                            $log_in = false;
                            unset($login);
                            unset($password);
                            throw new Exception("Nie poprawny login lub hasło");
                        }
                    }
                }
            } catch(Exception $e) {
                $_SESSION["login_e"] = $e->getMessage();
            }
		}
	}

	require_once "html_elements/head.php";
?>

	<form method="POST" class="form-inline col-12">
        <div class="form-group m-1">
            <?php if(isset( $_SESSION['login_e'])) {echo '<span style="color:#ff0000;">' .$_SESSION['login_e'].'</span><br>'; unset($_SESSION['login_e']);}?>
            <label >Login <input type="text" name="login" placeholder="Login" value="JaWitold"></label>
        </div>
        <div class="form-group m-1">
            <label >Hasło <input type="password" name="password" value="qwerty123" placeholder="Hasło" ></label>
        </div>
		<input type="submit" value="Zaloguj się" class="ml-3 btn btn-outline-light">
		
	</form>

<?php require_once "html_elements/ending.php"; ?>