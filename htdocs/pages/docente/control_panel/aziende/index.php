<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 26/01/18
 * Time: 9.04
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER);
$user = \auth\connect_token_google($google_client, $_SESSION["user"]["token"], $oauth2);

// Variabili pagina
$page = "Gestione Aziende";

$server = new \mysqli_wrapper\mysqli();
$aziende = new class($server, "SELECT nominativo, codiceFiscale, IVA, id FROM Azienda") extends \helper\Pagination
{
    public function compute_rows()
    {
        $rows = 0;
        $conta = $this->link->prepare(
            "SELECT COUNT(id) AS 'c' FROM Azienda");


        $conta->execute(true);
        $conta->bind_result($row_tot);
        $conta->fetch();
        $conta->close();

        return $row_tot;
    }
};

$aziende->execute();
$aziende->bind_result($nome, $cf, $iva, $id);
?>
<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) .  "/utils/pages/head.phtml"; ?>
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
            <div>
                <p>
                    <a class="button is-primary is-pulled-right is-large" href="./aggiungi.php">
                        <span class="icon">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </span>
                        <span>
                            Aggiungi
                        </span>
                    </a>
                </p>
                <table class="table is-fullwidth" style="overflow-x: auto">
                    <thead>
                    <tr>
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
                    <?php
                    while ($aziende->fetch())
                    {
                        ?>
                        <tr>
                            <td><?= $nome ?></td>
                            <td>
                                <samp><?= $cf ?></samp>
                            </td>
                            <td>
                                <samp><?= $iva ?></samp>
                            </td>
                            <td>
                                <a class="button is-warning is-small is-fullwidth" href="./info.php?id=<?= $id ?>">
                                    <span class="icon">
                                        <i class="fa fa-info"></i>
                                    </span>
                                    <span>
                                        Altre informazioni
                                    </span>
                                </a>
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
<?php include ($_SERVER["DOCUMENT_ROOT"]) .  "/utils/pages/footer.phtml"; ?>
</body>
</html>