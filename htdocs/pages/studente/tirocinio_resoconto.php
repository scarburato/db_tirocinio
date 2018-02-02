<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 28/01/18
 * Time: 17.02
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_STUDENT, "./../../");
$user = \auth\connect_token_google($google_client, $_SESSION["user"]["token"], "./../../", $oauth2);

if(!isset($_GET["tirocinio"]))
{
    header("Location: index.php");
    die("");
}

// TODO Interrogazione
$tirocinio_azienda = "NULL";

// Variabili pagina
$page = "Scrivi Resosconto - " . $tirocinio_azienda;

?>

<html lang="it">
<head>
    <?php include "../../utils/pages/head.phtml"; ?>
    <script src="https://unpkg.com/lite-editor@1.4.0/js/lite-editor.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/lite-editor@1.4.0/css/lite-editor.css">
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
                    <a href="pages/studente/index.php?time=now">
                        <span class="icon">
                            <i class="fa fa-play" aria-hidden="true"></i>
                        </span>
                        <span>
                            In corso
                        </span>
                    </a>
                </li>
                <li>
                    <a href="pages/studente/index.php?time=future">
                        <span class="icon">
                            <i class="fa fa-fast-forward" aria-hidden="true"></i>
                        </span>
                        <span>
                            Futuri
                        </span>
                    </a>
                </li>
                <li>
                    <a href="pages/studente/index.php?time=past">
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
        <div>
            <div id="summernote">
                cds
            </div>
        </div>
    </div>
</section>
</body>
</html>