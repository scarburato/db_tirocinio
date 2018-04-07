<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 06/04/18
 * Time: 18.15
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

$permit = new \auth\PermissionManager($server, $user);
$have_permitt = $permit->check("train.readall");

$valid = $server->prepare("SELECT id FROM Tirocinio WHERE id = ? AND (docenteTutore = ? OR ?)");
$valid->bind_param(
    "iii",
    $_GET["tirocinio"],
    $user->get_database_id(),
    $have_permitt
);

$valid->execute();
if(!$valid->fetch())
{
    echo "Non si può accedere ai contenuti di questo tirocinio! Non ti appartiene!<br>";
    die("invalido id");
}

$valid->close();

$commenti = new class($server,
    "SELECT CM.id, U.id, U.nome, U.cognome, U.fotografia, testo, quando
  FROM Commento CM 
  INNER JOIN UtenteGoogle U ON CM.autore = U.id
  WHERE CM.tirocinio = ? ORDER BY quando DESC, CM.id") extends \helper\Pagination
{
    public function compute_rows()
    {
        $row_tot = 0;
        $conta = $this->link->prepare(
            "SELECT COUNT(id) FROM Commento WHERE tirocinio=?");
        $conta->bind_param('i', $_GET['tirocinio']);
        $conta->execute(false);
        $conta->bind_result($row_tot);
        $conta->fetch();
        $conta->close();

        return $row_tot;
    }
};
$commenti->set_limit(isset($_GET['limite']) ? $_GET['limite'] : 5);
$commenti->set_current_page(isset($_GET['pagina']) ? $_GET['pagina'] : 0);

$commenti->bind_param('i', $_GET["tirocinio"]);
$commenti->execute(false);
$commenti->bind_result($comm_id, $autore, $comm_nome, $comm_cognome, $comm_foto, $comm_testo, $comm_tstamp);

$nav = new \helper\PaginationIndexBuilder($commenti);
$nav->set_pagination_builder(new \helper\IndexJS());
?>
<div class="ajax_comment" data-current-page="<?= $commenti->get_current_page() ?>">
    <?php while ($commenti->fetch()) { ?>
        <div class="box">
            <article class="media">
                <div class="media-left">
                    <figure class="image is-96x96">
                        <img src="<?= sanitize_html($comm_foto) ?>" alt="">
                    </figure>
                </div>
                <div class="media-content">
                    <p>
                        <strong>
                            <?= sanitize_html($comm_nome) . " " . sanitize_html($comm_cognome) ?>
                            -
                            <time datetime="<?= $comm_tstamp ?>"><?= $comm_tstamp ?></time>
                        </strong>
                        <br>
                        <?= sanitize_html($comm_testo) ?>
                    </p>
                </div>
                <?php
                if($permit->check("train.comments.delete"))
                {
                    ?>
                    <div class="media-right">
                        <button class="button is-danger is-small delete-comment" title="Elimina il commento">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    </div>
                    <?php
                }
                ?>
            </article>
        </div>
    <?php }
    $_GET["page"] = "comments";
    $nav->generate_index($_GET);
    ?>
</div>
