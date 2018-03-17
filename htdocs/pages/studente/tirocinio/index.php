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
    redirect('index.php');

$tirocinio_azienda = $server->prepare(
    'SELECT A.nominativo,
    C.nome, C.secondoNome, C.cognome, C.email, C.telefono, C.FAX,
    D.nome, D.cognome, D.indirizzo_posta,
    T.dataInizio, T.dataTermine, T.giudizio, T.descrizione, T.visibilita, MD5(T.descrizione)
    FROM Tirocinio T LEFT JOIN Azienda A ON T.azienda = A.id
        LEFT JOIN UtenteGoogle D ON T.docenteTutore = D.id
        LEFT JOIN Contatto C ON T.tutoreAziendale = C.id
    WHERE T.id = ? AND T.studente = ?;');

$tirocinio_azienda->bind_param('ii', $_GET['tirocinio'], $user_info['id']);

$tirocinio_azienda->execute(true);

$tirocinio_azienda->bind_result($a_nom,
    $c_nome, $c_secNom, $c_cognome, $c_posta, $c_tel, $c_fax,
    $doc_nome, $doc_cog, $doc_posta,
    $t_ini, $t_end, $t_giud, $t_desc, $t_vis, $desc_md5);

if (!$tirocinio_azienda->fetch()) // errore, utente non valido e/o tirocinio non trovatos
    redirect("/index.php");

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
            $passed = ($_GET['page'] == 'resoconto' ? 'preview' : 'info');
            break;
    }
    unset($_GET['page']);
}


// Variabili pagina
$page = "Gestione Tirocinio - " . $a_nom;
$num_tir = $_GET['tirocinio'];

// Preparazione dei commenti impaginabili
$commenti = new class($server,
    "SELECT CM.id, U.id, U.nome, U.cognome, U.fotografia, testo, quando
  FROM Commento CM INNER JOIN UtenteGoogle U ON CM.autore = U.id
  WHERE CM.tirocinio = ? ORDER BY quando DESC") extends \helper\Pagination
{
    public function compute_rows()
    {
        $row_tot = 0;
        $conta = $this->link->prepare(
            "SELECT COUNT(id) FROM Commento WHERE tirocinio=?");
        $conta->bind_param('i', $_GET['tirocinio']);
        $conta->execute(true);
        $conta->bind_result($row_tot);
        $conta->fetch();
        $conta->close();

        return $row_tot;
    }
};
$commenti->set_limit(5);
$commenti->set_current_page(isset($_GET['pagina']) ? $_GET['pagina'] : 0);

$commenti->bind_param('i', $num_tir);
$commenti->execute(true);
$commenti->bind_result($comm_id, $autore, $comm_nome, $comm_cognome, $comm_foto, $comm_testo, $comm_tstamp);

$nav = new \helper\PaginationIndexBuilder($commenti);
$nav->set_pagination_builder(new \helper\IndexJS());
?>

<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/head.phtml"; ?>
    <link rel="stylesheet" href="<?= BASE_DIR ?>css/editor/themes/modern.min.css" type="text/css" media="all">

    <script src="<?= BASE_DIR ?>js/editor/sceditor.min.js"></script>
    <script src="<?= BASE_DIR ?>js/editor/bbcode.min.js"></script>
    <script src="<?= BASE_DIR ?>js/editor/icons/monocons.min.js"></script>
    <script src="<?= BASE_DIR ?>js/editor/icons/material.min.js"></script>
    <script src="<?= BASE_DIR ?>js/lib/jquery.md5.js"></script>
    <script> const PASSED = '<?= $passed?>';
		md5_ATT = '<?=$desc_md5?>';
		const TIR = '<?=$num_tir?>' </script>
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
                    Sappiamo cosa hai fatto! <span class="icon"><i class="fa fa-file-code-o" aria-hidden="true"></i></span><br>
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
                    <h1> <?= $a_nom ?> </h1>
                    <?php if (isset($c_nome)) { ?>
                        <p> Tutore aziendale del tirocinio: <?= $c_nome ?> <?= $c_cognome ?>
                            email: <a href=mailto:> <?= $c_posta ?> </a>
                        </p>
                    <?php } ?>
                    <br>
                    <p> Docente tutore: <?= $doc_nome ?> <?= $doc_cog ?> <br>
                        email: <a href=mailto:> <?= $doc_posta ?> </a>
                    </p>

                </div>
                <?php if ($status == 1) { ?>
                    <div class="control" data-tab="editor" hidden>
                        <textarea id="resoconto" class="textarea" rows="30"
                                  title="resonto" <?php if ($t_vis == "azienda") echo 'readonly'; ?> ><?= $t_desc ?></textarea>
                    </div>
                <?php }
                if ($status != 0)
                { ?>
                    <div data-tab="preview" hidden>
                        <?php
                        if ($status == 1)
                        { /*
                    * TODO Spostare il bottone formattandolo meglio
                    */ ?>
                            <div class="field">
                                <button class="button" id="bt_save">Salva Modifiche</button>
                            </div>
                        <?php } ?>
                        <div class="content" id="preview_editor">
                            <?php if ($t_vis == 'azienda') echo $t_desc; ?>
                        </div>
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
                            <button class="button" id="bt_comments">Invia</button>
                        </div>
                    </div>
                    <div class="">
                        <!-- TODO js+php che estrae i commenti necessari-->
                        <?php while ($commenti->fetch()) { ?>
                            <div class="box">
                                <article class="media">
                                    <div class="media-left">
                                        <figure class="image is-96x96">
                                            <img src="<?= $comm_foto ?>" alt="">
                                        </figure>
                                    </div>
                                    <div class="media-content">
                                        <p>
                                            <strong>
                                                <?= $comm_nome . " " . $comm_cognome ?>
                                                -
                                                <time datetime="<?= $comm_tstamp ?>"><?= $comm_tstamp ?></time>
                                            </strong>
                                            <br>
                                            <?= $comm_testo ?>
                                        </p>
                                    </div>
                                </article>
                            </div>
                        <?php }
                        $_GET["page"] = "comments";
                        $nav->generate_index($_GET);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
</body>

<script src="<?= BASE_DIR ?>js/toggleTab.js"></script>
<script src="js/resoconto.js"></script>
</html>
