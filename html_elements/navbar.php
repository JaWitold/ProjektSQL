<nav class="navbar navbar-expand-lg navbar-dark py-3">

	<a class="navbar-brand" href="index.php">Langner</a>
	
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarmenu" aria-controls="navbarmenu" aria-expanded="false" aria-label="navbar switch">
		<span class="navbar-toggler-icon"></span>
	</button>
	
	<div class="collapse navbar-collapse" id="navbarmenu">
	
		<ul class="navbar-nav mr-auto">
			<li class="nav-item">
				<a class="nav-link" href="add_new_product.php">dodaj produkt</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="show_products_list.php">pokaż produkty</a>
			</li>
            <?php require_once "checkPermissions.php"; if(showForAdmin()) echo '
                <li class="nav-item">
                    <a class="nav-link" href="show_users_list.php">pokaż liste pracowników</a>
                </li>
			';?>
			<li class="nav-item">
				<a class="nav-link" href="show_user_details.php?login=<?php require_once "c_user.php"; $currentUser = unserialize($_SESSION['user']); echo $currentUser->getLogin();?>">moje dane</a>
			</li>
		</ul>

        <?php require_once "checkPermissions.php"; if(showForAccountant() || showForAdmin()) echo '<a class="nav-link btn btn-outline-success float-right mr-3" href="backup.php">backup</a>';?>
        <?php require_once "checkPermissions.php"; if(showForAdmin()) echo '<a class="nav-link btn btn-outline-primary float-right mr-3" href="show_logs_list.php">logs</a>';?>

        <a class="nav-link btn btn-outline-light float-right mr-3" href="logout.php">Wyloguj</a>
		
	</div>
</nav>