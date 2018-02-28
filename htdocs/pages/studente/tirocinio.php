<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 22/01/18
 * Time: 19.48
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";

(new \auth\User())->is_authorized(\auth\User::UNAUTHORIZED_THROW);

$tempo = (isset($_GET['chTrain']) ? $_GET['chTrain'] : 1);
$index = (isset($_GET["index"]) && $_GET["index"] >= 0 ? $_GET["index"] : 0);
$server = new \mysqli_wrapper\mysqli();


switch ($tempo) {
  case 0: // Tirocini passati
  $train = $server->prepare(
          "SELECT Tirocinio.id, A.nominativo, dataInizio, dataTermine, visibilita FROM Tirocinio
              LEFT JOIN Azienda A ON Tirocinio.azienda = A.id
              LEFT JOIN Docente D ON Tirocinio.docenteTutore = D.utente
              LEFT JOIN Contatto C ON Tirocinio.tutoreAziendale = C.id
              WHERE studente = ?
                AND (dataTermine<CURRENT_DATE() AND dataTermine IS NOT NULL)
              ORDER BY dataInizio ASC
              LIMIT 1 OFFSET ?");
    break;
  case 1: // Presenti
  default:
  $train = $server->prepare(
          "SELECT Tirocinio.id, A.nominativo, dataInizio, dataTermine, visibilita FROM Tirocinio
              LEFT JOIN Azienda A ON Tirocinio.azienda = A.id
              LEFT JOIN Docente D ON Tirocinio.docenteTutore = D.utente
              LEFT JOIN Contatto C ON Tirocinio.tutoreAziendale = C.id
              WHERE studente = ?
                AND (CURRENT_DATE()>=dataInizio AND (dataTermine IS NULL OR CURRENT_DATE()<=dataTermine))
              ORDER BY dataInizio ASC
              LIMIT 1 OFFSET ?");
    break;
  case 2: // Futuri
  $train = $server->prepare(
          "SELECT Tirocinio.id, A.nominativo, dataInizio, dataTermine, visibilita FROM Tirocinio
              LEFT JOIN Azienda A ON Tirocinio.azienda = A.id
              LEFT JOIN Docente D ON Tirocinio.docenteTutore = D.utente
              LEFT JOIN Contatto C ON Tirocinio.tutoreAziendale = C.id
              WHERE studente = ? AND CURRENT_DATE()<dataInizio
              ORDER BY dataInizio ASC
              LIMIT 1 OFFSET ?");
    break;
}

$train->bind_param(
        "ii",
        $_SESSION["user"]["id"],
        $index
);

$train->execute(false);
$train->bind_result($db_id, $business_name, $data_inizio, $data_termine, $visibilita);

if(!$train->fetch())
    return;
?>

<article class="tirocinio" id="tirocinio_<?= $index ?>" data-nextid="<?= $index + 1 ?>">
  <div class='card'>
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
    <?php if ($tempo!=2) { ?>
      <a href="tirocinio_resoconto.php?tirocinio=<?=$db_id?>&page=resoconto" class="card-footer-item">
        <span class="icon">
          <i class="fa fa-pencil-square" aria-hidden="true"></i>
        </span>
        <?php if ($visibilita=='azienda') echo 'Visualizza Resoconto'; else echo 'Scrivi Resoconto';?>
      </a>
    <?php ;} ?>
      <a href="tirocinio_resoconto.php?tirocinio=<?=$db_id?>&page=info" class="card-footer-item">
        <span class="icon">
          <i class="fa fa-pencil-square" aria-hidden="true"></i>
        </span>
        Info
      </a>
    </footer>
  </div>
</article>
<br>
