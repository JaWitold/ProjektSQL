<?php
    require_once "checkPermissions.php";
    isLogged();

    require_once "html_elements/head.php";
    require_once "html_elements/navbar.php";
    require_once "html_elements/currentUser.php";
?>
	
	<div class ="container">
	<div class="row">
		<div class="h2 col-6">wszystkie produkty</div>
		<div class="col-6"><a href="add_new_product.php" class="btn btn-success float-right">Dodaj nowy produkt</a></div>
	</div>
	
	<?php
		
		try {
            require_once "connect.php";
            global $db;
			$query = $db->query('SELECT * FROM products');
			
			if($query->rowCount() !=0) {
				$result = $query->fetchAll(PDO::FETCH_ASSOC);
				require_once "functions.php";
				echo '<table class="table table-dark table-striped text-center"><thead><tr><td>Nazwa</td><td>Cena NETTO</td><td>VAT</td><td>Cena Sprzedaży</td><td>Ilość</td></tr></thead><tbody>';
				foreach($result as $r) {
					echo '<tr class="my-auto"><td><a href="show_product.php?id='.$r['productId'].'">'.$r['productName']."</a></td>";
					echo "<td>".$r['netPrice'].add_zeros($r['netPrice'])." PLN</td>";
					echo "<td>".$r['tax']."% </td>";
					echo "<td>".$r['netPrice']*(1 + $r['tax']/100).add_zeros($r['netPrice']*(1 + $r['tax']/100))." PLN</td>";
					echo "<td>".$r['amount']."</td></tr>";
				}
				echo '</tbody></table>';
			} else {
				echo "Brak tresci do wyswietlenia. Skontaktuj sie z adminem.";
			}
		} catch(Exception $e) {
			echo $e->getMessage();
		}
		
	?>
	</div>
	
	<?php require_once "html_elements/ending.php"?>