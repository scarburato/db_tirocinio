<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 21/04/18
 * Time: 18.46
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

// Ottengo lista aziende
$sedi = new class($server,
    "SELECT S.nomeSede, S.indirizzo, S.CAP, S.comune, S.numCivico, S.provincia, S.stato
      FROM Sede S 
    WHERE S.azienda = ?"
) extends \helper\Pagination
{
    public function compute_rows()
    {
        $row_tot = 0;
        $conta = $this->link->prepare(
            "SELECT COUNT(*) FROM Sede S WHERE S.azienda = ?");

        $conta->bind_param("i", $_GET["azienda"]);

        $conta->execute();
        $conta->bind_result($row_tot);
        $conta->fetch();
        $conta->close();

        return $row_tot;
    }
};

$sedi->set_limit(5);
$sedi->set_current_page($pagina);

$sedi->bind_param("i", $azienda);

$sedi->execute();
$sedi->bind_result($sede, $indirizzo, $cap, $comune, $civico, $provincia, $stato);

if($sedi->get_rows() < 1)
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

$nav = new \helper\PaginationIndexBuilder($sedi);
$nav->set_pagination_builder(new \helper\IndexJS());
?>

<div>
    <table class="table is-narrow is-fullwidth">
        <thead>
        <tr>
            <th>Sede</th>
            <th style="width: 40%">Indirizzo</th>
            <th style="width: 20%">Stato</th>
        </tr>
        </thead>
        <tbody>
        <?php
        while($sedi->fetch())
        {
            ?>
            <tr>
                <th><?= sanitize_html($sede) ?></th>
                <td><?= sanitize_html("{$indirizzo} {$civico}, {$comune}, {$provincia} [{$cap}]") ?></td>
                <td><?= sanitize_html($stato) ?></td>
            </tr>
            <?php
        }
        $sedi->close();
        ?>
        </tbody>
    </table>
    <?php
    $nav->generate_index();
    ?>
</div>
