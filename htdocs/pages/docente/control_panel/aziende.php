<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 26/01/18
 * Time: 9.04
 */

require_once "../../../utils/lib.hphp";
require_once "../../../utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_STUDENT, "./../../../");
$user = \auth\connect_token_google($google_client, $_SESSION["user"]["token"], $oauth2);

// Variabili pagina
$page = "Gestione Aziende";
?>
<html lang="it">
<head>
    <?php include "../../../utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 8;
            include "../menu.php";
            ?>
        </aside
        <div class="column">
            <div class="container" style="overflow-x: scroll">
                <p>
                    <a class="button is-primary is-pulled-right is-large">
                        <span class="icon">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </span>
                        <span>
                            Aggiungi
                        </span>
                    </a>
                </p>
                <table class="table is-fullwidth">
                    <thead>
                    <tr>
                        <th style="width: 10%;">
                            ID
                        </th>
                        <th>
                            Nome
                        </th>
                        <th>
                            Codice Fiscale
                        </th>
                        <th>
                            Partita IVA
                        </th>
                        <th style="width: 25%;">

                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th>
                            0
                        </th>
                        <td>
                            Mario et mario
                        </td>
                        <td>
                            wdew
                        </td>
                        <td>
                            efew
                        </td>
                        <td>
                            <a class="button is-warning is-fullwidth">
                                Altro...
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
</body>
</html>