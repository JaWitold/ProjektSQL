<div class="col-12 text-right mx-0 my-3">
    <?php
    require_once "c_user.php";
    $currentUser = unserialize($_SESSION['user']);
    echo 'Zalogowano jako '.$currentUser->getName().' '.$currentUser->getSurname().' '.$currentUser->getRole();

    ?>
</div>

