<?php
    require_once "checkPermissions.php";
    isLogged();

	try {
        require_once "connect.php";
        global $db;
		$query = $db->prepare("SELECT * FROM products WHERE productId = :productId");
		$query->bindValue(':productId', $_GET['id'], PDO::PARAM_INT );
		$query->execute();
		
		if($query->rowCount() != 0) {
			$result = $query->fetch(PDO::FETCH_ASSOC);
		} else {
//			echo "Nie znaleziono produktu o podanym id<br>";
//			echo '<a href="index.php">Powrot do strony glownej</a>';
			header('Location: show_products_list.php');
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
	
		<div class="row my-3">
			<div class="col-md-8 h2 text-center ">
                <?php echo $result["productName"]; ?>
            </div>
            <div class="col-md-4">
			    <form method="POST" action="edit_product.php" class="form-group">
                    <input type="hidden" name="productId" value="<?php echo $_GET['id']; ?>">
                    <input type="submit" class="btn btn-outline-primary col-12" value="Edytuj produkt">
                </form>
			</div>
		</div>
		
		<div class="row">
            <div class="col-md-8">
                <table class="table table-dark table-striped">
                    <tr><th>Cena NETTO</th><td><?php echo $result["netPrice"].add_zeros($result["netPrice"]);?> PLN</td></tr>
                    <tr><th>VAT</th><td><?php echo $result["tax"];?>%</td></tr>
                    <tr><th>Cena BRUTTO</th><td><?php echo brutto($result["netPrice"], $result["tax"]).add_zeros(brutto($result["netPrice"], $result["tax"]));?> PLN</td></tr>
                    <tr><th>Ilość</th><td><?php echo $result["amount"]." ".$result["unitOfMeasure"];?></td></tr>
                </table>

                <form method="POST" action="remove_product.php" class="form-group pt-3">
                    <input type="hidden" name="productId" value="<?php echo $_GET["id"]; ?>">
                    <input type="submit" class="btn btn-outline-danger btn-block" value="Usuń produkt">
                </form>

			</div>
			<div class="col-md-4 text-center">
			    <?php if(!$result["photo"] == NULL){echo '<img class="img-fluid rounded" src="upload/'.$result["photo"].'">';} else echo "I`m a photo";?>
			</div>
		</div>
		

	</div>

	<?php require_once "html_elements/ending.php"?>
	