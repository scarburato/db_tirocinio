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
            <p class="control has-text-right">
                <button class="button is-primary is-large" id="upload">
                    <span>
                        Carica nuove impostazioni
                    </span>
                    <span class="icon is-large">
                        <i class="fa fa-upload" aria-hidden="true"></i>
                    </span>
                </button>
            </p>
            <h3 class="title is-3">
                Unità organizzative studente
            </h3>
            <div class="field has-text-right has-addons">
                <button class="button is-loading add-button" data-orgtype="studente">Aggiungi</button>
                <button class="button is-loading remove-button" data-orgtype="studente">Rimuovi</button>
            </div>
            <div class="box is-paddingless">
                <table class="table is-fullwidth is-narrow">
                    <tbody class="orgunits" data-orgtype="studente">
                    <?php
                    $unita_studente = $server->prepare(
                            "SELECT unita_organizzativa FROM UnitaOrganizzativa WHERE tipo = 'studente'"
                    );

                    $unita_studente->execute(true);
                    $unita_studente->bind_result($path);
                    while($unita_studente->fetch())
                    {
                        ?>
                        <tr data-raw="<?= $path ?>">
                            <td><?= $path ?></td>
                            <td style="width: 20%">
                                <a tabindex=''>Seleziona</a>
                            </td>
                        </tr>
                        <?php
                    }
                    $unita_studente->close();
                    ?>
                    </tbody>
                </table>
            </div>
            <h3 class="title is-3">
                Unità organizzative Docente
            </h3>
            <div class="field has-text-right has-addons">
                <button class="button is-loading add-button" data-orgtype="docente">Aggiungi</button>
                <button class="button is-loading remove-button" data-orgtype="docente">Rimuovi</button>
            </div>
            <div class="box is-paddingless">
                <table class="table is-fullwidth is-narrow">
                    <tbody class="orgunits" data-orgtype="docente">
                    <?php
                    $unita_docente = $server->prepare(
                            "SELECT unita_organizzativa FROM UnitaOrganizzativa WHERE tipo = 'docente'"
                    );

                    $unita_docente->execute(true);
                    $unita_docente->bind_result($path);
                    while($unita_docente->fetch())
                    {
                        ?>
                        <tr data-raw="<?= $path ?>">
                            <td><?= $path ?></td>
                            <td style="width: 20%">
                                <a tabindex=''>Seleziona</a>
                            </td>
                        </tr>
                        <?php
                    }
                    $unita_docente->close();
                    ?>
                    </tbody>
                </table>
            </div>
            <h3 class="title is-3">
                Unità organizzative ambigue
            </h3>
            <div class="field has-text-right has-addons">
                <button class="button is-loading add-button" data-orgtype="ambedue">Aggiungi</button>
                <button class="button is-loading remove-button" data-orgtype="ambedue">Rimuovi</button>
            </div>
            <div class="box is-paddingless">
                <table class="table is-fullwidth is-narrow">
                    <tbody class="orgunits" data-orgtype="ambedue">
                    <?php
                    $unita_ambigue = $server->prepare(
                        "SELECT unita_organizzativa FROM UnitaOrganizzativa WHERE tipo = 'ambedue'"
                    );

                    $unita_ambigue->execute(true);
                    $unita_ambigue->bind_result($path);
                    while($unita_ambigue->fetch())
                    {
                        ?>
                        <tr data-raw="<?= $path ?>">
                            <td><?= $path ?></td>
                            <td style="width: 20%">
                                <a tabindex=''>Seleziona</a>
                            </td>
                        </tr>
                        <?php
                    }
                    $unita_ambigue->close();
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/footer.phtml"; ?>

<!--- PopOut: Seleziona Unità organizzative -->
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
                    <!-- Generato dinamico dal JavaScript -->
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
<script src="js/set_orgunit.js"></script>
</body>
</html>