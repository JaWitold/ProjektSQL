<?php
    require_once "checkPermissions.php";
    isLogged();

    require_once "html_elements/head.php";
    require_once "html_elements/navbar.php";
    require_once "html_elements/currentUser.php";
?>
	
	<div class ="container">

        <div class="row">
            <form method="GET" class="form-group col-12 my-3">
                <div class="input-group mb-3">
                    <input name="search" type="text" class="form-control form-control-lg" placeholder="Wyszukaj" aria-label="search" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-lg btn-outline-light" type="submit">Wyszukaj</button>
                        <a class="btn btn-lg btn-outline-light" href="show_products_list.php" >Wyczysć</a>
                    </div>
                </div>
            </form>
        </div>


        <div class="row">
            <div class="col-md-6 offset-md-6 mb-3"><a href="add_new_product.php" class="btn btn-success float-right">Dodaj nowy produkt</a></div>
        </div>
	
	    <?php
            try {
                $queryStr = 'SELECT productId FROM products';

                if(isset($_GET['search'])) $queryStr = $queryStr . " WHERE productName LIKE :search";

                require_once "connect.php";
                global $db;
                $query = $db->prepare($queryStr);
                if(isset($_GET['search'])) {$query->bindValue(":search", "%".$_GET['search']."%", PDO::PARAM_STR);}
                $query->execute();

                if ($query->rowCount() != 0) {
                    $results = $query->fetchAll(PDO::FETCH_ASSOC);

                    $products = [];
                    foreach ($results as $result) {
                        require_once "product.php";
                        $tmp = new product($result['productId']);
                        array_push($products, $tmp);
//                        print_r($tmp);
//                        echo "<br>";
                    }

                    require_once "functions.php";
                    echo '<table class="table table-dark table-striped text-center"><thead><tr><td>Nazwa</td><td>Cena netto [PLN]</td><td>Vat [%]</td><td>Cena brutto [PLN]</td><td>Ilość</td><td>Szczegóły</td></tr></thead><tbody>';
                    foreach ($products as $product) {
                        echo "<tr>";
                        echo $product->getAsRow();
                        echo '<td><a href="./show_product.php/?id=' . $product->getProductId() . '">pokaż</a></td>';
                        echo "</tr>";
                    }
                    echo '</tbody></table>';
                } else {
                    echo "Brak tresci do wyswietlenia.";
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
	    ?>
	</div>
	
	<?php require_once "html_elements/ending.php"?>