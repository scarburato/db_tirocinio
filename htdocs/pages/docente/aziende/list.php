<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 17/04/18
 * Time: 17.45
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 0;

// Ottengo lista aziende
$aziende = new class($server,
    "
SELECT A.id, A.nominativo,
  (SELECT COUNT(S.id) FROM Sede S WHERE S.azienda = A.id),
  (SELECT COUNT(C.id) FROM Contatto C WHERE C.azienda = A.id),
  (SELECT COUNT(T.id) FROM Tirocinio T WHERE
    (CURRENT_DATE() >= T.dataInizio AND (T.dataTermine IS NULL OR CURRENT_DATE() <= T.dataTermine))
    AND T.azienda = A.id)
FROM Azienda A"
) extends \helper\Pagination
{
    public function compute_rows()
    {
        $row_tot = 0;
        $conta = $this->link->prepare(
            "SELECT COUNT(id) FROM Azienda");

        $conta->execute();
        $conta->bind_result($row_tot);
        $conta->fetch();
        $conta->close();

        return $row_tot;
    }
};

$aziende->set_limit(4);
$aziende->set_current_page($pagina);

$aziende->execute();
$aziende->bind_result($id, $nominativo, $sedi_conta, $contatti_conta, $tirocini_conta);

$nav = new \helper\PaginationIndexBuilder($aziende);
$nav->set_pagination_builder(new \helper\IndexJS());
?>
<div>
    <?php
    $nav->generate_index($_GET);
    ?>
    <br>
    <?php while ($aziende->fetch())
    {
        ?>
        <div class="box ajax_comment" data-current-page="<?= $aziende->get_current_page() ?>">
            <h3 class="title is-3"><?= sanitize_html($nominativo) ?></h3>
            <table class="table is-fullwidth is-narrow">
                <tr>
                    <th><?= $contatti_conta ?></th>
                    <td>Referenti e contatti aziendiali registrati</td>
                </tr>
                <tr>
                    <th><?= $sedi_conta ?></th>
                    <td>Sedi registrate</td>
                </tr>
                <tr>
                    <th><?= $tirocini_conta ?></th>
                    <td>Studenti attualmente coinvolti in attività</td>
                </tr>
            </table>
            <p class="has-text-right">
                <a class="button is-link" href="../azienda/?id=<?= $id ?>">
                    <span class="icon">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                    </span>
                    <span>Mostra scheda</span>
                </a>
            </p>
        </div>
        <?php
    }?>

    <?php
    $nav->generate_index($_GET);
    ?>
</div>