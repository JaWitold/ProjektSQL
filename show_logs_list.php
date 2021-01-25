<?php
    require_once "checkPermissions.php";
    isAdministator();

    require_once "html_elements/head.php";
    require_once "html_elements/navbar.php";
    require_once "html_elements/currentUser.php";
?>
	
	<div class ="container">
	<div class="row">
		<div class="h2 my-3 col-md-12 text-center">wszystkie logi</div>
	</div>
	
	<?php
		try {
            require_once "connect.php";
            global $db;
			$query = $db->query('SELECT * FROM logs');

			if($query->rowCount() != 0) {
				$result = $query->fetchAll(PDO::FETCH_ASSOC);

				echo '<table class="table col-12 table-dark table-striped text-center"><tbody>';
				foreach($result as $r) {
					echo '<tr>';
                    foreach($r as $v) {
                        echo "<td>$v</td>";
                    }
					echo "</tr>";
				}
				echo '</tbody></table>';
			} else {
				echo "Brak tresci do wyswietlenia.";
			}
		} catch(Exception $e) {
			echo $e->getMessage();
		}
    ?>

    </div>
<?php
		require_once "html_elements/ending.php"
?>