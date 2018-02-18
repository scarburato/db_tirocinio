<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 19/01/18
 * Time: 20.06
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_STUDENT);
$oauth2 = \auth\connect_token_google($google_client, $_SESSION["user"]["token"]);$user = \auth\get_user_info($oauth2);

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
        <aside class="column is-3 is-fullheight" style="min-height: 20em">
            <p class="menu-label">
                Tirocini
            </p>
            <ul class="menu-list">
                <li>
                    <a class="is-active">
                        <span class="icon">
                            <i class="fa fa-play" aria-hidden="true"></i>
                        </span>
                        <span>
                            In corso
                        </span>
                    </a>
                </li>
                <li>
                    <a>
                        <span class="icon">
                            <i class="fa fa-fast-forward" aria-hidden="true"></i>
                        </span>
                        <span>
                            Futuri
                        </span>
                    </a>
                </li>
                <li>
                    <a>
                        <span class="icon">
                            <i class="fa fa-stop" aria-hidden="true"></i>
                        </span>
                        <span>
                            Terminati
                        </span>
                    </a>
                </li>
            </ul>
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
