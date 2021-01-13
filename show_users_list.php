<?php
    require_once "checkPermissions.php";
    isAdministator();

    require_once "html_elements/head.php";
    require_once "html_elements/navbar.php";
    require_once "html_elements/currentUser.php";
?>
	
	<div class ="container">
	<div class="row">
		<div class="h2 col-md-6">wszyscy pracownicy</div>
		<div class="h2 col-md-6 text-right"><a href="add_new_user.php" class="btn btn-success">Dodaj pracownika</a></div>
	</div>
	
	<?php

		try {
            require_once "connect.php";
            global $db;
			$query = $db->query('SELECT login, userName, userSurname, role FROM users');

			if($query->rowCount() != 0) {
				$result = $query->fetchAll(PDO::FETCH_ASSOC);

				echo '<table class="table table-dark table-striped text-center"><thead><tr><td>Imie i Nazwisko</td><td>Rola</td></tr></thead><tbody>';
				foreach($result as $r) {
					echo '<tr><td><a href="show_user_details.php?login='.$r["login"].'">'.$r["userName"]." ".$r["userSurname"]."</a></td>";
					echo "<td>".$r["role"]."</td>";
					echo "</tr>";
				}
				echo '</tbody></table>';
			} else {
				echo "Brak tresci do wyswietlenia.";
			}
		} catch(Exception $e) {
			echo $e->getMessage();
		}

		require_once "html_elements/ending.php"
?>