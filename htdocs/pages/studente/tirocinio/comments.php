<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 17/03/18
 * Time: 20.02
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();

if(!$user->is_authorized(\auth\LEVEL_GOOGLE_STUDENT, \auth\User::UNAUTHORIZED_RETURN_FALSE))
{
    echo "Non si è autorizzati ad accedere a questa sezione, provare ad uscire e rientrare<br>";
    die("Non autorizzato!");
}

$valid = $server->prepare("SELECT id FROM Tirocinio WHERE id = ? AND studente = ?");
$valid->bind_param(
    "ii",
    $_GET["tirocinio"],
    $user->get_database_id()
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
  FROM Commento CM INNER JOIN UtenteGoogle U ON CM.autore = U.id
  WHERE CM.tirocinio = ? ORDER BY quando DESC") extends \helper\Pagination
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
                    <img src="<?= $comm_foto ?>" alt="">
                </figure>
            </div>
            <div class="media-content">
                <p>
                    <strong>
                        <?= $comm_nome . " " . $comm_cognome ?>
                        -
                        <time datetime="<?= $comm_tstamp ?>"><?= $comm_tstamp ?></time>
                    </strong>
                    <br>
                    <?= filter_var($comm_testo, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?>
                </p>
            </div>
        </article>
    </div>
<?php }
$_GET["page"] = "comments";
$nav->generate_index($_GET);
?>
</div>
