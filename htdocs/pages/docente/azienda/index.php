<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 21/04/18
 * Time: 14.59
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();

if(empty($_GET["id"]))
    throw new RuntimeException("Manca l'id!", -1);

$azienda = $server->prepare("
  SELECT A.id, A.nominativo, A.IVA, A.codiceFiscale, C.descrizione, A.dimensione, A.gestione, AT.cod2007, AT.descrizione, NOT A.no_accessi
    FROM Azienda A 
    LEFT JOIN CodiceAteco AT on A.ateco = AT.id
    LEFT JOIN Classificazioni C on A.classificazione = C.id
  WHERE A.id = ?");
$azienda->bind_param("i", $_GET["id"]);

$azienda->execute();
$azienda->bind_result($id, $nome, $iva, $cf, $classificazione, $dimensione, $gestione, $ateco, $ateco_desc, $entrato_sito);

if($azienda->fetch() !== true)
    throw new RuntimeException("Richiesta malformata, l'azienda richiesta non esiste", 404);

// Variabili pagina
$page = "Azienda - " . $nome;

?>

<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = null;
            include "../menu.php";
            ?>
        </aside>
        <div class="column">
            <div class="media">

                <figure class="media-left">
                    <p>
                        <span class="icon is-large" style="width: 96px;">
                            <i class="fa fa-building fa-3x" aria-hidden="true"></i>
                        </span>
                    </p>
                </figure>
                <div class="media-content">
                    <h2 class="title is-2"><?= sanitize_html($nome) ?></h2>
                    <table class="table is-narrow is-fullwidth">
                        <tr>
                            <th>Partita IVA</th>
                            <td><?= sanitize_html($iva) ?></td>
                        </tr>
                        <tr>
                            <th>Codice Fiscale</th>
                            <td><?= sanitize_html($cf) ?></td>
                        </tr>
                        <tr>
                            <th rowspan="2">Codice ateco</th>
                            <td><?= sanitize_html($ateco) ?></td>
                        </tr>
                        <tr>
                            <td><?= sanitize_html($ateco_desc) ?></td>
                        </tr>
                        <tr>
                            <th>Classificazione</th>
                            <td><?= sanitize_html($classificazione) ?></td>
                        </tr>
                        <tr>
                            <th>Dimensione</th>
                            <td><?= sanitize_html($dimensione) ?></td>
                        </tr>
                        <tr>
                            <th>Tipo di gestione</th>
                            <td><?= sanitize_html($gestione) ?></td>
                        </tr>
                        <tr>
                            <th>Accesso</th>
                            <td><?= $entrato_sito ? "Già effettuato!" : "Mai entrato per il momento" ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="section">
                <div class="tabs is-boxed" id="selector">
                    <ul>
                        <li data-tab="indirizzi">
                            <a>
                                <span class="icon">
                                    <i class="fa fa-suitcase" aria-hidden="true"></i>
                                </span>
                                <span>
                                    Indirizzi di studio
                                </span>
                            </a>
                        </li>
                        <li data-tab="sedi">
                            <a>
                                <span class="icon">
                                    <i class="fa fa-map" aria-hidden="true"></i>
                                </span>
                                <span>
                                    Sedi
                                </span>
                            </a>
                        </li>
                        <li data-tab="contatti">
                            <a>
                                <span class="icon">
                                    <i class="fa fa-address-book" aria-hidden="true"></i>
                                </span>
                                <span>
                                    Contatti
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div id="contents">
                    <div data-tab="indirizzi" hidden>
                        <h4 class="title is-4" id="indirizzi">Indirizzi di studio</h4>
                        <div id="indirizzi_dinamici"></div>
                    </div>

                    <div data-tab="sedi" hidden>
                        <h4 class="title is-4" id="sedi">Sedi</h4>
                        <div id="sedi_dinamici"></div>
                    </div>

                    <div data-tab="contatti" hidden>
                        <h4 class="title is-4" id="contatti">Contatti e referenti</h4>
                        <div id="contatti_dinamici"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>

<script src="<?= BASE_DIR ?>js/toggleTab.js"></script>
<script src="<?= BASE_DIR ?>js/DynamicPagination.js"></script>

<script src="js/tabs.js"></script>
<script src="js/indirizzi.js"></script>
</body>
</html>
