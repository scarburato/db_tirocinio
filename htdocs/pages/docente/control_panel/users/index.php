<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 14/02/18
 * Time: 19.18
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER, "./../../../../");
$user = \auth\connect_token_google($google_client, $_SESSION["user"]["token"], "./../../../../", $oauth2);

// Variabili pagina
$page = "Utenze Google";
$page_n = (isset($_GET["pagina"]) && $_GET["pagina"] >= 0) ? $_GET["pagina"] : 0;
$limit = 50;

$server = new \mysqli_wrapper\mysqli();

// Numero di righe
$conta = $server->prepare("SELECT COUNT(id) FROM UtenteGoogle");
$conta->execute(true);
$conta->bind_result($row_tot);
$conta->fetch();
$conta->close();

$page_tot = (int)($row_tot / $limit);
if($page_n > $page_tot)
    $page_n = $page_tot;

$offset = $page_n * $limit;

$utenze = $server->prepare("SELECT id, SUB_GOOGLE, nome, cognome, indirizzo_posta, D.utente, S.utente FROM UtenteGoogle
  LEFT JOIN Docente D ON UtenteGoogle.id = D.utente
  LEFT JOIN Studente S ON UtenteGoogle.id = S.utente
  LIMIT ? OFFSET ?");

$utenze->bind_param(
        "ii",
        $limit,
        $offset
);

$utenze->execute(true);
$utenze->bind_result($id_interno, $id_google, $nome, $cognome, $posta, $docente, $studente);
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
            <table class="table is-fullwidth">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Google</th>
                    <th title="I nomi sono stati inseriti all'icontrario dalla scuola">Nome</th>
                    <th title="I nomi sono stati inseriti all'icontrario dalla scuola">Cognome</th>
                    <th>Posta Elettronica</th>
                    <th>Studente</th>
                    <th>Docente</th>
                </tr>
                </thead>
                <tbody>
                <?php
                while ($utenze->fetch())
                {
                    ?>
                    <tr>
                        <th><?= $id_interno ?></th>
                        <td><?= $id_google ?></td>
                        <td><?= $nome ?></td>
                        <td><?= $cognome ?></td>
                        <td>
                            <a href="mailto:<?= $posta ?>">
                                <?= $posta ?>
                            </a>
                        </td>
                        <td><?= ($studente !== null) ? "SÌ" : "NO" ?></td>
                        <td><?= ($docente !== null) ? "SÌ" : "NO" ?></td>

                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>



            <nav class="pagination" role="navigation" aria-label="pagination">
                <a href="?pagina=<?= $page_n - 1 ?>" class="pagination-previous" <?= $page_n == 0 ? "disabled" : ""?>>Indietro</a>
                <a href="?pagina=<?= $page_n + 1 ?>" class="pagination-next" <?= $page_n >= $page_tot ? "disabled" : ""?>>Avanti</a>


                <ul class="pagination-list">
                    <li><a href="?pagina=0" class="pagination-link" aria-label="Pagina 0">0</a></li>
                    <li><span class="pagination-ellipsis">&hellip;</span></li>


                    <?php
                    if($page_n > 0)
                    {
                        ?>
                        <li><a href="?pagina=<?= $page_n - 1 ?>" class="pagination-link"
                               aria-label="Pagina <?= $page_n - 1 ?>"><?= $page_n - 1 ?></a></li>
                        <?php
                    }
                    ?>
                    <li><a href="?pagina=<?= $page_n ?>" class="pagination-link is-current" aria-label="Pagina <?= $page_n ?>" aria-current="page"><?= $page_n ?></a></li>
                    <?php
                    if($page_n < $page_tot)
                    {
                        ?>
                        <li><a href="?pagina=<?= $page_n + 1 ?>" class="pagination-link"
                               aria-label="Pagina <?= $page_n + 1 ?>"><?= $page_n + 1 ?></a></li>
                        <?php
                    }
                    ?>

                    <li><span class="pagination-ellipsis">&hellip;</span></li>
                    <li><a href="?pagina=<?= $page_tot ?>" class="pagination-link" aria-label="Pagina <?= $page_tot ?>"><?= $page_tot ?></a></li>
                </ul>
            </nav>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/footer.phtml"; ?>
</body>
</html>

