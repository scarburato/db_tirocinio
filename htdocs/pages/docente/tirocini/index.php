<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 12/02/18
 * Time: 18.58
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER, "./../../../");
$user = \auth\connect_token_google($google_client, $_SESSION["user"]["token"], "./../../../", $oauth2);

// Variabili pagina
$page = "Tirocini";
?>
<html lang="it">
<head>
    <?php include "../../../utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 1;
            include "../menu.php";
            ?>
        </aside>
        <div class="column">

        </div>
    </div>
</section>
</body>
</html>