<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 14/02/18
 * Time: 19.18
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());
// Variabili pagina
$page = "Utenze Google";

$filtro = (isset($_GET["filtro"])) ? "%{$_GET["filtro"]}%" : "%";
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
            <form  method="get">
                <div class="level">
                    <div class="level-left">
                        <div class="level-item">
                            <a class="button is-primary" href="import.php">
                                <span class="icon">
                                    <i class="fa fa-user-plus" aria-hidden="true"></i>
                                </span>
                                <span>
                                    Aggiungi utente dal dominio
                                </span>
                            </a>
                        </div>
                        <div class="level-item">
                            <a href="set_orgunit.php" class="button" type="button" title="Imposta">
                                <span class="icon">
                                    <i class="fa fa-gear" aria-hidden="true"></i>
                                </span>
                            </a>
                        </div>
                    </div>
                    <div class="level-right">
                        <div class="level-item">
                            <div class="field has-addons">
                                <p class="control">
                                    <input class="input" name="filtro" type="text" value="<?= sanitize_html($_GET["filtro"]) ?>" placeholder="Filtra persone">
                                </p>
                                <input title="limite" hidden type="number" name="limite" value="<?= sanitize_html($utenze->get_limit()) ?>">
                                <p class="control">
                                    <button type="submit" class="button">
                                        Filtra
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
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
                        <td><?= sanitize_html($nome) ?></td>
                        <td><?= sanitize_html($cognome) ?></td>
                        <td>
                            <a href="mailto:<?= sanitize_html($posta) ?>">
                                <?= sanitize_html($posta) ?>
                            </a>
                        </td>
                        <td><?= ($studente !== null) ? "SÌ" : "NO" ?></td>
                        <td><?= ($docente !== null) ? "SÌ" : "NO" ?></td>
                        <td>
                            <a href="info.php?utente=<?= $id_interno ?>" class="button is-small is-fullwidth is-warning">
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

