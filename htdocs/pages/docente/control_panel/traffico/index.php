<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 28/01/18
 * Time: 17.52
 */

require_once "../../../../utils/lib.hphp";
require_once "../../../../utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER, "./../../../../");
$user = \auth\connect_token_google($google_client, $_SESSION["user"]["token"], "./../../../../", $oauth2);

// Variabili pagina
$page = "Accessi";

$server = new \mysqli_wrapper\mysqli();
$indirizzi  = $server->prepare(
        "SELECT indirizzo_rete, ultimo_accesso, tentativi_falliti, ultimo_tentativo FROM AziendeTentativiAccesso ORDER BY ultimo_tentativo DESC "
);
$indirizzi->execute(true);
$indirizzi->bind_result(
        $indirizzo_ip,
        $ultimo_accesso,
        $tentativi_falliti,
        $ultimo_tentativo
);

?>
<html lang="it">
<head>
    <?php include "../../../../utils/pages/head.phtml"; ?>
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
        <div class="column">
            <div  style="overflow-x: auto">
                <table class="table is-fullwidth">
                    <thead>
                    <tr>
                        <th>Indirizzo di rete</th>
                        <th>Ultimo accesso</th>
                        <th>Ultimo tentativo</th>
                        <th>Tentativi falliti</th>
                        <th>Difficoltà captcha</th>
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
                                <?= compute_hashes($tentativi_falliti) ?>
                            </td>
                            <td>
                                <?php
                                if($tentativi_falliti > 0)
                                {
                                    ?>
                                    <a class="button" href="pages/docente/control_panel/traffico/traffico_cristo.php?indirizzo=<?= inet_ntop($indirizzo_ip) ?>">
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
<?php include "../../../../utils/pages/footer.phtml"; ?>
</body>
</html>
