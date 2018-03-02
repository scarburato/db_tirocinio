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
$server = new \mysqli_wrapper\mysqli();
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
        <div class="column is-fullheight">
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
            <div id="setting" class="columns is-fullheight">
                <div class="column is-5 is-fullheight is-paddingless">
                    <h3 class="title is-3 has-text-centered">Applicati</h3>
                    <div class="box is-paddingless" style="height: 30em">
                        <table class="table is-narrow">
                            <tbody id="applicati">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="is-hidden-mobile" style="height: 3em"></div>
                    <div class="field">
                        <div class="control">
                            <button class="button is-fullwidth" id="rimuovi">
                                <span class="icon">
                                    <i class="fa fa-arrow-right is-hidden-mobile" aria-hidden="true"></i>
                                    <i class="fa fa-arrow-down  is-hidden-tablet " aria-hidden="true"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <button class="button is-fullwidth" id="aggiungi">
                                <span class="icon">
                                    <i class="fa fa-arrow-left is-hidden-mobile" aria-hidden="true"></i>
                                    <i class="fa fa-arrow-up   is-hidden-tablet" aria-hidden="true"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="column is-5 is-fullheight is-paddingless">
                    <h3 class="title is-3 has-text-centered">Disponibili</h3>
                    <div class="box is-paddingless" style="height: 30em">
                        <table class="table is-fullwidth">
                            <tbody id="privilegi">
                            <?php
                            $permessi = $server->prepare(
                                    "SELECT nome, descrizione FROM Privilegio"
                            );
                            $permessi->execute(true);
                            $permessi->bind_result($nome, $descrizione);
                            while($permessi->fetch())
                            {
                                ?>
                                <tr data-id="<?= $nome ?>">
                                    <th style="width: 25%"><?= $nome ?></th>
                                    <td><p class="has-text-justified"><?= $descrizione ?></p></td>
                                    <td style="width: 20%">
                                        <a tabindex="">Seleziona</a>
                                    </td>
                                </tr>
                                <?php
                            }
                            $permessi->close();
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>

<script src="<?= BASE_DIR ?>js/tableSelection.js"></script>
<script src="js/main.js"></script>
</body>
</html>