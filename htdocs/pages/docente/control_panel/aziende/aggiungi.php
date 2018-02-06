<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 29/01/18
 * Time: 15.42
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) ."/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER, "./../../../../");
$user = \auth\connect_token_google($google_client, $_SESSION["user"]["token"], "./../../../../", $oauth2);
$server = new \mysqli_wrapper\mysqli();

// Variabili pagina
$page = "Gestione Aziende - Aggiungi";
?>
<html lang="it" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
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
            <?php
            if(isset($_GET["errors"]))
            {
                $errori = urldecode($_GET["errors"]);
                ?>
                <article class="message is-danger" id="errore_db">
                    <div class="message-header">
                        <p>
                            <span class="icon">
                                <i class="fa fa-database"></i>
                            </span>
                            <span>
                                Errore di processo
                            </span>
                        </p>
                        <button class="delete" aria-label="delete" id="errore_db_delete"></button>
                    </div>
                    <div class="message-body">
                        <p>Si sono verificati dei problemi durante il processo dei dati!</p>
                        <pre><?= $errori ?></pre>
                    </div>
                    <script>
                        $("#errore_db_delete").on("click", function ()
						{
                           $("#errore_db").remove();
						});
                    </script>
                </article>
                <?php
            }
            ?>
            <form id="main_form" method="post" action="./aggiungi_db.php">
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">
                            Dati anagrafici
                        </label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <input class="input" type="text" required maxlength="100" name="nominativo" placeholder="Nominativo ">
                            <p class="help">
                                Campo obbligatorio
                            </p>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal"></div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control has-icons-right">
                                <input class="input" type="text" minlength="11" maxlength="11" name="codice_fiscale" placeholder="Codice Fiscale">
                                <span class="icon is-small is-right" data-status="ok">
                                    <i class="fa fa-check"></i>
                                </span>
                                <span class="icon is-small is-right" data-status="error">
                                    <i class="fa fa-exclamation-triangle"></i>
                                </span>
                                <span class="icon is-small is-right" data-status="warn">
                                    <i class="fa fa-exclamation-triangle"></i>
                                </span>
                                <span class="icon is-small is-right" data-status="load">
                                    <i class="fa fa-spinner fa-pulse"></i>
                                </span>
                            </div>
                            <p class="help is-danger is-hidden" data-help="error-db">
                                È stata trovata un'altra occorrenza già registrata!
                            </p>
                        </div>
                        <div class="field">
                            <div class="control has-icons-right">
                                <input class="input" type="text" maxlength="11" name="iva" placeholder="Partita I.V.A.">
                                <span class="icon is-small is-right" data-status="ok">
                                    <i class="fa fa-check"></i>
                                </span>
                                <span class="icon is-small is-right" data-status="error">
                                    <i class="fa fa-exclamation-triangle"></i>
                                </span>
                                <span class="icon is-small is-right" data-status="warn">
                                    <i class="fa fa-exclamation-triangle"></i>
                                </span>
                                <span class="icon is-small is-right" data-status="load">
                                    <i class="fa fa-spinner fa-pulse"></i>
                                </span>
                            </div>
                            <p class="help is-danger is-hidden" data-help="error-db">
                                È stata trovata un'altra occorrenza già registrata!
                            </p>
                            <p class="help is-warning is-hidden" data-help="error-iva">
                                La partita IVA potrebbe non essere valida.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">
                            Sedi
                        </label>
                    </div>
                    <div class="field-body" >
                        <div class="field box is-fullwidth" style="height: 10rem; overflow-y: auto">
                            <a class="button is-small is-link is-pulled-right" id="aggiungi_sede_trigger">
                                <span class="icon">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </span>
                                <span>
                                    Aggiungi
                                </span>
                            </a>
                            <table class="table is-fullwidth" >
                                <tbody id="sedi_memoria">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">
                            Tipo gestione
                        </label>
                    </div>
                    <div class="field-body">
                        <div class="field is-normal">
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select title="tipo_gestione" name="gestione">
                                        <option>Lascia vuoto</option>
                                        <?php
                                        $opzioni = $server->enum_values("Azienda", "gestione");
                                        foreach ($opzioni as $opzione)
                                        {
                                            ?>
                                            <option value="<?= $opzione ?>">
                                                <?= $opzione ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">
                            Dimensione
                        </label>
                    </div>
                    <div class="field-body">
                        <div class="field is-normal">
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select title="dimensione" name="dimensione">
                                        <option>Lascia vuoto</option>
                                        <?php
                                        $opzioni = $server->enum_values("Azienda", "dimensione");
                                        foreach ($opzioni as $opzione)
                                        {
                                            ?>
                                            <option value="<?= $opzione ?>">
                                                <?= $opzione ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">
                            Classificazione
                        </label>
                    </div>
                    <div class="field-body">
                        <div class="field is-normal">
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select title="classificazione" name="classificazione">
                                        <?php
                                        $classificazioni = $server->prepare("SELECT id, descrizione FROM Classificazioni");
                                        $classificazioni->execute(true);

                                        $classificazioni->bind_result($id, $descrizione);
                                        while($classificazioni->fetch())
                                        {
                                            ?>
                                                <option value="<?= $id ?>">
                                                    <?= $descrizione ?>
                                                </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <p class="help">
                                    Campo obbligatorio
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">
                            ATECO 2007
                        </label>
                    </div>
                    <div class="field-body">
                        <div class="field has-addons is-normal">
                            <div class="control is-expanded">
                                <input class="input" type="text" maxlength="8" name="ateco_unique" required readonly placeholder="Premere seleziona">
                                <input hidden type="text" name="ateco" title="ateco">
                                <p class="help">
                                    Campo obbligatorio
                                </p>
                            </div>
                            <div class="control">
                                <a class="button is-info" id="seleziona_ateco_trigger">
                                    <span class="icon">
                                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                                    </span>
                                    <span>
                                        Seleziona...
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">
                            Autenticazione
                        </label>
                    </div>
                    <div class="field-body">
                        <div class="field has-addons is-normal">
                            <div class="control is-expanded">
                                <input class="input" type="text" minlength="8" required placeholder="Parola d'ordine" name="parolaordine" id="parolaordine">
                                <p class="help">
                                    Campo obbligatorio. La parola d'ordine deve essere almeno otto caratteri. La parola d'ordine dovrà essere cambiata al primo accesso
                                </p>
                            </div>
                            <div class="control">
                                <a class="button is-info" id="nuovaparola">
                                    <span class="icon">
                                        <i class="fa fa-key" aria-hidden="true"></i>
                                    </span>
                                    <span>
                                        Rigenera....
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal"></div>
                    <div class="field-body">
                        <div class="field">
                            <button class="button is-large is-primary is-fullwidth">
                                <span class="icon">
                                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                </span>
                                <span>
                                    Salva
                                </span>
                            </button>
                            <p class="help">
                                Se la procedura andrà a buon fine sarà visualizzato un documento contente le credenziali per il primo accesso.
                            </p>
                        </div>

                    </div>
                </div>
                <input type="hidden" name="sedi" hidden id="sedi_silent_out">
            </form>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/footer.phtml"; ?>

<!--- PopOut: Aggiunta sede -->
<div class="modal" id="aggiungi_sede">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Aggiungi Sede</p>
        </header>
        <section class="modal-card-body" style="overflow-y: auto">
            <form id="aggiungi_sede_form">
                <div class="field">
                    <label class="label">
                        Nome sede
                    </label>
                    <div class="control">
                        <input class="input" type="text" maxlength="128" required name="nominativo" placeholder="Nome della sede">
                    </div>
                    <p class="help">
                        Questo campo è obbligatorio
                    </p>
                </div>
                <div class="field">
                    <label class="label">
                        Locazione
                    </label>
                    <div class="control">
                        <input class="input" type="text" maxlength="128" name="indirizzo" placeholder="Indirizzo sede">
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <input class="input" type="text" maxlength="15" name="civico" placeholder="Numero civico">
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <input class="input" type="text" maxlength="128" name="comune" placeholder="Comune">
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <input class="input" type="text" maxlength="128" name="provincia" placeholder="Provincia">
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <input class="input" type="number" maxlength="5" name="cap" placeholder="Codice d'avviamento postale">
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <input class="input" type="text" maxlength="128" name="stato" placeholder="Stato" value="Italia" list="stati">
                    </div>
                </div>

                <datalist id="stati">
                    <?php
                    $stati = $server->prepare("SELECT DISTINCT stato FROM Sede");
                    $stati->execute();
                    $stati->bind_result($stato);
                    while ($stati->fetch())
                    {
                        ?>
                        <option value="<?= $stato ?>">
                        <?php
                    }
                    ?>
                </datalist>

            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="aggiungi_sede_aggiungi">Aggiungi</button>
            <button class="button" id="aggiungi_sede_scarta">Scarta</button>
        </footer>
    </div>
</div>

<!--- PopOut: Seleziona ATECO -->
<div class="modal" id="seleziona_ateco">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Aggiungi Sede</p>
        </header>
        <section class="modal-card-body" style="height: 100%; max-height: 100%">
            <form id="ateco_filtro">
                <div class="field has-addons">
                    <p class="control">
                        <input class="input" name="query" type="text" placeholder="Cerca ATECO">
                    </p>
                    <p class="control">
                        <button type="submit" class="button">
                            Cerca
                        </button>
                    </p>
                </div>
            </form>
            <div class="is-fullwidth" style="overflow-y: auto">
                <table class="table is-fullwidth is-narrow is-hoverable">
                    <thead>
                    <tr>
                        <th>Codice</th>
                        <th>Descrizione</th>
                    </tr>
                    </thead>
                    <tbody id="ateco_tbody">
                    <?php
                    $ateco = $server->prepare("SELECT id, cod2007, descrizione FROM CodiceAteco");
                    $ateco->execute();
                    $ateco->bind_result(
                            $id,
                            $codice,
                            $descrizione
                    );

                    while($ateco->fetch())
                    {
                        ?>
                        <tr style="cursor: pointer">

                            <td class="codice_ateco_value" data-dbid="<?= $id ?>"><?= $codice?></td>
                            <td><?= $descrizione?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="seleziona_ateco_aggiungi">Seleziona</button>
            <button class="button" id="seleziona_ateco_scarta">Scarta</button>
        </footer>
    </div>
</div>

<script src="<?= BASE_DIR ?>js/togglePanel.js"></script>
<script src="<?= dirname($_SERVER["REQUEST_URI"]) . "/"?>js/main.js"></script>
<script src="<?= dirname($_SERVER["REQUEST_URI"]) . "/"?>js/filtro_ateco.js"></script>
<script src="<?= dirname($_SERVER["REQUEST_URI"]) . "/"?>js/iva_cf_validatore.js"></script>
</body>
</html>