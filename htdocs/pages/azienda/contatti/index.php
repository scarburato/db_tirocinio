<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 26/02/18
 * Time: 11.05
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();
$user = new auth\User();
$user->is_authorized(\auth\LEVEL_FACTORY, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveAziendaFromDatabase($server)));


$page = "Contatti"
?>
<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../../common/google_navbar.php"; ?>
<br>
<section class="container is-fullheight">
    <div class="columns">
        <aside class="column is-3 is-fullheight" >
            <?php
            $index_menu = 1;
            include "../menu.php";
            ?>
        </aside>
        <div class="column">
            <p>
                <a class="button is-primary is-pulled-right is-large" href="./crea.php">
                    <span class="icon">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </span>
                    <span>
                        Aggiungi
                    </span>
                </a>
            </p>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
</body>
</html>