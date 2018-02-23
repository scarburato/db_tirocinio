<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 22/01/18
 * Time: 19.48
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_STUDENT);

$index = (isset($_GET["index"]) && $_GET["index"] >= 0 ? $_GET["index"] : 0);
$server = new \mysqli_wrapper\mysqli();
$train = $server->prepare(
        "SELECT Tirocinio.id, A.nominativo, dataInizio, dataTermine FROM Tirocinio
                LEFT JOIN Azienda A ON Tirocinio.azienda = A.id
                LEFT JOIN Docente D ON Tirocinio.docenteTutore = D.utente
                LEFT JOIN Contatto C ON Tirocinio.tutoreAziendale = C.id
               WHERE studente = ? LIMIT 1 OFFSET ?");

$train->bind_param(
        "ii",
        $_SESSION["user"]["id"],
        $index
);

$train->execute(false);
$train->bind_result($db_id, $business_name, $data_inizio, $data_termine);

if(!$train->fetch())
    return;
?>

<article class="card tirocinio" id="tirocinio_<?= $index ?>" data-nextid="<?= $index + 1 ?>">
    <header class="card-header">
        <h1 class="card-header-title">
            Tirocinio a <?= $business_name ?>
        </h1>
    </header>
    <div class="card-content">
        <div class="content">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus nec iaculis mauris.
            <a href="#">@bulmaio</a>. <a href="#">#css</a> <a href="#">#responsive</a>
            <br>
            <?php if($data_termine === null)
            {
                ?>
                <time datetime="<?= $data_inizio ?>">Dal <?= $data_inizio ?>. Data termine non pervenuta</time>
                <?php
            }
            else
            {
                ?>
                <time datetime="<?= $data_inizio ?>/<?= $data_termine ?>">Dal <?= $data_inizio ?> al <?= $data_termine ?></time>
                <?php
            }
            ?>
        </div>
    </div>
    <footer class="card-footer">
        <a href="tirocinio_resoconto.php?tirocinio=<?=$db_id?>" class="card-footer-item">
            <span class="icon">
                <i class="fa fa-pencil-square" aria-hidden="true"></i>
            </span>
            <span>
                Scrivi un resoconto
            </span>
        </a>
    </footer>
</article>
<br>