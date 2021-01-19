<?php
    require_once "checkPermissions.php";
    isLogged();

	if(!isset($_POST['productId'])) {
	    print_r($_POST);
	    header('Location:show_products_list.php');
		exit();
	} else {
		try {
            require_once "product.php";
            $product = new product($_POST['productId']);

			if(isset($_POST['productName'])) {

				$valid = true;

				$product->setProductName($_POST['productName']);

                $product->setNetPrice(floatval($_POST['netPrice']));
				$product->setTax($_POST['tax']);
				$product->setAmount($_POST['amount']);
				$product->setUnitOfMeasure($_POST['uom']);


				if(!$product->checkName()) {
					$valid = false;
					$_SESSION['e_nazwa']="Nazwa produktu powinna zawierać od 3 do 100 znaków";
				}

				if(!$product->checkNetPrice()) {
					$valid = false;
					$_SESSION['e_netPrice']="Nie poprawna wartość ceny zakupu";
				}
				
				if(!$product->checkTax()) {
					$valid = false;
					$_SESSION['e_vat']="Nie poprawna wartość stawki vat";
				}
				
				if(!$product->checkAmount()) {
					$valid = false;
					$_SESSION['e_ilosc']="Nie poprawna wartość pola ilość";
				}

                if(!$product->checkUnitOfMeasure()) {
                    $valid = false;
                    $_SESSION['e_ilosc']="Nie poprawna jednostka miary";
                }

				$photo = $_FILES['photo'];
                print_r($photo);
				$photo_new_name = "";

				//Sprawdzanie w bazie nazwy starego zdjecia
                require_once "connect.php";
                global $db;
				$query = $db->prepare('SELECT photo FROM products WHERE productId = :productId');
				$query->bindValue(':productId', $product->getProductId(), PDO::PARAM_INT);
				$query->execute();
					
				$photo_old_name = $query->fetch(PDO::FETCH_ASSOC);
				
				//przetwarzanie zdjecia, jeżeli zostało przesłane
				if($photo['error'] !== 4) {

					if(!product::validPhoto($photo)) {
						$valid = false;
						$_SESSION["e_file"] = "Błąd przetwarzania zdjęcia";
					}
					print_r($photo_old_name);
					//exit();
                    $photo_format = explode(".", $photo['name']);
                    $photo_format = end($photo_format);

					if(!strcmp($photo_old_name["photo"],'no_photo.jpg')) {
						$photo_new_name = uniqid().".".$photo_format;
					} else {
						$photo_new_name = $photo_old_name["photo"];
					}
					print_r($photo_new_name);
					
				} else {
					$photo_new_name = $photo_old_name["photo"];
				}

                if(!is_dir("upload")){
                    mkdir("upload", 0777);
                }

				$destination = "upload/".$photo_new_name;
				
				//print_r($destination);
				
				if($valid == true)
				{
					if($photo["error"] !== 4) {
						//upload pliku
						move_uploaded_file($photo["tmp_name"], $destination);
					}

                    $product->setPhoto($photo_new_name);
                    $product->updateProductInDatabase();
					echo "jest w pyte ";
					header("Location: show_product_details.php?id=".$product->getProductId());
					//echo '<a href="show_products_list.php">back</a>';
					exit();
				}
			}
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}
    require_once "html_elements/head.php";
    require_once "html_elements/navbar.php";
    require_once "html_elements/currentUser.php";
?>

	<div class="container">
        <div class="row">
            <div class="col-12 my-3 text-center">
                <h2 class="h2 mb-3">Edytuj produkt</h2>
            </div>
        </div>

        <div class="row">
            <form method="POST" enctype="multipart/form-data" class="form-group col-8">

                <div class="form-row">
                    <?php echo'<input type="hidden" name="productId" value="'.$product->getProductId().'">';?>
                    <div class="input-group mb-3 col">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="productName">Nazwa</label>
                        </div>
                        <input type="text" name="productName" id="productName" placeholder="Nazwa produktu" <?php if(isset($product)){echo 'value="'.$product->getProductName().'"';}?> class="form-control"><?php if(isset($_SESSION["e_nazwa"])){echo '<span style="color:red;">'.$_SESSION["e_nazwa"].'</span>'; unset($_SESSION["e_nazwa"]);}?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group mb-3 col">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="netPrice">Cena netto</label>
                        </div>
                        <input type="number" name="netPrice" id="netPrice" placeholder="Cena netto" step="0.01" min="0" <?php if(isset($product)){echo 'value="'.$product->getNetPrice().'"'; }?> class="form-control"><?php if(isset($_SESSION["e_netPrice"])){echo '<span style="color:red;">'.$_SESSION["e_netPrice"].'</span>'; unset($_SESSION["e_netPrice"]);}?>
                        <div class="input-group-append">
                            <label class="input-group-text" for="netPrice">PLN</label>
                        </div>
                    </div>

                    <div class="input-group mb-3 col">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="tax">Stawka vat</label>
                        </div>
                        <input type="number" id="tax" name="tax" placeholder="Stawka Vat" step="1" max="100" min="0"<?php if(isset($product)){echo 'value="'.$product->getTax().'"';}else echo 'value="23"'?> class="form-control"><?php if(isset($_SESSION["e_vat"])){echo '<span style="color:red;">'.$_SESSION["e_vat"].'</span>'; unset($_SESSION["e_vat"]);}?>
                        <div class="input-group-append">
                            <label class="input-group-text" for="tax">%</label>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group mb-3 col">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="amount">Ilość</label>
                        </div>
                        <input type="number" name="amount" id="amount" placeholder="Ilosc" min="0" step="0.001" <?php if(isset($product)){echo 'value="'.$product->getAmount().'"';}?> class="form-control">
                    </div>
                    <div class="input-group mb-3 col">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="uom">Jednostka miary</label>
                        </div>
                        <select type="text" name="uom" id="uom" class="form-control">
                            <option value="kg"  <?php if(isset($product) && !strcmp($product->getUnitOfMeasure(), "kg")){echo "selected"; }?>>kg </option>
                            <option value="szt" <?php if(isset($product) && !strcmp($product->getUnitOfMeasure(), "szt" )){echo "selected"; }?>>szt</option>
                        </select> <?php if(isset($_SESSION["e_ilosc"])){echo '<span style="color:red;">'.$_SESSION["e_ilosc"].'</span>'; unset($_SESSION["e_ilosc"]);}?>

                    </div>
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="photo">Dodaj zdjęcie </label>
                    </div>
                    <div class="custom-file">
                        <label class="custom-file-label" for="photo" id="fileName"></label>
                        <input type="file" name="photo" id="photo" class="form-control-file"><?php if(isset($_SESSION["e_file"])){echo '<span style="color:red">'.$_SESSION["e_file"].'</span>'; unset($_SESSION["e_file"]);}?>
                        <script>
                            document.getElementById("photo").addEventListener('change', function() {
                                document.getElementById("fileName").textContent = document.getElementById("photo").files[0].name;
                                //console.log(document.getElementById("photo").files[0].name);
                            });
                        </script>
                    </div>
                </div>

                <input type="submit" value="Zapisz zmiany" class="btn btn-primary form-control">

            </form>

            <div class="col-md-4">
                <img class="img-fluid rounded" alt="photo of product" src="upload/<?php echo $product->getPhoto();?> ">
            </div>
        </div>


    </div>
    <div class="col-12"><?php if(isset($_SESSION["e_photo"])){echo '<span style="color:red;">'.$_SESSION["e_photo"].'</span>'; unset($_SESSION["e_photo"]);}?></div>




<?php require_once "html_elements/ending.php" ?>