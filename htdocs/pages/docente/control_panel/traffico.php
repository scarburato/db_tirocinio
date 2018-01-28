<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 28/01/18
 * Time: 17.52
 */

require_once "../../../utils/lib.hphp";
require_once "../../../utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_STUDENT, "./../../../");
$user = \auth\connect_token_google($google_client, $_SESSION["user"]["token"], "./../../../", $oauth2);

// Variabili pagina
$page = "Accessi";

$server = new mysqli(DBMS_SERVER, DBMS_USER, DBMS_PASS, DBMS_DB_NAME);
$indirizzi  = $server->prepare(
        "SELECT indirizzo_rete, ultimo_accesso, tentativi_falliti, ultimo_tentativo FROM AziendeTentativiAccesso;"
);
$indirizzi->execute();
$indirizzi->bind_result(
        $indirizzo_ip,
        $ultimo_accesso,
        $tentativi_falliti,
        $ultimo_tentativo
);

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
        </aside>
        <div class="column">
            <div  style="overflow-x: auto">
                <table class="table is-fullwidth">
                    <thead>
                    <tr>
                        <th>Indirizzo di rete</th>
                        <th>Ultimo accesso</th>
                        <th>Ultimo tentativo</th>
                        <th>Tentativi falliti</th>
                        <th>Hash richieste</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    while($indirizzi->fetch())
                    {
                        ?>
                        <tr>
                            <th>
                                <?= inet_ntop($indirizzo_ip) ?>
                            </th>
                            <td>
                                <?= ($ultimo_accesso === NULL) ? "MAI" : $ultimo_accesso ?>
                            </td>
                            <td>
                                <?= ($ultimo_tentativo === NULL) ? "MAI" : $ultimo_tentativo ?>
                            </td>
                            <td>
                                <?= $tentativi_falliti ?>
                            </td>
                            <td>
                                <?= (($tentativi_falliti%3) + 1)*1024 ?>
                            </td>
                            <td>
                                <?php
                                if($tentativi_falliti > 0)
                                {
                                    ?>
                                    <a class="button">
                                        <span>Perdona</span>
                                        <span class="icon">
                                            <i class="fa fa-handshake-o" aria-hidden="true"></i>
                                        </span>
                                    </a>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
</body>
</html>
