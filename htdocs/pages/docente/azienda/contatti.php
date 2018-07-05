<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 21/04/18
 * Time: 18.57
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

$pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 0;
$azienda = $_GET["azienda"];

if(empty($azienda))
    throw new RuntimeException("Missing parameter 'azienda'!", -1);

$permessi = new \auth\PermissionManager($server, $user);
$puo_entrare_in_contatto = $permessi->check("factory.intouch");

// Ottengo lista aziende
$contatti = new class($server,
    "SELECT C.id, nome, cognome, qualifica, COUNT(E.inizio)
      FROM Contatto C 
      LEFT JOIN (
        SELECT * FROM EntratoInContatto
        WHERE inizio <= CURRENT_DATE() AND (fine IS NULL OR fine > CURRENT_DATE())
      ) E ON C.id = E.contatto
    WHERE azienda = ?
    GROUP BY C.id"
) extends \helper\Pagination
{
    public function compute_rows(): int
    {
        $row_tot = 0;
        $conta = $this->link->prepare(
            "SELECT COUNT(*) FROM Contatto C WHERE C.azienda = ?");

        $conta->bind_param("i", $_GET["azienda"]);

        $conta->execute();
        $conta->bind_result($row_tot);
        $conta->fetch();
        $conta->close();

        return $row_tot;
    }
};

$contatti->set_limit(5);
$contatti->set_current_page($pagina);

$contatti->bind_param("i", $azienda);

$contatti->execute();
$contatti->bind_result($c_id, $nome, $cognome, $qualifica, $contatti_attuali);

if($contatti->get_rows() < 1)
{
    ?>
    <div>
        <h4 class="title is-4 has-text-centered">
            <span class="icon">
                <i class="fa fa-frown-o" aria-hidden="true"></i>
            </span>
            <span>
                Non c'è nulla da mostrare qua
            </span>
        </h4>
    </div>
    <?php
    exit;
}

$nav = new \helper\PaginationIndexBuilder($contatti);
$nav->set_pagination_builder(new \helper\IndexJS());
?>

<div>
    <table class="table is-narrow is-fullwidth">
        <thead>
        <tr>
            <th>Nome Cognome</th>
            <th>Qualifica</th>
            <th style="width: 20%">Contatti in corso</th>
            <th style="width: 1.6em"></th>
            <?php if($puo_entrare_in_contatto) {
               ?>
                <th style="width: 1.6em"></th>
               <?php
            }?>
        </tr>
        </thead>
        <tbody>
        <?php
        while($contatti->fetch())
        {
            ?>
            <tr>
                <td><?= sanitize_html("{$nome} {$cognome}") ?></td>
                <td><?= sanitize_html($qualifica) ?></td>
                <td><?= $contatti_attuali ?></td>
                <td>
                    <a class="button is-small is-link"
                       aria-label="Ulteriori informazioni"
                       title="Ulteriori informazioni"
                       href="contatto/?id=<?= $c_id ?>">
                        <span class="icon">
                            <i class="fa fa-info" aria-hidden="true"></i>
                        </span>
                    </a>
                </td>
                <?php
                if($puo_entrare_in_contatto)
                {
                    ?>
                    <td>
                        <a class="button is-link is-small"
                           aria-label="Segnati in contatto"
                           title="Segnati in contatto"
                           href="<?= BASE_DIR ?>pages/docente/aziende/contatti/create.php?contact=<?=$c_id?>">
                            <span class="icon">
                                <i class="fa fa-handshake-o" aria-hidden="true"></i>
                            </span>
                        </a>
                    </td>
                    <?php
                }?>
            </tr>
            <?php
        }
        $contatti->close();
        ?>
        </tbody>
    </table>
    <?php
    $nav->generate_index();
    ?>
</div>
