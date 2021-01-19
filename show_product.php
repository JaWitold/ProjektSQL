<?php
    require_once "checkPermissions.php";
    isLogged();

	try {
	    require_once "product.php";
	    if(isset($_GET["id"])) {
            $product = new product($_GET["id"]);
        }

	} catch(Exception $e) {
		echo $e->getMessage();
		header("Location: index.php");
		exit();
	}
    require_once "functions.php";

    require_once "html_elements/head.php";
    require_once "html_elements/navbar.php";
    require_once "html_elements/currentUser.php";
?>

	<div class ="container">

		<div class="row my-3">
			<div class="col-md-8 h2 text-center ">
                <?php echo $product->getProductName(); ?>
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
                    <tr><th>Cena Netto</th><td><?php echo $product->getNetPrice().add_zeros($product->getNetPrice());?> PLN</td></tr>
                    <tr><th>Vat</th><td><?php echo $product->getTax();?>%</td></tr>
                    <tr><th>Cena Brutto</th><td><?php echo $product->getGrossPrice().add_zeros($product->getGrossPrice());?> PLN</td></tr>
                    <tr><th>Ilość</th><td><?php echo $product->getAmount()." ".$product->getUnitOfMeasure();?></td></tr>
                </table>

                <form method="POST" action="remove_product.php" class="form-group pt-3">
                    <input type="hidden" name="productId" value="<?php echo $_GET["id"]; ?>">
                    <input type="submit" class="btn btn-outline-danger btn-block" value="Usuń produkt">
                </form>

			</div>
			<div class="col-md-4 text-center">
			    <img class="img-fluid rounded" src="<?php if($product->getPhoto() != ""){ echo "upload/".$product->getPhoto(); } else { echo "./upload/no_photo.jpg"; }?>" alt="photo of the product">
                <!--<?php if(!$product->getPhoto() == NULL){ echo '<img class="img-fluid rounded" src="upload/'.$product->getPhoto().'">'; } else echo "I`m a photo";?>-->
            </div>
		</div>


	</div>

	<?php require_once "html_elements/ending.php";?>

    <a href="index.php">back</a>