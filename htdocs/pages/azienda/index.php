<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 11/02/18
 * Time: 18.53
 */
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();
$user = new auth\User();
$user->is_authorized(\auth\LEVEL_FACTORY, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveAziendaFromDatabase($server)));

$page = "Azienda";
?>

<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns" style="">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 0;
            include "menu.php";
            ?>
        </aside>
        <div class="column">

        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
</body>
</html>