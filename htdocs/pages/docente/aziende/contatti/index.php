<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 05/04/18
 * Time: 10.25
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();

$azienda = $server->prepare("SELECT nominativo FROM Azienda WHERE id = ?");
$azienda->bind_param(
    "i",
    $_GET["azienda"]
);
$azienda->bind_result($nome);

$azienda->execute();
if(!($azienda->fetch()))
    redirect("../../");

$azienda->close();

// Variabili pagina
$page = "In contatto con " . sanitize_html($nome);


?>
<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 7;
            include "../../menu.php";
            ?>
        </aside>
        <div class="column">
            <h2 class="title is-2"><?= sanitize_html($nome) ?></h2>
            <table class="table is-fullwidth">
                <thead>
                <tr>
					<th style="width: 1.59rem"></th>
                    <th>Inzio</th>
                    <th>Fine</th>
                    <th>Contatto Aziendale</th>
                    <th>Contatto Scolastico</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                    $contatti = $server->prepare("
                      SELECT E.inizio, E.fine, C.id, C.nome, C.cognome, D.nome, D.cognome, E.fine <= CURRENT_DATE(), E.inizio >= CURRENT_DATE()
                        FROM EntratoInContatto E
                        INNER JOIN Contatto C on E.contatto = C.id
                        INNER JOIN UtenteGoogle D on E.docente = D.id
                      WHERE C.azienda = ?
                      ORDER BY E.inizio DESC, E.fine IS NULL DESC");

                    $contatti->bind_param(
                            "i",
                            $_GET["azienda"]
                    );

                    $contatti->execute();
                    $contatti->bind_result($inzio, $fine, $az_id, $az_nome, $az_cogn, $doc_nome, $doc_cognome, $finito, $futuro);

                    while($contatti->fetch())
                    {
                        ?>
                        <tr>
							<td><span class="icon"><?php
									if($finito)
									{
										?><i class="fa fa-stop" aria-hidden="true"></i><?php
									}
									elseif ($futuro)
									{
										?><i class="fa fa-fast-forward" aria-hidden="true"></i><?php
									}
									else
									{
										?><i class="fa fa-play" aria-hidden="true"></i><?php
									}
									?></span></td>
                            <td><?= $inzio ?></td>
                            <td><?= $fine === null ? "In corso" : $fine ?></td>
                            <td>
                                <a target="_blank" href="../../azienda/contatto/?id=<?= $az_id ?>">
                                    <?= sanitize_html($az_nome . " " . $az_cogn) ?>
                                </a>
                            </td>
                            <td><?= sanitize_html($doc_nome . " ". $doc_cognome) ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>

</body>
</html>