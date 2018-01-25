<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 23/01/18
 * Time: 19.27
 */

require_once "../../utils/lib.hphp";
require_once "../../utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_STUDENT, "./../../");

$google_client->setAccessToken($_SESSION["user"]["token"]);

$oauth2 = new \Google_Service_Oauth2($google_client);
$user = $oauth2->userinfo->get();

// Variabili pagina
$page = "Cruscotto";
?>
<html lang="it">
<head>
    <?php include "../../utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
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
<?php include "../../utils/pages/footer.phtml"; ?>
</body>
</html>