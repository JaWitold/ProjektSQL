<?php

    function generateBackup($filepath) {
        //echo $backup;
        require_once "connect.php";
        global $db;
//        $query  = $db->query("SHOW TABLES");
//        $query->execute();
//        $result = $query->fetchAll();
//
//        foreach ($result as $r) {
//            //echo $r[0]."<br>";
//
//            $query2 = $db->query("SELECT * FROM $r[0]");
//            $query2->execute();
//            $items = $query2->fetchAll(PDO::FETCH_ASSOC);
//
//            if($query2->rowCount() != 0) {
//                foreach ($items as $i) {
//                    $msg = "INSERT INTO $r[0] VALUES ( '";
//                    $last = end($i);
//                    foreach ($i as $v) {
//                        if($v === $last) {
//                            $msg = $msg . $v . "' );";
//                        } else {
//                            $msg = $msg . $v . "', '";
//                        }
//
//                    }
//                    $backup = $backup . $msg . "\n";
//                }
//            }
//        }
        $database = 'langner';
        $user = 'langner_admin';
        $pass = 'qwerty123';
        $host = 'localhost';
        $dir = './dump.sql';

        chdir ("../../mysql/bin/");
        exec("mysqldump --user={$user} --password={$pass} --host={$host} {$database} --result-file=C:/Programs/xammp/htdocs/ProjektSQL/{$filepath} 2>&1", $output);
        chdir ("../../htdocs/ProjektSQL");
    }

    require_once "checkPermissions.php";
    isAccountant();

    if(isset($_POST['action'])) {
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
        //echo $action;
        if(!strcmp($action, "export")) {

            $filename="backup.sql";
            $filepath="./tmp/".$filename;
            //Tworzenie backupu
            generateBackup($filepath);

            if(!empty($filename) && file_exists($filepath)) {

                header("Cashe-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$filename");
                header("Content-Type: application/zip");
                header("Content-Transfer-Emcoding: binary");
                flush();

                readfile($filepath);
                unlink($filepath);
                exit();
            }

        } else if(!strcmp($action, "import")) {
            $backup = $_FILES['backup'];
            if($backup['error'] == 0) {
                $date = new DateTime();
                require_once "c_user.php";
                $currentUser = unserialize($_SESSION['user']);

                generateBackup("./backups/autobackup_" . $currentUser->getLogin()."_". $date->format("Y-m-d_H_i_s") . ".sql");
                //print_r($_FILES['backup']);

                $cmd = "mysql -h localhost -u langner_admin -pqwerty123 langner < {$backup['tmp_name']}";

                chdir ("../../mysql/bin/");
                exec($cmd);
                chdir ("../../htdocs/ProjektSQL");
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['e_file'] = "Wystąpił błąd";
            }
        }
    }


    require_once "html_elements/head.php";
    require_once "html_elements/navbar.php";
    require_once "html_elements/currentUser.php"; ?>

    <div class="container">
        <div class="row">
            <form method="POST" class="form-group col-md-6">
                <h2 class="text-center h2 my-3">EXPORT</h2>
                <input type="hidden" name="action" value="export">
                <input class="btn btn-success btn-block" type="submit" value="Export bazy danych">
            </form>

            <form method="POST" class="form-group col-md-6" enctype="multipart/form-data">
                <h2 class="text-center h2 my-3">IMPORT</h2>

                <input type="hidden" name="action" value="import">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="backup">dodaj plik (.sql)</label>
                    </div>
                    <div class="custom-file">
                        <label class="custom-file-label" for="backup" id="fileName"></label>
                        <input type="file" name="backup" id="backup" class="form-control-file"><?php if(isset($_SESSION["e_file"])){echo '<span style="color:red">'.$_SESSION["e_file"].'</span>'; unset($_SESSION["e_file"]);}?>
                        <script>
                            document.getElementById("backup").addEventListener('change', function() {
                                document.getElementById("fileName").textContent = document.getElementById("backup").files[0].name;
                                //console.log(document.getElementById("backup").files[0].name);
                            });
                        </script>
                    </div>
                </div>

                <input type="submit" value="Import bazy danych" class="btn btn-primary form-control">
            </form>
        </div>
    </div>
<?php
    require_once "html_elements/ending.php";
