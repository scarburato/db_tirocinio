<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 28/02/18
 * Time: 10.32
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

// Variabili pagina
$page = "Gestione dei permessi";
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
        <div class="column">
            <div class="field">
                <label class="label">
                    Selezionare un docente nella base di dati
                </label>
            </div>
            <div class="field has-addons">
                <div class="control is-expanded">
                    <input id="query" class="input" type="email" placeholder="Indirizzo di posta elettronica">
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
            <div class="field is-horizontal">
                <div class="field-label is-normal">
                    <label class="label">
                        Non trovato?
                    </label>
                </div>
                <div class="field-body">
                    <div class="field is-normal">
                        <a href="../users/import.php" class="button is-fullwidth is-primary">
                            <span class="icon">
                                <i class="fa fa-user-plus" aria-hidden="true"></i>
                            </span>
                            <span>
                                Importa un utente dal dominio!
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <div id="info" class="message is-info">
                <div class="message-body">
                    Per cominciare inserire l'indirizzo di <strong>posta elettronica</strong> dell'utente di cui si
                    vuole modificare i permessi d'accesso.
                </div>
            </div>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
</body>
</html>