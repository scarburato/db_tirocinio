<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 28/01/18
 * Time: 17.02
 */

 // TODO scelta iniziale del tag attivo da tirocinio.php

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_STUDENT, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveStudenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

if(!isset($_GET["tirocinio"]))
{
    header("Location: index.php");
    die("");
}

$tirocinio_azienda = $server->prepare(
    'SELECT A.nominativo,
    C.nome, C.secondoNome, C.cognome, C.email, C.telefono, C.FAX,
    D.nome, D.cognome, D.indirizzo_posta,
    T.dataInizio, T.dataTermine, T.giudizio, T.descrizione, T.visibilita
    FROM Tirocinio T LEFT JOIN Azienda A ON T.azienda = A.id
        LEFT JOIN UtenteGoogle D ON T.docenteTutore = D.id
        LEFT JOIN Contatto C ON T.tutoreAziendale = C.id
    WHERE T.id = ? AND T.studente = ?;');

$tirocinio_azienda->bind_param('ii', $_GET['tirocinio'], $user_info['id']);

$tirocinio_azienda->execute(true);

$tirocinio_azienda->bind_result($a_nom,
    $c_nome, $c_secNom, $c_cognome, $c_posta, $c_tel, $c_fax,
    $doc_nome, $doc_cog, $doc_posta,
    $t_ini, $t_end, $t_giud, $t_desc, $t_vis);

if (!$tirocinio_azienda->fetch()) // errore, utente non valido e/o tirocinio non trovatos
  redirect("/index.php");

if ($t_desc === NULL)
  $t_desc = "";

/* variabile per controllare cosa è possibile fare.
 * 0 = futuro, solo Info ed eventualmente commenti visibili
 * 1 = in corso o simile, resoconto privato, modificabile
 * 2 = resoconto visibile globalmente, modifica non effettuabile
 * Questi dati sono già calcolati in tirocinio.php, ma sono ricalcolati per evitare possibili intrusioni dannose
 * TODO applicare le conseguenze di $status
*/
$status = ($t_ini > date('Y-m-d') ? 0 : ($t_vis=='azienda' ? 2 : 1));
// Questo permette un comportamento ottimizzato con lo switch seguente
if (!isset($_GET['page']))
  $_GET['page']='no';

switch ($status) {
  case 0:
  default:
    $passed = 'info';
  case 1:
    $passed = ($_GET['page']=='resoconto' ? 'editor' : 'info');
    break;
  case 2:
    $passed = ($_GET['page']=='resoconto' ? 'preview' : 'info');
    break;
}
unset($_GET['page']);

// Variabili pagina
$page = "Gestione Tirocinio - " . $a_nom;

?>

<html lang="it">
<head>
    <?php include "../../utils/pages/head.phtml"; ?>
    <link rel="stylesheet" href="<?= BASE_DIR ?>css/editor/themes/modern.min.css" type="text/css" media="all">

    <script src="<?= BASE_DIR ?>js/editor/sceditor.min.js"></script>
    <script src="<?= BASE_DIR ?>js/editor/bbcode.min.js"></script>
    <script src="<?= BASE_DIR ?>js/editor/icons/monocons.min.js"></script>
    <script src="<?= BASE_DIR ?>js/editor/icons/material.min.js"></script>
    <script> const PASSED='<?= $passed?>'</script>
</head>
<body>
<?php include "../common/google_navbar.php"; ?>
<br>
<!-- Menù Laterale -->
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight" style="min-height: 20em">
            <p class="menu-label">
                Tirocini
            </p>
            <ul class="menu-list">
                <li>
                    <a href="./index.php?time=1">
                        <span class="icon">
                            <i class="fa fa-play" aria-hidden="true"></i>
                        </span>
                        <span>
                            In corso
                        </span>
                    </a>
                </li>
                <li>
                    <a href="./index.php?time=2">
                        <span class="icon">
                            <i class="fa fa-fast-forward" aria-hidden="true"></i>
                        </span>
                        <span>
                            Futuri
                        </span>
                    </a>
                </li>
                <li>
                    <a href="./index.php?time=0">
                        <span class="icon">
                            <i class="fa fa-stop" aria-hidden="true"></i>
                        </span>
                        <span>
                            Terminati
                        </span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Tab Navigation Bar -->

        <div class="column is-fullwidth is-fullheight">
            <div class="tabs" id="selector">
                <ul>
                  <li data-tab="info">
                      <a> <!-- TODO fare tutto -->
                          <span class="icon">
                              <i class="fa fa-info" aria-hidden="true"></i>
                          </span>
                          <span>
                              Informazioni
                          </span>
                      </a>
                  </li>
                    <?php if ($status==1) { ?>
                      <li data-tab="editor" >
                          <a> <!-- TODO salvataggio -->
                              <span class="icon">
                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                              </span>
                              <span>
                                  Videoscrittura
                              </span>
                          </a>
                      </li>
                    <?php }
                    if ($status!=0) { ?>
                      <li data-tab="preview">
                          <a> <!-- TODO salvataggio / confronto -->
                              <span class="icon">
                                  <i class="fa fa-file-text" aria-hidden="true"></i>
                              </span>
                              <span>
                                  Anteprima
                              </span>
                          </a>
                      </li>
                      <li data-tab="comments">
                          <a> <!-- TODO fare tutto -->
                              <span class="class">
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
                <?php /* TODO implementare questo bottone (salvataggio)
                if ($t_vis!="azienda") { ?>
                <div class="field" data-tab="editor anteprima"> <!-- TODO aggiornare toggleTab per accettare un vettore? -->
                    <button class="button" id="bt_save"> Salva Modifiche</button>
                </div>
                <?php }  */ ?>

                <div data-tab="info" hidden>
                  <!-- TODO formattare -->
                  <h1> <?= $a_nom ?> </h1>
                  <?php if (isset($c_nome)) {
                    echo '<p> Tutore aziendale del tirocinio:', $c_nome, ' ', $c_cognome, '<br>',
                        'email: ', $c_posta;
                  } ?>
                  <br>
                  <p> Docente tutore: <?= $doc_nome?> <?= $doc_cog?> <br>
                    email: <?= $doc_posta ?>
                  </p>

                </div>
              <?php if ($status==1) { ?>
                <div class="control" data-tab="editor" hidden>
                    <!-- TODO aggiungere scritta di informazione se è impossibile modificare -->
                    <textarea id="resoconto" class="textarea" rows="20" title="resonto" <?php if ($t_vis=="azienda") echo 'readonly>'; else echo '>',$t_desc;?></textarea>
                </div>
              <?php }
              if ($status!=0) { ?>
                <div data-tab="preview" hidden>
                    <div class="content" id="preview_editor">
                      <?php if ($t_vis=='azienda') echo $t_desc;?>
                    </div>
                </div>
                <div data-tab="comments" hidden>

                </div>
              <?php } ?>
            </div>
          </div>
        </div>
</section>

<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
</body>

<script src="<?= BASE_DIR ?>js/toggleTab.js"></script>
<script src="js/resoconto.js"></script>
</html>
