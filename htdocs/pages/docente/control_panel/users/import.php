<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 24/02/18
 * Time: 11.45
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());
// Variabili pagina
$page = "Importa utente da Google";

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
            <div class="field">
                <label class="label">
                    Aggiungi da dominio
                </label>
            </div>
            <div class="field has-addons">
                <div class="control is-expanded">
                    <input class="input" type="email" placeholder="Indirizzo di posta elettronica">
                </div>
                <div class="control">
                    <a class="button is-link">
                        <span class="icon">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </span>
                        <span>
                            Cerca
                        </span>
                    </a>
                </div>
            </div>

            <div id="output" class="box">
                <div class="media">
                    <figure class="image is-96x96 media-left">
                        <img src="https://plus.google.com/_/focus/photos/public/AIbEiAIAAABDCOzvp6qfv9q6CCILdmNhcmRfcGhvdG8qKDc4ODZkNzUzN2U2ZDM1NWRhODRkNDdhMDM4ODQ5ZWFkNjdhZTZlNzgwAZU1sk7AdSwrMfBtPsgA445O-F2R">
                    </figure>
                    <div class="media-right">
                        <h1 class="title is-2">Mario Rossi</h1>
                        <h3 class="subtitle is-3">
                            <a href="mailto:dario.pagani@itispisa.gov.it">
                                dario.pagani@itispisa.gov.it
                            </a>
                        </h3>
                        <p>
                            Unità organizzativa <code>/STUDENTI/5INF-2017</code>
                        </p>
                        <p><strong>Livelli</strong></p>
                        <ul>
                            <li>Docente</li>
                        </ul>
                    </div>
                </div>
                <br>
                <button class="button is-large is-primary is-fullwidth">
                    <span class="icon">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </span>
                    <span>
                        Aggiungi
                    </span>
                </button>
            </div>
        </div>
    </div>
</section>
</body>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
</html>