<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 19/01/18
 * Time: 20.06
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_STUDENT, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveStudenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());
// Variabili pagina
$page = "In corso";

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
            <p class="menu-label">
                Tirocini
            </p>
            <ul class="menu-list">
                <li>
                    <a class="is-active switch" data-selezione="1" tabindex="">
                        <span class="icon">
                            <i class="fa fa-play" aria-hidden="true"></i>
                        </span>
                        <span>
                            In corso
                        </span>
                    </a>
                </li>
                <li>
                    <a class="switch" data-selezione="2" tabindex="">
                        <span class="icon">
                            <i class="fa fa-fast-forward" aria-hidden="true"></i>
                        </span>
                        <span>
                            Futuri
                        </span>
                    </a>
                </li>
                <li>
                    <a class="switch" data-selezione="0" tabindex="">
                        <span class="icon">
                            <i class="fa fa-stop" aria-hidden="true"></i>
                        </span>
                        <span>
                            Terminati
                        </span>
                    </a>
                </li>
            </ul>
            <div class="is-hidden-mobile" style="min-height: 15em">

            </div>
        </aside>
        <div class="column">
            <div id="tirocinis">
            </div>
            <div id="loading_go_on" data-nextid="0">
                <div class="content has-text-centered">
                    <span class="icon">
                        <i class="fa fa-circle-o-notch fa-pulse" aria-hidden="true"></i>
                    </span>
                    <span class="is-fullheight">
                        Caricamento di altri tirocini...
                    </span>
                </div>
            </div>
            <div id="loading_stop" hidden="hidden">
                <div class="content has-text-centered">
                    <span class="icon">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </span>
                    <span class="is-fullheight">
                        Non c'è più nulla da mostrare.
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>

<script src="js/tirocini_builder.js"></script>
</body>
</html>
