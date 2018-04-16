<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 25/03/18
 * Time: 11.32
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

$permissions = new \auth\PermissionManager($server, $user);
$permissions->check("train.import", \auth\PermissionManager::UNAUTHORIZED_REDIRECT);

// Variabili pagina
$page = "Tirocini";
?>
<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/head.phtml"; ?>
    <script>
        const DOMAIN = "<?= sanitize_html(TRUSTED_DOMAIN) ?>";
    </script>
</head>
<body>
<?php include "../../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 1;
            include "../../menu.php";
            ?>
        </aside>
        <div class="column is-9">
            <div class="field set_div" id="file_div">
                <div class="file has-name is-fullwidth is-right">
                    <label class="file-label">
                        <input class="file-input" type="file" accept=".csv" id="csv_up">
                        <span class="file-name" id="csv_name"></span>
                        <span class="file-cta">
                            <span class="file-icon">
                                <i class="fa fa-upload" aria-hidden="true"></i>
                            </span>
                            <span class="file-label">
                                Carica un CSV
                            </span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="field is-horizontal set_div">
                <div class="field-label">
                    <label class="label">Separatore</label>
                </div>
                <div class="field-body">
                    <div class="field">
                        <div class="control">
                            <input class="input" type="text" id="csv_col" maxlength="1" placeholder="Separatore colonne">
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <input class="input" type="text" id="csv_quote" maxlength="1" placeholder="Separatore stringhe">
                        </div>
                    </div>
                </div>
            </div>
            <div class="field is-horizontal set_div">
                <div class="field-label">
                    <label class="label">Intestazione</label>
                </div>
                <div class="field-body">
                    <div class="field">
                        <div class="control">
                            <label class="checkbox">
                                <input type="checkbox" id="csv_header" checked>
                                Selezionare se la prima riga è l'intestazione della tabella
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="field is-horizontal set_div">
                <div class="field-label">
                    <label class="label">Prestazioni</label>
                </div>
                <div class="field-body">
                    <div class="field">
                        <div class="control">
                            <label class="checkbox">
                                <input type="checkbox" id="csv_multithread" checked>
                                Selezionare per usare la conversione multi-thread
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="field set_div">
                <div class="control">
                    <button class="button is-fullwidth is-info" id="csv_start" disabled>
                        <span>
                            Avvia conversione
                        </span>
                        <span class="icon">
                            <i class="fa fa-play" aria-hidden="true"></i>
                        </span>
                    </button>
                </div>
            </div>

            <!-- OUTPUT -->
            <div id="out" hidden>
                <div class="field" id="csv_halt_field">
                    <div class="control">
                        <button class="button is-danger is-fullwidth is-large" id="csv_halt" disabled>
                            <span>
                                Un ci piange'
                            </span>
                            <span class="icon">
                                <i class="fa fa-hand-paper-o" aria-hidden="true"></i>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="field level" id="csv_correct" hidden>
                    <div class="media-content">
                        Il documento è stato importato corretamente?
                    </div>
                    <div class="media-right buttons">
                        <button class="button is-danger" id="csv_correct_false">
                            <span class="icon">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </span>
                            <span>
                                No, riprova
                            </span>
                        </button>
                        <button class="button is-success" id="csv_correct_true">
                            <span class="icon">
                                <i class="fa fa-check" aria-hidden="true"></i>
                            </span>
                            <span>
                                Sì, continua
                            </span>
                        </button>
                    </div>
                </div>

                <progress id="csv_progress" class="progress is-info is-large" value="0" max="Infinity"></progress>
                <p class="help">Importate <span id="csv_rows_counter">0</span> righe</p>
                <div style="width: 100%; height: 85vh; overflow-x: scroll">
                    <table class="table is-fullwidth">
                        <thead>
                        <tr id="csv_head"></tr>
                        </thead>
                        <tbody id="csv_body"></tbody>
                    </table>
                </div>
            </div>

            <!-- Schermata di caricamento, nulla di che -->
            <div id="load" hidden>
                <p class="">Sto caricando....</p>
            </div>

            <!-- Dopo aver verificato l'anteprima associare le colonne ai tirocini -->
            <div id="config_cols" hidden>
                <h3 class="title is-3">Selezionare colonne per ogni campo</h3>
                <div class="box">
                    <h4 class="title is-4">Informazioni Studente</h4>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Nome</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select class="col_out" id="assoc_stu_name" title="nome">

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Cognome</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select class="col_out" id="assoc_stu_last_name" title="last_name">

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Chiave</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select title="key_name" id="assoc_stu_key">
                                            <option value="">Non usare</option>
                                            <optgroup class="col_out" label="Colonne">

                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <p class="help">
                                    La colonna da utilizzare come discriminante univoca in caso di omonimia (come il Codice Fiscale)
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Posta elettronica</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select title="key_name" id="assoc_stu_mail">
                                            <option value="">Non usare</option>
                                            <optgroup class="col_out" label="Colonne">

                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <p class="help">
                                    La colonna che contiene l'indirizzo di posta nel dominio dello studente. Se lasciata vuota si dovrà inserire manulamente!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <h4 class="title is-4">Informazioni tirocinio</h4>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Azienda</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select class="col_out" title="factory" id="assoc_fact_name">

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Tutore aziendale</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select class="col_out" title="tutore">
                                            <option value="">Non usare</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Docente referente</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select title="tutore">
                                            <optgroup class="col_out" label="Colonne">

                                            </optgroup>
                                            <option value="ask">Chiedi di volta in volta</option>
                                            <optgroup label="Docenti">
                                                <option value="me" selected>Io</option>
                                                <?php
                                                $docenti = $server->prepare(
                                                    "SELECT id, nome, cognome, indirizzo_posta FROM Docente
                                                              INNER JOIN UtenteGoogle G ON Docente.utente = G.id"
                                                );

                                                $docenti->execute();
                                                $docenti->bind_result($id, $nome, $cognome, $email);
                                                while ($docenti->fetch())
                                                {
                                                    ?>
                                                    <option value="<?= $id ?>">
                                                        <?= sanitize_html($nome) ?> <?= sanitize_html($cognome) ?> &lt;<?= sanitize_html($email) ?>&gt;
                                                    </option>
                                                    <?php
                                                }
                                                ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Data inizio</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select class="col_out" title="start">

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Data fine</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select class="col_out" title="stop">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <div class="control has-text-right">
                        <button class="button is-success" id="data_assoc_goon">
                            <span class="icon">
                                <i class="fa fa-check" aria-hidden="true"></i>
                            </span>
                            <span>
                                Continua
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- dopo aver associato le colonne si associano gli studenti alle e-mail -->
            <div id="data_assoc_stu" hidden>
                <h3 class="title is-3">Associare studenti alle proprie utenze</h3>
                <h4 class="subtitle is-4"><span id="data_assoc_stu_index"></span> su <span id="data_assoc_stu_max"></span> studenti</h4>
                <div class="box">
                    <div class="field">
                        <label class="label">Indirizzo di posta elettronica alunno</label>
                    </div>
                    <div class="field has-addons">
                        <div class="control is-expanded">
                            <input class="input" type="email" id="data_stu_mail" placeholder="Indirizzo di posta">
                            <p class="help is-danger" id="data_stu_mail_fail_helper" hidden>
                                Utente non esiste!
                            </p>
                            <p class="help">
                                Non è possibile memorizzare studenti che non fanno parte del dominio di Google.
                            </p>
                        </div>
                        <div class="control">
                            <button class="button is-info" id="data_stu_mail_validate">
                                <span class="icon">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </span>
                                <span>
                                    Valida
                                </span>
                            </button>
                        </div>
                    </div>
                    <?php
                    $can_do_it = \auth\check_permission($server, "user.google.add", false);
                    ?>
                    <div class="field">
                        <div class="control">
                            <label class="checkbox">
                                <input type="checkbox" id="data_stu_gimport" <?= $can_do_it ? "checked" : "disabled" ?>>
                                Cerca automaticamente di importare utenti dal dominio
                            </label>
                        </div>
                    </div>
                    <?php
                    if($can_do_it)
                    {
                        ?>
                        <div class="field">
                            <div class="control">
                                <a target="_blank" href="../../control_panel/users/import.php" class="button is-small is-link">
                                    Importa usando lo strumento dedicato (verrà aperto in una nuova scheda)
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="field">
                        <div class="control has-text-right">
                            <button class="button is-success" id="data_stu_next">
                                <span>
                                    Continua
                                </span>
                                <span class="icon">
                                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                </span>
                            </button>
                            <button class="button is-success" id="data_stu_end" hidden>
                                <span>
                                    Termina
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ora si associano le aziende! -->
            <div id="data_assoc_fact" hidden>
                <h3 class="title is-3">Associare aziende</h3>
                <h4 class="subtitle is-4"><span id="data_assoc_fact_index"></span> su <span id="data_assoc_fact_max"></span> aziende</h4>
                <div class="box">
                    <h5 class="title is-5" id="data_assoc_fact_name">Nome</h5>
                    <?php
                    if(\auth\check_permission($server, "user.factory.add", false))
                    {
                    ?>
                        <div class="field">
                            <div class="control">
                                <a target="_blank" href="../../control_panel/aziende/aggiungi.php" class="button is-small is-link">
                                    Aggiungi una azienda
                                </a>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="level">
                        <!-- Left side -->
                        <div class="level-left">
                            <form id="azienda_cerca">
                                <div class="field has-addons">
                                    <p class="control">
                                        <input class="input" name="query" type="text" placeholder="Cerca">
                                    </p>
                                    <p class="control">
                                        <button type="submit" class="button">
                                            Cerca
                                        </button>
                                    </p>
                                </div>
                            </form>
                        </div>

                        <div class="level-right">
                            <div class="field has-addons">
                                <p class="control">
                                    <button class="button" disabled id="azienda_back">Indietro</button>
                                </p>
                                <p class="control">
                                    <button class="button" disabled id="azienda_forward">Avanti</button>
                                </p>
                                <p class="control">
                                    <button class="button" disabled id="azienda_reload">
                                        <span class="icon">
                                            <i class="fa fa-refresh" aria-hidden="true" title="Ricarica"></i>
                                        </span>
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="is-fullwidth" style="overflow-y: auto">
                        <table class="table is-fullwidth is-narrow is-hoverable">
                            <thead>
                            <tr>
                                <th>Nominativo</th>
                                <th>IVA</th>
                                <th>C. Fiscale</th>
                                <th style="width: 10%"></th>
                            </tr>
                            </thead>
                            <tbody id="aziende_tbody">

                            </tbody>
                        </table>
                    </div>
                    <div class="field">
                        <div class="control has-text-right">
                            <button class="button is-primary" id="seleziona_azienda_aggiungi" disabled>
                                <span>
                                    Continua
                                </span>
                                <span class="icon">
                                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>

<script src="<?= BASE_DIR ?>js/lib/papaparse.min.js"></script>
<script src="<?= BASE_DIR ?>js/table/tableSelection.js"></script>
<script src="<?= BASE_DIR ?>js/table/getHandler.js"></script>

<script src="js/importer.js"></script>
<script src="js/associa.js"></script>
<script src="js/valida_stu.js"></script>
<script src="js/valida_fact.js"></script>
</body>
</html>