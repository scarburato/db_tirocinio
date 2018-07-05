<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 24/02/18
 * Time: 11.45
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();

// Variabili pagina
$page = "Importa utente da Google";

?>

<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 8;
            include "../../menu.php";
            ?>
        </aside>
        <div class="column is-fullwidth">
            <div class="field">
                <label class="label">
                    Aggiungi da dominio
                </label>
            </div>
            <div class="field has-addons">
                <div class="control is-expanded">
                    <input id="query" autocomplete="off" class="input" type="email" placeholder="Indirizzo di posta elettronica">
                </div>
                <div class="control">
                    <button id="search" class="button is-link">
                        <span class="icon">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </span>
                        <span>
                            Cerca
                        </span>
                    </button>
                </div>
            </div>

            <div id="wait" class="is-boxed">

            </div>

            <div id="info" class="message is-info">
                <div class="message-body">
                    Per cominciare inserire l'indirizzo di <strong>posta elettronica</strong> dell'utente che si vuole aggiungere.
                </div>
            </div>

            <div id="error" class="message is-danger">
                <div class="message-header">
                    <p>
                        <span class="icon">
                            <i class="fa fa-database"></i>
                        </span>
                        <span>
                            Errore durante l'interrogazione
                        </span>
                    </p>
                </div>
                <div class="message-body">
                    <p>Si sono verificati dei problemi durante l'interrogazione!</p>
                    <pre id="error_what"><?= sanitize_html($errori) ?></pre>
                </div>
            </div>

            <div id="no_output" class="box">
                <h1 class="title is-2">
                    <span class="icon is-large">
                        <i class="fa fa-frown-o" aria-hidden="true"></i>
                    </span>
                    <span>Nessun risultato</span>
                </h1>
            </div>

            <div id="output" class="box">
                <div class="media">
                    <figure class="image is-96x96 media-left" id="output_img">
                        <img src="">
                    </figure>
                    <div class="media-right">
                        <h1 class="title is-2" id="output_nominative"></h1>
                        <h3 class="subtitle is-3">
                            <a id="output_email" href="">
                            </a>
                        </h3>
                        <p>
                            Unità organizzativa <code id="output_orgunit"></code>
                        </p>
                        <p><strong>Livelli</strong></p>
                        <ul>
                            <li>Docente</li>
                        </ul>
                    </div>
                </div>
                <br>
                <button id="add_user" class="button is-large is-primary is-fullwidth" >
                    <span class="icon">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </span>
                    <span>
                        Aggiungi
                    </span>
                </button>
                <p class="help is-danger" id="user_exists" hidden>Questo utente è già registrato!</p>
            </div>
        </div>
    </div>
</section>
</body>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
<script src="js/main.js"></script>
</html>