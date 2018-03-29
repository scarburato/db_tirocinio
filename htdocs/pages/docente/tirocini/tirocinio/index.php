<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 18/03/18
 * Time: 17.58
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveStudenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

$bbCode = new Genert\BBCode\BBCode();

if (!isset($_GET["tirocinio"]))
    redirect('../index.php');

// TODO Controllo permessi!
$can_see_all = true;

$tirocinio_azienda = $server->prepare(
    'SELECT A.nominativo,
    C.nome, C.secondoNome, C.cognome, C.email, C.telefono, C.FAX,
    D.nome, D.cognome, D.indirizzo_posta,
    T.dataInizio, T.dataTermine, T.giudizio, T.descrizione, T.visibilita, MD5(T.descrizione)
    FROM Tirocinio T LEFT JOIN Azienda A ON T.azienda = A.id
        LEFT JOIN UtenteGoogle D ON T.docenteTutore = D.id
        LEFT JOIN Contatto C ON T.tutoreAziendale = C.id
    WHERE T.id = ? AND (T.docenteTutore = ? OR ?);');

$tirocinio_azienda->bind_param(
    'iii',
    $_GET['tirocinio'],
    $user->get_database_id(),
    $can_see_all
);

$tirocinio_azienda->execute(true);

$tirocinio_azienda->bind_result($a_nom,
    $c_nome, $c_secNom, $c_cognome, $c_posta, $c_tel, $c_fax,
    $doc_nome, $doc_cog, $doc_posta,
    $t_ini, $t_end, $t_giud, $t_desc, $t_vis, $desc_md5);

if (!$tirocinio_azienda->fetch()) // errore, utente non valido e/o tirocinio non trovatos
    die("unautharized!");

$tirocinio_azienda->close();

if ($t_desc === NULL)
    $t_desc = "";

/* variabile per controllare cosa è possibile fare.
 * 0 = futuro, solo Info ed eventualmente commenti visibili
 * 1 = in corso o simile, resoconto privato, modificabile
 * 2 = resoconto visibile globalmente, modifica non effettuabile
 * Questi dati sono già calcolati in tirocinio.php, ma sono ricalcolati per evitare possibili intrusioni dannose
*/
$status = ($t_ini > date('Y-m-d') ? 0 : ($t_vis == 'azienda' ? 2 : 1));
// Questo permette un comportamento ottimizzato con lo switch seguente
if (!isset($_GET['page']))
    $_GET['page'] = 'no';

if ($_GET['page'] == 'comments')
    $passed = 'comments';
elseif ($_GET['page'] == 'resoconto' && $status > 0)
    $passed = "preview";
else
    $passed = 'info';

// Variabili pagina
$page = "Gestione Tirocinio - " . $a_nom;
$num_tir = $_GET['tirocinio'];
?>

<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/head.phtml"; ?>
    <script src="<?= BASE_DIR ?>js/editor/sceditor.min.js"></script>
    <script src="<?= BASE_DIR ?>js/editor/bbcode.min.js"></script>
    <script>
        const PASSED = '<?= $passed?>';
    </script>
</head>
<body>
<?php include "../../../common/google_navbar.php"; ?>
<br>
<!-- Menù Laterale -->
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 1;
            include "../../menu.php";
            ?>
        </aside>

        <!-- Tab Navigation Bar -->
        <div class="column">
            <div class="tabs" id="selector">
                <ul>
                    <li data-tab="info">
                        <a>
                          <span class="icon">
                              <i class="fa fa-info" aria-hidden="true"></i>
                          </span>
                            <span>
                              Informazioni
                          </span>
                        </a>
                    </li>
                    <?php
                    if ($status != 0)
                    { ?>
                        <li data-tab="preview">
                            <a>
                                <span class="icon">
                                  <i class="fa fa-file-text" aria-hidden="true"></i>
                              </span>
                                <span>
                                  Resoconto
                              </span>
                            </a>
                        </li>
                        <li data-tab="comments">
                            <a>
                                <span class="icon">
                                  <i class="fa fa-comments" aria-hidden="true"></i>
                              </span>
                                <span>
                                  Commenti
                              </span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>

            <!-- Contenuti -->
            <div id="contents">
                <div data-tab="info" hidden>
                    <h1> <?= sanitize_html($a_nom) ?> </h1>
                    <?php if (isset($c_nome)) { ?>
                        <p> Tutore aziendale del tirocinio: <?= sanitize_html($c_nome) ?> <?= sanitize_html($c_cognome) ?>
                            email: <a href=mailto:<?= sanitize_html($c_posta) ?>> <?= sanitize_html($c_posta) ?> </a>
                        </p>
                    <?php } ?>
                    <br>
                    <p> Docente tutore: <?= sanitize_html($doc_nome) ?> <?= sanitize_html($doc_cog) ?> <br>
                        email: <a href=mailto:<?= sanitize_html($doc_posta) ?>> <?= sanitize_html($doc_posta) ?> </a>
                    </p>

                </div>
                <?php
                if ($status != 0)
                { ?>
                    <div data-tab="preview" hidden>
                        <div class="content" id="preview_editor"><?= sanitize_html($t_desc) ?></div>
                    </div>
                <?php } ?>
                <div data-tab="comments" hidden>
                    <div class="field">
                        <div class="control">
                            <textarea id="commento" class="textarea" rows="4"
                                      placeholder="Scrivi commento..."></textarea>
                        </div>
                        <p class="help">
                            I commenti saranno visibili ai docenti!
                        </p>
                    </div>
                    <div class="field">
                        <div class="control">
                            <button class="button" id="bt_comments">
                                <span>Invia</span>
                                <span class="icon">
                                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                </span>
                            </button>
                            <button class="button" id="bt_comments_reload">
                                <span>Aggiorna</span>
                                <span class="icon">
                                    <i class="fa fa-refresh" aria-hidden="true"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div id="dynamic_comments_loading" class="has-text-centered">
                        <p>
                            <span class="icon">
                                <i class="fa fa-circle-o-notch fa-spin fa-fw"></i>
                            </span>
                            <span>
                                Interrogazione in corso...
                            </span>
                        </p>
                    </div>
                    <div id="dynamic_comments">

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
</body>

<script src="<?= BASE_DIR ?>js/toggleTab.js"></script>
<script src="js/main.js"></script>
</html>