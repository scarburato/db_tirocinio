<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 14/02/18
 * Time: 19.18
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER);
$user = \auth\connect_token_google($google_client, $_SESSION["user"]["token"], "./../../../../", $oauth2);

// Variabili pagina
$page = "Utenze Google";

$filtro = (isset($_GET["filtro"])) ? "%{$_GET["filtro"]}%" : "%";
$server = new \mysqli_wrapper\mysqli();
$utenze = new class($server, "SELECT id, nome, cognome, indirizzo_posta, D.utente, S.utente FROM UtenteGoogle
  LEFT JOIN Docente D ON UtenteGoogle.id = D.utente
  LEFT JOIN Studente S ON UtenteGoogle.id = S.utente
  WHERE nome LIKE ? OR cognome LIKE ? OR indirizzo_posta LIKE ?
  ") extends \helper\Pagination
{
    public function compute_rows()
    {
        $row_tot = 0;
        $filtro = (isset($_GET["filtro"])) ? "%{$_GET["filtro"]}%" : "%";

        $conta = $this->link->prepare(
                "SELECT COUNT(id) AS 'c' FROM UtenteGoogle WHERE nome LIKE ? OR cognome LIKE ? OR indirizzo_posta LIKE ?");

        $conta->bind_param(
                "sss",
                $filtro,
                $filtro,
                $filtro
        );
        $conta->execute(true);
        $conta->bind_result($row_tot);
        $conta->fetch();
        $conta->close();

        return $row_tot;
    }
};

$utenze->set_limit((isset($_GET["limite"]) && $_GET["limite"] > 1) ? $_GET["limite"] : 15);
$utenze->set_current_page((isset($_GET["pagina"]) && $_GET["pagina"] >= 0) ? $_GET["pagina"] : 0);

$utenze->bind_param(
        "sss",
        $filtro,
        $filtro,
        $filtro
);

$utenze->execute(true);

$utenze->bind_result($id_interno, $nome, $cognome, $posta, $docente, $studente);

$nav = new \helper\PaginationIndexBuilder($utenze);
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
            <form method="get">
                <div class="field has-addons is-pulled-right">
                    <p class="control">
                        <input class="input" name="filtro" type="text" value="<?= $_GET["filtro"] ?>" placeholder="Filtra persone">
                    </p>
                    <input title="limite" hidden type="number" name="limite" value="<?= $utenze->get_limit() ?>">
                    <p class="control">
                        <button type="submit" class="button">
                            Filtra
                        </button>
                    </p>
                </div>
            </form>

            <table class="table is-fullwidth">
                <thead>
                <tr>
                    <th title="I nomi sono stati inseriti all'icontrario dalla scuola">Nome</th>
                    <th title="I nomi sono stati inseriti all'icontrario dalla scuola">Cognome</th>
                    <th>Posta Elettronica</th>
                    <th>Studente</th>
                    <th>Docente</th>
                    <th style="width: 8%"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                while ($utenze->fetch())
                {
                    ?>
                    <tr>
                        <td><?= $nome ?></td>
                        <td><?= $cognome ?></td>
                        <td>
                            <a href="mailto:<?= $posta ?>">
                                <?= $posta ?>
                            </a>
                        </td>
                        <td><?= ($studente !== null) ? "SÌ" : "NO" ?></td>
                        <td><?= ($docente !== null) ? "SÌ" : "NO" ?></td>
                        <td>
                            <a class="button is-small is-fullwidth is-warning">
                                <span class="icon">
                                    <i class="fa fa-cog" aria-hidden="true"></i>
                                </span>
                                <span>
                                    Imposta
                                </span>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php $nav->generate_index($_GET); ?>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/footer.phtml"; ?>
</body>
</html>

