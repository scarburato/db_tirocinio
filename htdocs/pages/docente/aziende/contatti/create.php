<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 05/04/18
 * Time: 10.38
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

$permissions = new \auth\PermissionManager($server, $user);

$permissions->check("factory.intouch", \auth\PermissionManager::UNAUTHORIZED_REDIRECT);

// Variabili pagina
$page = "Entra in contatto";

// Se è stato passato un ID di un contatto da proporre immediatamente lo mostro adesso!
$is_set_contatto = isset($_GET["contact"]);

if ($is_set_contatto)
{
    $contatto = $server->prepare("
   SELECT C.id, C.nome, C.cognome, A.id, A.nominativo
    FROM Contatto C
    INNER JOIN Azienda A on C.azienda = A.id
   WHERE C.id = ?
    ");

    $contatto->bind_param("i", $_GET["contact"]);
    $contatto->execute();
    $contatto->bind_result($c_id, $c_nome, $c_cognome, $a_id, $a_nome);

    // Ri-aggiorno a true se essiste un contatto con tale id, altrimenti diventa false ed inibisce la logica inferiroe
    $is_set_contatto = $contatto->fetch() === true;

    $contatto->close();
}

?>

<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/head.phtml"; ?>
    <script src="<?= BASE_DIR ?>js/table/getHandler.js"></script>
    <script src="<?= BASE_DIR ?>js/table/tableSelection.js"></script>
    <script src="<?= BASE_DIR ?>js/togglePanel.js"></script>
</head>
<body>
<?php include "../../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 7;
            include "../../menu.php";
            ?>
        </aside>
        <div class="column">
            <?php
            if (isset($_GET["errors"]))
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
                        <pre><?= sanitize_html($errori) ?></pre>
                    </div>
                    <script>
						$ ("#errore_db_delete").on ("click", function ()
						{
							$ ("#errore_db").remove ();
						});
                    </script>
                </article>
                <?php
            }
            ?>
            <form action="create_db.php" method="post">
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Azienda</label>
                    </div>
                    <div class="field-body">
                        <div class="field has-addons is-normal">
                            <div class="control is-expanded">
                                <input id="azienda-name" class="input" type="text" required readonly
                                       placeholder="Selezionare l'azienda"
                                       value="<?= $is_set_contatto ? sanitize_html($a_nome) : "" ?>"
                                >
                                <input id="azienda_id" hidden type="number" title="azienda"
                                       value="<?= $is_set_contatto ? $a_id : "" ?>"
                                >
                                <p class="help">
                                    Campo obbligatorio
                                </p>
                            </div>
                            <div class="control">
                                <button type="button" class="button is-info" id="seleziona_azienda_trigger"
                                        title="Selezionare un'azienda">
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
                        <label class="label">Contatto</label>
                    </div>
                    <div class="field-body">
                        <div class="field has-addons is-normal">
                            <div class="control is-expanded">
                                <input id="contatto-name" class="input" type="text" required readonly
                                       placeholder="Selezionare con chi si è entrati in contatto"
                                       value="<?= $is_set_contatto ? sanitize_html($c_nome . " " . $c_cognome) : "" ?>"
                                >
                                <input id="contatto-id" hidden type="number" title="contatto" name="contatto"
                                       value="<?= $is_set_contatto ? $c_id : "" ?>"
                                >
                                <p class="help">
                                    Campo obbligatorio
                                </p>
                            </div>
                            <div class="control">
                                <button type="button"
                                        class="button is-info"
                                        id="seleziona_contatto_trigger"
                                        title="Selezionare un contatto"
                                    <?= !$is_set_contatto ? "disabled" : "" ?>
                                >
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

                <div class="message is-warning" hidden id="contatto-occupato">
                    <div class="message-body">
                        <p class="has-text-justified">
                            Risulta che il contatto <span id="duplicate-contact-name">test</span> dell'azienda <span
                                    id="duplicate-factory-name"></span>
                            sia già in contatto con qualcuno a scuola.
                        </p>
                        <p class="has-text-right">
                            <a id="contatto-occupato-href"
                               class="button is-warning is-small"
                               target="_blank"
                               href="<?= BASE_DIR ?>pages/docente/azienda/contatto"
                               title="mostra scheda interessato"
                            >
                                Guarda
                            </a>
                        </p>
                    </div>
                </div>

                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Periodo</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <input title="Data di inizio" id="data_inizio" required type="date"
									   class="input data_dinamica" name="data_inizio" placeholder="Inizio">
                            </div>
                            <p class="help">
                                Campo obbligatorio
                            </p>
                        </div>
                        <div class="field">
                            <div class="control">
                                <input title="Data di termine" class="input data_dinamica" type="date"
									   name="data_fine" id="data_fine" placeholder="Fine">
                            </div>
                            <p class="help">
                                La data di termine non è obbligatoria
                            </p>
                        </div>
                    </div>
                </div>

				<div class="message is-danger" hidden id="contatto-sovrapposto">
					<div class="message-body">
						<p class="has-text-justified">
							Il contatto selezionato è già in contatto l'utente attuale nello stesso periodo temporale. Continurare genererà un eccezzione di sovrapposizione temporale.<br>
							Se si vuole proseguire terminare il contatto che crea sovrapossizione!
						</p>
						<p class="has-text-right">
							<a id="contatto-sovrapposto-href"
							   class="button is-danger is-small"
							   target="_blank"
							   href="<?= BASE_DIR ?>pages/docente/azienda/contatto"
							   title="mostra scheda interessato"
							>
								Guarda
							</a>
						</p>
					</div>
				</div>

                <div class="field">
                    <div class="control has-text-right">
                        <button type="submit" class="button is-primary">Crea</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>

<!--- PopOut: Seleziona Aziende -->
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/pages/docente/modals/azienda.phtml"; ?>

<!--- PopOut: Seleziona Contatto aziendale -->
<div class="modal" id="contatto_modal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Selezione contatto</p>
        </header>
        <section class="modal-card-body" style="height: 100%; max-height: 100%">
            <div class="level">
                <!-- Left side -->
                <div class="level-left">
                    <form id="contatto_cerca">
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
                            <button class="button" disabled id="contatto_back">Indietro</button>
                        </p>
                        <p class="control">
                            <button class="button" disabled id="contatto_forward">Avanti</button>
                        </p>
                    </div>
                </div>
            </div>
            <div class="is-fullwidth" style="overflow-y: auto">
                <table class="table is-fullwidth is-narrow is-hoverable">
                    <thead id="contatti_thead">
                    <tr>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Qualifica</th>
                        <th style="width: 10%"></th>
                    </tr>
                    </thead>
                    <tbody id="contatti_tbody">

                    </tbody>
                </table>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="seleziona_contatto_aggiungi">Seleziona</button>
            <button class="button" id="seleziona_contatto_scarta">Scarta</button>
        </footer>
    </div>
</div>

<script src="js/seleziona_contatto_aziendale.js"></script>
<script src="js/controllo_sovrapposizione.js"></script>

</body>
</html>
