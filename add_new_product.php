<?php
    require_once "checkPermissions.php";
    isLogged();


		try {
			if(isset($_POST['productName'])) {

				$all_ok = true;

                $productName = $_POST['productName'];
				if(strlen($productName) < 3 || strlen($productName) > 100) {
					$all_ok = false;
					$_SESSION['error']="Nazwa produktu powinna zawierać od 3 do 100 znaków";
				}

                $tax = $_POST['tax'];
				
				if(!is_numeric($tax)) {
					$all_ok = false;
					$_SESSION['error']="Nie poprawna wartość stawki vat";
				}

                $netPrice = $_POST['netPrice'];
				
				if(!is_numeric($netPrice) || $netPrice <= 0) {
					$all_ok = false;
					$_SESSION['error']="Nie poprawna wartość ceny sprzedaży";
				}

                $amount = $_POST['amount'];
				
				if(!is_numeric($amount) || $amount == 0) {
					$all_ok = false;
					$_SESSION['error']="Nie poprawna wartość pola ilość";
				}

                $uom = $_POST['uom'];

				$photo = $_FILES['photo'];
				//przetwarzanie zdjecia
				if($photo['error'] !== 4){
				$format = ["jpg" , "jpeg", "png"];
				
				$photo_format = explode(".", $photo['name']);
				$photo_format = end($photo_format);
				//echo $photo_format;
				
				if(!in_array($photo_format, $format)) {
					$all_ok = false;
					$_SESSION["error"] = "Niepoprawny format pliku";
				}
				
				if($photo['error'] !== 0) {
					$all_ok = false;
					$_SESSION["error"] = "Wystąpił błąd podczas przesylania pliku";
				}
				
				if($photo['size'] > 200000) {
					$all_ok = false;
					$_SESSION["error"] = "Plik jest za duży :P";
				}
				
				$photo_new_name = uniqid().".".$photo_format;
				} else {
					$photo_new_name = "no_photo.jpg";
				}

				if(!is_dir("upload")){
				    mkdir("upload", 0777);
				}
				$destination = "upload/".$photo_new_name;
				//koniec walidacji danych
				
				if($all_ok === true) {
                    require_once "connect.php";
                    global $db;
				    try {
                        $db->beginTransaction();
                        $query = $db->prepare("SELECT * FROM products WHERE productId = :productName");
                        $query->bindValue(':productName', $productName, PDO::PARAM_STR);
                        $query->execute();

                        $result = $query->rowCount();
                        if ($result === 0) {
                            if ($photo["error"] !== 4) {
                                //upload pliku
                                move_uploaded_file($photo["tmp_name"], $destination);
                            }
                            $query = $db->prepare('INSERT INTO `products`(`productId`, `productName`, `amount`, `unitOfMeasure`, `netPrice`, `tax`, `photo`) VALUES (NULL, :productName, :amount, :untOfMeasure, :netPrice, :tax, :photo)');
                            $query->bindValue(':productName', $productName, PDO::PARAM_STR);
                            $query->bindValue(':amount', $amount, PDO::PARAM_STR);
                            $query->bindValue(':untOfMeasure', $uom, PDO::PARAM_STR);
                            $query->bindValue(':netPrice', $netPrice, PDO::PARAM_STR);
                            $query->bindValue(':tax', $tax, PDO::PARAM_STR);
                            $query->bindValue(':photo', $photo_new_name, PDO::PARAM_STR);
                            $query->execute();
                            $db->commit();

                            header("Location: show_products_list.php");
                            exit();
                        } else {
                            $_SESSION["error"] = "istnieje juz produkt o tej nazwie";
                        }
                    } catch (Exception $e) {
                        $db->rollBack();
                    }
				}
			}
		} catch(Exception $e) {
			echo $e->getMessage();
		}


	require_once "./html_elements/head.php";
	require_once "./html_elements/navbar.php";
    require_once "./html_elements/currentUser.php";
	?>

	<div class="container">
        <div class="row">
            <div class="h2 my-3 py-2 col-md-10 offset-md-1 text-center">Dodaj nowy produkt</div>
        </div>

        <form class="form-group col-md-10 offset-md-1" method="POST" enctype="multipart/form-data">


            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="productName">Nazwa</label>
                </div>
                <input type="text" name="productName" id="productName" placeholder="Nazwa produktu" class="form-control" required <?php if(isset($productName)){echo 'value="'.$productName.'"'; unset($productName);}?> >
            </div>


            <div class="form-row">
                <div class="input-group mb-3 col">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="netPrice">Cena netto</label>
                    </div>
                        <input type="number" name="netPrice" id="netPrice" placeholder="Cena Sprzedaży" min="0" step="0.01" class="form-control" required <?php if(isset($netPrice)){echo 'value="'.$netPrice.'"'; unset($netPrice);}?>>
                    <div class="input-group-append">
                        <label class="input-group-text" for="netPrice">PLN</label>
                    </div>
                </div>

                <div class="input-group mb-3 col">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="tax">Vat</label>
                    </div>
                    <input type="number" name="tax" id="tax" placeholder="Stawka Vat" step="1" max="100" min="0"  class="form-control" required <?php if(isset($tax)){echo 'value="'.$tax.'"'; unset($tax);}else echo 'value="23"'?>>
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
                    <input type="number" name="amount" id="amount" placeholder="Ilosc" min="0" step="0.001" <?php if(isset($amount)){echo 'value="'.$amount.'"'; unset($amount);}?> class="form-control">
                </div>
                <div class="input-group mb-3 col">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="uom">Jednostka miary</label>
                    </div>
                    <select name="uom" id="uom" class="form-control">
                        <option value="kg"  <?php if(isset($uom) && !strcmp($uom, "kg")){echo "selected"; unset($uom);}?>>kg </option>
                        <option value="szt" <?php if(isset($uom) && !strcmp($uom, "szt" )){echo "selected"; unset($uom);}?>>szt</option>
                    </select>
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
                            console.log(document.getElementById("photo").files[0].name);
                        });
                    </script>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group col">
                    <?php if(isset($_SESSION["error"])){echo '<span style="color:red;">'.$_SESSION["error"].'</span>'; unset($_SESSION["error"]);}?>

                    <input type="submit" class="btn btn-outline-success btn-block my-3" value="Dodaj nowy produkt">
                </div>
            </div>
        </form>
	</div>
<?php require_once "html_elements/ending.php"?>