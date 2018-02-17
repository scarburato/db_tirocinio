<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 23/01/18
 * Time: 20.03
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER);
$user = \auth\connect_token_google($google_client, $_SESSION["user"]["token"], $oauth2);

// Variabili pagina
$page = "Cassetta degli strumenti";
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
            $index_menu = 8;
            include "../menu.php";
            ?>
        </aside>
        <div class="column">
            <div class="notification is-danger">
                Sembra che non si possegga alcun diritto in questa sezione.<br>
            </div>
            <article class="media box">
                <figure class="media-left">
                    <span class="icon is-large">
                        <i class="fa fa-users fa-2x" aria-hidden="true"></i>
                    </span>
                </figure>
                <div class="media-content">
                    <div class="content">
                        <h1>Gestione Utenze scolastiche</h1>
                        <p class="has-text-justified">È necessario avere un'utenza su Google in grado di sfogliare le utenze sul dominio</p>
                        <a class="button is-link is-pulled-right" href="users">
                            Configura
                        </a>
                    </div>
                </div>
            </article>
            <article class="media box">
                <figure class="media-left">
                    <span class="icon is-large">
                        <i class="fa fa-building fa-2x" aria-hidden="true"></i>
                    </span>
                </figure>
                <div class="media-content">
                    <div class="content">
                        <h1>Gestione Aziende</h1>
                        <a class="button is-link is-pulled-right" href="./aziende">
                            Configura
                        </a>
                    </div>
                </div>
            </article>
            <article class="media box">
                <figure class="media-left">
                    <span class="icon is-large">
                        <i class="fa fa-plug fa-2x" aria-hidden="true"></i>
                    </span>
                </figure>
                <div class="media-content">
                    <div class="content">
                        <h1>Controllo degli accessi</h1>
                        <a class="button is-link is-pulled-right" href="./traffico">
                            Configura
                        </a>
                    </div>
                </div>
            </article>
            <article class="media box">
                <figure class="media-left">
                    <span class="icon is-large">
                        <i class="fa fa-legal fa-2x" aria-hidden="true"></i>
                    </span>
                </figure>
                <div class="media-content">
                    <div class="content">
                        <h1>Gestione permessi docenti</h1>
                        <a class="button is-link is-pulled-right">
                            Configura
                        </a>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
</body>
</html>