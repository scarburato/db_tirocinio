<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 21/04/18
 * Time: 18.08
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
$indirizzi = new class($server,
    "SELECT I2.indirizzo, I.motivazioni 
      FROM IndirizziAzienda I 
      LEFT JOIN Indirizzo I2 on I.indirizzo = I2.id
    WHERE azienda = ?"
) extends \helper\Pagination
{
    public function compute_rows(): int
    {
        $row_tot = 0;
        $conta = $this->link->prepare(
            "SELECT COUNT(*) FROM IndirizziAzienda WHERE azienda = ?");

        $conta->bind_param("i", $_GET["azienda"]);

        $conta->execute();
        $conta->bind_result($row_tot);
        $conta->fetch();
        $conta->close();

        return $row_tot;
    }
};

$indirizzi->set_limit(5);
$indirizzi->set_current_page($pagina);

$indirizzi->bind_param("i", $azienda);

$indirizzi->execute();
$indirizzi->bind_result($indirizzo, $motivazioni);

if($indirizzi->get_rows() < 1)
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

$nav = new \helper\PaginationIndexBuilder($indirizzi);
$nav->set_pagination_builder(new \helper\IndexJS());
?>

<div>
    <table class="table is-narrow is-fullwidth">
        <thead>
        <tr>
            <th>Indirizzo</th>
            <th style="width: 60%">Motivazioni Fornite</th>
        </tr>
        </thead>
        <tbody>
        <?php
        while($indirizzi->fetch())
        {
            ?>
            <tr>
                <th><?= sanitize_html($indirizzo) ?></th>
                <td><?= sanitize_html($motivazioni) ?></td>
            </tr>
            <?php
        }
        $indirizzi->close();
        ?>
        </tbody>
    </table>
    <?php
    $nav->generate_index();
    ?>
</div>
