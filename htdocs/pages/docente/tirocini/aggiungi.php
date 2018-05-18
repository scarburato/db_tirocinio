<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 12/02/18
 * Time: 19.00
 */
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$permissions = new \auth\PermissionManager($server, $user);
$permissions->check("train.add", \auth\PermissionManager::UNAUTHORIZED_REDIRECT);

$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));
$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

// Variabili pagina
$page = "Creazione tirocinio";

?>
<html lang="it">
<head>
    <?php include "../../../utils/pages/head.phtml"; ?>

	<script src="<?= BASE_DIR ?>js/table/getHandler.js"></script>
	<script src="<?= BASE_DIR ?>js/table/tableSelection.js"></script>
	<script src="<?= BASE_DIR ?>js/togglePanel.js"></script>
</head>
<body>
<?php include "../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 1;
            include "../menu.php";
            ?>
        </aside>
        <div class="column">
			<?php include "../../common/create_contact.php"; ?>
			<form action="aggiungi_db.php" method="post" id="main_form">
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Studente</label>
                    </div>
                    <div class="field-body">
                        <div class="field has-addons is-normal">
                            <div class="control is-expanded">
                                <input id="studente_name" class="input" type="text" required readonly
                                       placeholder="Selezionare studente tirocinante">
                                <input hidden type="number" title="studente" name="studente">
                                <p class="help">
                                    Campo obbligatorio
                                </p>
                            </div>
                            <div class="control">
                                <button type="button" class="button is-info" id="seleziona_studente_trigger">
                                    <span class="icon">
                                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                                    </span>
                                    <span>
                                        Seleziona...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Azienda</label>
                    </div>
                    <div class="field-body">
                        <div class="field has-addons is-normal">
                            <div class="control is-expanded">
                                <input id="azienda_name" class="input" type="text" required readonly
                                       placeholder="Selezionare azienda ospitante">
                                <input hidden type="number" title="azienda" name="azienda">
                                <p class="help">
                                    Campo obbligatorio
                                </p>
                            </div>
                            <div class="control">
                                <button type="button" class="button is-info" id="seleziona_azienda_trigger">
                                    <span class="icon">
                                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                                    </span>
                                    <span>
                                        Seleziona...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Tutore</label>
                    </div>
                    <div class="field-body">
                        <div class="field has-addons is-normal">
                            <div class="control is-expanded">
                                <input class="input" type="text" readonly
                                       placeholder="Selezionare tutore aziendale">
                                <input hidden type="number" title="studente" name="tutore">
                            </div>
                            <div class="control">
                                <button type="button" class="button is-info" id="seleziona_tutore_trigger" onclick="alert('No!')">
                                    <span class="icon">
                                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                                    </span>
                                    <span>
                                        Seleziona...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Docente</label>
                    </div>
                    <div class="field-body">
                        <div class="field has-addons is-normal">
                            <div class="control is-expanded">
                                <input id="docente_name" class="input" type="text" required readonly
                                       placeholder="Selezionare docente referente">
                                <input hidden type="number" title="docente" name="docente">
                                <p class="help">
                                    Campo obbligatorio
                                </p>
                            </div>
                            <div class="control">
                                <button type="button" class="button is-info" id="seleziona_docente_trigger">
                                    <span class="icon">
                                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                                    </span>
                                    <span>
                                        Seleziona...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Periodo</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <input id="test" required class="input" type="date" name="data_inizio"
                                       placeholder="Inizio" title="Data di inzio">
                            </div>
                            <p class="help">
                                Campo obbligatorio
                            </p>
                        </div>
                        <div class="field">
                            <div class="control">
                                <input class="input" type="date" name="data_fine" placeholder="Fine" title="Data di fine">
                            </div>
                            <p class="help">
                                La data di termine non è obbligatoria
                            </p>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <button type="submit" class="button is-primary is-large is-pulled-right">
                        <span class="icon">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </span>
                        <span>
                            Crea
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!--- PopOut: Seleziona Studente -->
<div class="modal" id="studente_modal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Selezione studente</p>
        </header>
        <section class="modal-card-body" style="height: 100%; max-height: 100%">
            <div class="level">
                <!-- Left side -->
                <div class="level-left">
                    <form id="studente_cerca">
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
                </div>

                <div class="level-right">
                    <div class="field has-addons">
                        <p class="control">
                            <button class="button" disabled id="studente_back">Indietro</button>
                        </p>
                        <p class="control">
                            <button class="button" disabled id="studente_forward">Avanti</button>
                        </p>
                    </div>
                </div>
            </div>
            <div class="is-fullwidth" style="overflow-y: auto">
                <table class="table is-fullwidth is-narrow is-hoverable">
                    <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Matricola</th>
                        <th style="width: 10%"></th>
                    </tr>
                    </thead>
                    <tbody id="studenti_tbody">

                    </tbody>
                </table>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="seleziona_studente_aggiungi">Seleziona</button>
            <button class="button" id="seleziona_studente_scarta">Scarta</button>
        </footer>
    </div>
</div>

<!--- PopOut: Seleziona Docente -->
<div class="modal" id="docente_modal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Selezione docente</p>
        </header>
        <section class="modal-card-body" style="height: 100%; max-height: 100%">
            <div class="level">
                <!-- Left side -->
                <div class="level-left">
                    <form id="docente_cerca">
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
                </div>

                <div class="level-right">
                    <div class="field has-addons">
                        <p class="control">
                            <button class="button" disabled id="docente_back">Indietro</button>
                        </p>
                        <p class="control">
                            <button class="button" disabled id="docente_forward">Avanti</button>
                        </p>
                    </div>
                </div>
            </div>
            <div class="is-fullwidth" style="overflow-y: auto">
                <table class="table is-fullwidth is-narrow is-hoverable">
                    <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Posta</th>
                        <th style="width: 10%"></th>
                    </tr>
                    </thead>
                    <tbody id="docenti_tbody">

                    </tbody>
                </table>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="seleziona_docente_aggiungi">Seleziona</button>
            <button class="button" id="seleziona_docente_scarta">Scarta</button>
        </footer>
    </div>
</div>

<!--- PopOut: Seleziona Aziende -->
<div class="modal" id="azienda_modal">
	<div class="modal-background"></div>
	<div class="modal-card">
		<header class="modal-card-head">
			<p class="modal-card-title">Selezione azienda</p>
		</header>
		<section class="modal-card-body" style="height: 100%; max-height: 100%">
			<div class="level">
				<!-- Left side -->
				<div class="level-left">
					<form id="azienda_cerca">
						<div class="field has-addons">
							<p class="control">
								<input class="input" name="query" type="text" placeholder="Filtra">
							</p>
							<p class="control">
								<button type="submit" class="button">
									Cerca
								</button>
							</p>
						</div>
					</form>
				</div>2\

				<div class="level-right">
					<div class="field has-addons">
						<p class="control">
							<button class="button" disabled id="azienda_back">Indietro</button>
						</p>
						<p class="control">
							<button class="button" disabled id="azienda_forward">Avanti</button>
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
		</section>
		<footer class="modal-card-foot">
			<button class="button is-success" id="seleziona_azienda_aggiungi">Seleziona</button>
			<button class="button" id="seleziona_azienda_scarta">Scarta</button>
		</footer>
	</div>
</div>

<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
</body>
<script>
	let main_form = $ ("#main_form");
</script>

<script src="js/selezione_studente.js"></script>
<script src="js/selezione_docente.js"></script>
<script src="js/selezione_azienda.js"></script>

</html>