<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 27/02/18
 * Time: 10.57
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

// Variabili pagina
$page = "Imposta Unità Organizzative";

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
            <article class="message is-info">
                <div class="message-body has-text-justified">
                    Selezionare una unità organizzativa rende valide anche tutte le sotto-unità.<br>
                    Eventuali sovraposizioni vengono gestite con l'inserimento in <strong>ambedue le sezioni</strong>.
                    Ad esempio:<br>
                    Se un utente ha come unità organizzativa <code>/PERSONE/UNITÀ/SUPER-STUDENTI/4-A</code> ed è stato
                    impostato che gli utenti di unità organizzativa <code>/PERSONE</code> sono studenti e gli utenti
                    di unità organizzativa <code>/PERSONE/UNITÀ/SUPER-STUDENTI</code> sono docenti, allora l'utente
                    sopracitato finirà in ambedue i gruppi.
                </div>
            </article>
            <!-- TODO AGIUSTARE PULSANTE CRISTO -->
            <div class="field">
                <div class="control">
                    <button class="button is-primary is-large">
                        Carica nuove impostazioni (Agiustatemi pls)
                    </button>
                </div>
            </div>
            <h3 class="title is-3">
                Unità organizzative studente
            </h3>
            <div class="field has-text-right has-addons">
                <button class="button add-button" data-orgtype="student">Aggiungi</button>
                <button class="button">Rimuovi</button>
            </div>
            <div class="box is-paddingless">
                <table class="table is-fullwidth is-narrow">
                    <tbody class="orgunits" data-orgtype="student">

                    </tbody>
                </table>
            </div>
            <h3 class="title is-3">
                Unità organizzative Docente
            </h3>
            <div class="field has-text-right has-addons">
                <button class="button add-button" data-orgtype="teach">Aggiungi</button>
                <button class="button">Rimuovi</button>
            </div>
            <div class="box is-paddingless">
                <table class="table is-fullwidth is-narrow">
                    <tbody class="orgunits" data-orgtype="teach">

                    </tbody>
                </table>
            </div>
            <h3 class="title is-3">
                Unità organizzative Ambedue
            </h3>
            <div class="field has-text-right has-addons">
                <button class="button add-button" data-orgtype="ambedue">Aggiungi</button>
                <button class="button">Rimuovi</button>
            </div>
            <div class="box is-paddingless">
                <table class="table is-fullwidth is-narrow">
                    <tbody class="orgunits" data-orgtype="ambedue">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/footer.phtml"; ?>

<!--- PopOut: Seleziona ATECO -->
<div class="modal" id="seleziona_orgunit">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Seleziona unità organizzativa</p>
        </header>
        <section class="modal-card-body" style="height: 100%; max-height: 100%">
            <div class="is-fullwidth" style="overflow-y: auto">
                <table class="table is-fullwidth is-narrow is-hoverable">
                    <thead>
                    <tr>
                        <th>Percorso</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="orgunits_body">

                    </tbody>
                </table>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="seleziona_orgunit_aggiungi" disabled>Seleziona</button>
            <button class="button" id="seleziona_orgunit_scarta">Scarta</button>
        </footer>
    </div>
</div>
<script src="<?= BASE_DIR ?>js/togglePanel.js"></script>
<script src="<?= BASE_DIR ?>js/tableSelection.js"></script>
<script src="js/import.js"></script>
</body>
</html>