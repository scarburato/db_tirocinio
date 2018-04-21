<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 28/01/18
 * Time: 17.02
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_STUDENT, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveStudenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());


if (!isset($_GET["tirocinio"]))
    redirect('../index.php');

$tirocinio_azienda = $server->prepare(
    'SELECT A.nominativo, A.IVA, A.codiceFiscale,
    S.nome, S.cognome, S.indirizzo_posta, S.fotografia,
    C.nome, C.cognome, C.email, C.telefono, C.FAX,
    D.nome, D.cognome, D.indirizzo_posta, D.fotografia,
    T.dataInizio, T.dataTermine, T.giudizio, T.descrizione, T.visibilita, MD5(T.descrizione), T.ultima_modifica
    FROM Tirocinio T 
        LEFT JOIN Azienda A ON T.azienda = A.id
        LEFT JOIN UtenteGoogle D ON T.docenteTutore = D.id
        LEFT JOIN UtenteGoogle S ON T.studente = S.id
        LEFT JOIN Contatto C ON T.tutoreAziendale = C.id
    WHERE T.id = ? AND T.studente = ?;');

$tirocinio_azienda->bind_param('ii', $_GET['tirocinio'], $user_info['id']);

$tirocinio_azienda->execute();

$tirocinio_azienda->bind_result($a_nom, $a_iva, $a_cf,
    $studente_nome, $studente_cognome, $studente_posta, $studente_fotografia,
    $c_nome, $c_cognome, $c_posta, $c_tel, $c_fax,
    $doc_nome, $doc_cog, $doc_posta, $doc_fotografia,
    $t_ini, $t_end, $t_giud, $t_desc, $t_vis, $desc_md5, $t_last_edit);

if (!$tirocinio_azienda->fetch()) // errore, utente non valido e/o tirocinio non trovatos
    throw new RuntimeException("Il tirocinio non esiste ovvero non si posseggono i privilegi per visualizzarlo");

$tirocinio_azienda->close();

if ($t_desc === NULL)
    $t_desc = "";

/* variabile per controllare cosa è possibile fare.
 * 0 = futuro, solo Info ed eventualmente commenti visibili
 * 1 = in corso o simile, resoconto privato, modificabile
 * 2 = resoconto visibile globalmente, modifica non effettuabile
 * Questi dati sono già calcolati in tirocinio.php, ma sono ricalcolati per evitare possibili intrusioni dannose
*/
$status = ($t_ini > date('Y-m-d') ? 0 : ($t_vis === 'azienda' ? 2 : 1));
// Questo permette un comportamento ottimizzato con lo switch seguente
if (!isset($_GET['page']))
    $_GET['page'] = 'no';

if ($_GET['page'] == 'comments')
    $passed = 'comments';
else
{
    switch ($status)
    {
        case 0:
        default:
            $passed = 'info';
        case 1:
            $passed = ($_GET['page'] == 'resoconto' ? 'editor' : 'info');
            break;
        case 2:
            $passed = ($_GET['page'] == 'preview' || $_GET['page'] == 'resoconto' ? 'preview' : 'info');
            break;
    }
    unset($_GET['page']);
}


// Variabili pagina
$page = "Gestione Tirocinio - " . $a_nom;
$num_tir = $_GET['tirocinio'];


?>

<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/head.phtml"; ?>
    <link rel="stylesheet" href="<?= BASE_DIR ?>css/editor/themes/modern.min.css" type="text/css" media="all">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu|Ubuntu+Condensed|Ubuntu+Mono" rel="stylesheet">

    <script src="<?= BASE_DIR ?>js/editor/sceditor.min.js"></script>
    <script src="<?= BASE_DIR ?>js/editor/bbcode.min.js"></script>
    <script src="<?= BASE_DIR ?>js/editor/icons/monocons.min.js"></script>
    <script src="<?= BASE_DIR ?>js/editor/icons/material.min.js"></script>
    <script src="<?= BASE_DIR ?>js/lib/jquery.md5.js"></script>
    <script>
        const PASSED = '<?= $passed?>';
		md5_ATT = '<?=$desc_md5?>';
		const TIR = '<?=$num_tir?>';
    </script>

    <?php // Necessario per migrare la stampa ?>
    <script src="https://code.jquery.com/jquery-migrate-1.0.0.js"></script>
    <script src="<?= BASE_DIR ?>js/lib/jquery.printElement.min.js"></script>
</head>
<body>
<?php include "../../common/google_navbar.php"; ?>
<br>
<!-- Menù Laterale -->
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <p class="menu-label">
                Tirocini
            </p>
            <ul class="menu-list">
                <li>
                    <a href="../index.php?time=1">
                        <span class="icon">
                            <i class="fa fa-play" aria-hidden="true"></i>
                        </span>
                        <span>
                            In corso
                        </span>
                    </a>
                </li>
                <li>
                    <a href="../index.php?time=2">
                        <span class="icon">
                            <i class="fa fa-fast-forward" aria-hidden="true"></i>
                        </span>
                        <span>
                            Futuri
                        </span>
                    </a>
                </li>
                <li>
                    <a href="../index.php?time=0">
                        <span class="icon">
                            <i class="fa fa-stop" aria-hidden="true"></i>
                        </span>
                        <span>
                            Terminati
                        </span>
                    </a>
                </li>
            </ul>
            <div class="is-hidden-mobile" style="min-height: 15em">

            </div>
        </aside>

        <!-- Tab Navigation Bar -->
        <div class="column">
            <article id="weknow" class="message is-danger" hidden>
                <div class="message-body">
                    Sappiamo cosa hai fatto! <span class="icon"><i class="fa fa-file-code-o"
                                                                   aria-hidden="true"></i></span><br>
                </div>
            </article>
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
                    <?php if ($status == 1) { ?>
                        <li data-tab="editor">
                            <a>
                              <span class="icon">
                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                              </span>
                                <span>
                                  Videoscrittura
                              </span>
                            </a>
                        </li>
                    <?php }
                    if ($status != 0)
                    { ?>
                        <li data-tab="preview">
                            <a> <!-- TODO confronto per l'utente -->
                                <span class="icon">
                                  <i class="fa fa-file-text" aria-hidden="true"></i>
                              </span>
                                <span>
                                  Anteprima
                              </span>
                            </a>
                        </li>
                        <li data-tab="comments">
                            <a> <!-- TODO visualizzazione -->
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
                    <div class="media">
                        <figure class="media-left">
                            <p>
                                <span class="icon is-large" style="width: 96px;">
                                    <i class="fa fa-calendar fa-3x" aria-hidden="true"></i>
                                </span>
                            </p>
                        </figure>
                        <div class="media-content">
                            <p><strong>Inizio:&#9;</strong><time datetime="<?= $t_ini ?>"><?= $t_ini ?></time></p>
                            <p><strong>Termine:&#9;</strong>
                                <?php if($t_end !== null)
                                {
                                    ?>
                                    <time datetime="<?= $t_end ?>"><?= $t_end ?></time>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <span>indeterminato</span>
                                    <?php
                                }?>
                            </p>
                        </div>
                    </div>

                    <div class="media">
                        <figure class="media-left">
                            <p>
                                <span class="icon is-large" style="width: 96px;">
                                    <i class="fa fa-building fa-3x" aria-hidden="true"></i>
                                </span>
                            </p>
                        </figure>
                        <div class="media-content">
                            <h4 class="title is-4">Azienda ospitante</h4>
                            <h4 class="subtitle is-4"><?= sanitize_html($a_nom)?></h4>
                            <p><strong>Codice fiscale: </strong><?= sanitize_html($a_cf) ?></p>
                            <p><strong>Partita IVA: </strong><?= sanitize_html($a_iva) ?></p>
                        </div>
                    </div>

                    <?php if (isset($c_nome))
                    { ?>
                        <div class="media">
                            <figure class="media-left">
                                <p>
                                <span class="icon is-large" style="width: 96px;">
                                    <i class="fa fa-id-badge fa-3x" aria-hidden="true"></i>
                                </span>
                                </p>
                            </figure>
                            <div class="media-content">
                                <h4 class="title is-4">Tutore aziendale</h4>
                                <p><?= sanitize_html($c_nome . " " . $c_cognome) ?></p>
                                <p>
                                    <a href="mailto:<?= sanitize_html($c_posta) ?>"><?= sanitize_html($c_posta) ?></a>
                                </p>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="media">
                        <figure class="media-left">
                            <p class="image is-96x96">
                                <img src="<?= $studente_fotografia ?>">
                            </p>
                        </figure>
                        <div class="media-content">
                            <h4 class="title is-4">Studente</h4>
                            <p><?= sanitize_html($studente_nome . " " . $studente_cognome) ?></p>
                            <p>
                                <a href="mailto:<?= sanitize_html($studente_posta) ?>"><?= sanitize_html($studente_posta) ?></a>
                            </p>
                        </div>
                    </div>

                    <div class="media">
                        <figure class="media-left">
                            <p class="image is-96x96">
                                <img src="<?= $doc_fotografia ?>">
                            </p>
                        </figure>
                        <div class="media-content">
                            <h4 class="title is-4">Docente responsabile</h4>
                            <p><?= sanitize_html($doc_nome . " " . $doc_cog) ?></p>
                            <p>
                                <a href="mailto:<?= sanitize_html($doc_posta) ?>"><?= sanitize_html($doc_posta) ?></a>
                            </p>
                        </div>
                    </div>
                </div>

                <?php if ($status == 1) { ?>
                    <div class="control" data-tab="editor" hidden>
                        <textarea id="resoconto" class="textarea" rows="30"
                                  title="resonto" <?php if ($t_vis == "azienda") echo 'readonly'; ?> ><?= filter_var($t_desc, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></textarea>
                    </div>
                <?php }
                if ($status !== 0)
                { ?>
                    <div data-tab="preview" hidden>
                        <?php
                        if ($status === 1)
                        {
                            ?>
                            <div class="box">
                                <div class="media">
                                    <p class="media-content">
                                        <strong>
                                            Ultima modifica:
                                        </strong>
                                        <time id="last_edit" datetime="<?= $t_last_edit ?>"><?= $t_last_edit ?></time>
                                    </p>
                                    <p class="media-right">
                                        <button class="button" id="print">
                                            <span class="icon">
                                                <i class="fa fa-print" aria-hidden="true"></i>
                                            </span>
                                            <span>
                                                Stampa
                                            </span>
                                        </button>
                                        <button class="button is-link" id="bt_save">
                                            <span class="icon">
                                                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                            </span>
                                                <span>
                                                Salva Modifiche
                                            </span>
                                        </button>
                                        <a class="button is-success" id="bt_publish" <?= $t_vis !== "studente" ? "disabled" : "href=\"publish.php?tirocinio={$_GET['tirocinio']}\"" ?>>
                                            <span class="icon">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </span>
                                                <span>
                                                Pubblica al docente
                                            </span>
                                        </a>
                                    </p>
                                </div>
                            </div>
                        <?php } ?>
                        <p class="content" id="preview_editor"><?php if ($t_vis == 'azienda') echo sanitize_html($t_desc); ?></p>
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
<script src="<?= BASE_DIR ?>js/DynamicPagination.js"></script>

<script src="js/resoconto.js"></script>
<script src="js/comments.js"></script>
</html>
