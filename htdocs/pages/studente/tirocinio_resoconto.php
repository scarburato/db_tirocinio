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
    <link rel="stylesheet" href="<?= BASE_DIR ?>css/editor/themes/modern.min.css" type="text/css" media="all">

    <script src="<?= BASE_DIR ?>js/editor/sceditor.min.js"></script>
    <script src="<?= BASE_DIR ?>js/editor/bbcode.min.js"></script>
    <script src="<?= BASE_DIR ?>js/editor/icons/monocons.min.js"></script>
    <script src="<?= BASE_DIR ?>js/editor/icons/material.min.js"></script>

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
                    <a href="./index.php?time=now">
                        <span class="icon">
                            <i class="fa fa-play" aria-hidden="true"></i>
                        </span>
                        <span>
                            In corso
                        </span>
                    </a>
                </li>
                <li>
                    <a href="./index.php?time=future">
                        <span class="icon">
                            <i class="fa fa-fast-forward" aria-hidden="true"></i>
                        </span>
                        <span>
                            Futuri
                        </span>
                    </a>
                </li>
                <li>
                    <a href="./index.php?time=past">
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
        <div class="column is-fullwidth is-fullheight">
            <div class="tabs" id="selector">
                <ul>
                    <li class="is-active" data-tab="editor">
                        <a>
                            <span class="icon">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </span>
                            <span>
                                Videoscrittura
                            </span>
                        </a>
                    </li>
                    <li data-tab="preview">
                        <a>
                            <span class="icon">
                                <i class="fa fa-file-text" aria-hidden="true"></i>
                            </span>
                            <span>
                                Anteprima
                            </span>
                        </a>
                    </li>
                    <li data-tab="comments">
                        <a>
                            <span class="class">
                                <i class="fa fa-comments" aria-hidden="true"></i>
                            </span>
                            <span>
                                Commenti
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
            <div id="contents">
                <div class="control" data-tab="editor">
                    <textarea id="resoconto" class="textarea" rows="20" title="resonto"></textarea>
                </div>
                <div data-tab="preview" hidden>
                    <div class="content" id="preview_editor">

                    </div>
                </div>
                <div data-tab="comments" hidden>

                </div>
            </div>
        </div>
    </div>
</section>

<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
</body>

<script src="<?= BASE_DIR ?>js/toggleTab.js"></script>
<script src="js/resoconto.js"></script>
</html>