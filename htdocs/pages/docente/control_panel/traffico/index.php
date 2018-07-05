<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 28/01/18
 * Time: 17.52
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();
// Variabili pagina
$page = "Accessi";

$server = new \mysqli_wrapper\mysqli();
$indirizzi  = new class(
        $server,
        "SELECT indirizzo_rete, ultimo_accesso, tentativi_falliti, ultimo_tentativo FROM AziendeTentativiAccesso ORDER BY ultimo_tentativo DESC, indirizzo_rete"
) extends helper\Pagination
{
    public  function compute_rows(): int
    {
        $rows = 0;
        $stm = $this->link->prepare("SELECT COUNT(indirizzo_rete) FROM AziendeTentativiAccesso");
        $stm->execute();
        $stm->bind_result($rows);
        $stm->fetch();
        $stm->close();

        return $rows;
    }
};

$indirizzi->execute();
$indirizzi->bind_result(
        $indirizzo_ip,
        $ultimo_accesso,
        $tentativi_falliti,
        $ultimo_tentativo
);

$nav = new helper\PaginationIndexBuilder($indirizzi);
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
                                    <a class="button" href="./traffico_cristo.php?indirizzo=<?= inet_ntop($indirizzo_ip) ?>">
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
                <?php $nav->generate_index($_GET) ?>
            </div>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/footer.phtml"; ?>
</body>
</html>
