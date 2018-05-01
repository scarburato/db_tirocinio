<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 17/03/18
 * Time: 17.46
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

$permissions = new \auth\PermissionManager($server, $user);

$permissions->check("factory.intouch", \auth\PermissionManager::UNAUTHORIZED_REDIRECT);

// Variabili pagina
$page = "Contatti";

$contatti = new class($server,
    "SELECT A.id, A.nominativo, COUNT(*)
      FROM EntratoInContatto
      INNER JOIN Contatto C on EntratoInContatto.contatto = C.id
      INNER JOIN Azienda A on C.azienda = A.id
    GROUP BY A.id
  ") extends \helper\Pagination
{
    public function compute_rows()
    {
        $row_tot = 0;

        $conta = $this->link->prepare(
            "SELECT COUNT(*)
                    FROM Azienda A
                   WHERE EXISTS(
                      SELECT contatto
                        FROM EntratoInContatto
                        INNER JOIN Contatto C on EntratoInContatto.contatto = C.id
                      WHERE C.azienda = A.id)");

        $conta->execute();
        $conta->bind_result($row_tot);
        $conta->fetch();
        $conta->close();

        return $row_tot;
    }
};
$nav = new \helper\PaginationIndexBuilder($contatti);

$contatti->execute();
$contatti->bind_result($id,$nome, $numero_contatti);
?>
<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 7;
            include "../menu.php";
            ?>
        </aside>
        <div class="column">
            <div class="field">
                <p class="control has-text-right">
                    <a class="button is-primary is-large" href="contatti/create.php">
                        <span class="icon">
                            <i class="fa fa-handshake-o" aria-hidden="true"></i>
                        </span>
                        <span>
                            Segnarsi come in contatto
                        </span>
                    </a>
                </p>
            </div>
            <?php
            while($contatti->fetch())
            {
                ?>
                <article class="box">
                    <h3 class="title is-3"><?= sanitize_html($nome) ?></h3>
                    <p>
                        <span class="icon"><i class="fa fa-handshake-o" aria-hidden="true"></i></span>
                        <span><?= $numero_contatti ?> docenti in contatto con altrettanti referenti aziendali</span>
                    </p>
                    <div class="field">
                        <p class="control has-text-right">
                            <a class="button is-info" href="contatti/?azienda=<?= $id ?>">
                                <span class="icon">
                                    <i class="fa fa-list" aria-hidden="true"></i>
                                </span>
                                <span>
                                    Guarda
                                </span>
                            </a>
                        </p>
                    </div>
                </article>
                <?php
            }
            $contatti->close();
            ?>
            <?php $nav->generate_index($_GET); ?>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
</body>
</html>
