<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 15/03/18
 * Time: 17.29
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);


$tempo = (isset($_GET['chTrain']) ? $_GET['chTrain'] : 1);                      // Se ho ottenuto il tempo lo uso, altrimenti imposto 1

$index = (isset($_GET["index"]) && $_GET["index"] >= 0 ? $_GET["index"] : 0);   // Se ho ricevuto un indice lo uso, altrimenti pagina 0

$docente_all = (isset($_GET["docente"]) && $_GET["docente"] === "all");         // Se non ho ricevuto uno specifico docente metto a TRUE la codizione di fuga

$docente_id = $user->get_database_id();                                         // Se ho ricevuto un id da filtrare lo metto
if(isset($_GET["docente"]) && is_numeric($_GET["docente"]))                     // Uso l'id della sessione come riserva se
	$docente_id = $_GET["docente"];                                             // è stato passato un id di formato non valido

$studenti_all = (isset($_GET["studente"]) && $_GET["studente"] === "all");
$studente_filter = -1;                                                          // Uso un id non valido :P
if(isset($_GET["studente"]) && is_numeric($_GET["studente"]))
	$studente_filter = $_GET["studente"];

$server = new \mysqli_wrapper\mysqli();
$permission_manager = new \auth\PermissionManager($server, $user);

if($docente_id !== $user->get_database_id())
	$permission_manager->check("train.readall", \auth\PermissionManager::UNAUTHORIZED_THROW);

switch ($tempo)
{
	case 0: // Tirocini passati
		$train = $server->prepare(
			"SELECT T.id, A.id, A.nominativo, dataInizio, dataTermine, visibilita, S.indirizzo_posta, D.indirizzo_posta, C.id, CONCAT(C.nome, ' ', C.cognome) 
			FROM Tirocinio T
			  LEFT JOIN Azienda A ON T.azienda = A.id
			  LEFT JOIN UtenteGoogle S ON T.studente = S.id
			  LEFT JOIN UtenteGoogle D ON T.docenteTutore = D.id
			  LEFT JOIN Contatto C ON T.tutoreAziendale = C.id
			  WHERE (? OR docenteTutore = ?)
				AND (? OR T.studente = ?)
				AND (dataTermine<CURRENT_DATE() AND dataTermine IS NOT NULL)
			  ORDER BY dataInizio ASC, T.id
			  LIMIT 1 OFFSET ?");
		break;
	case 1: // Presenti
	default:
		$train = $server->prepare(
			"SELECT T.id, A.id, A.nominativo, dataInizio, dataTermine, visibilita, S.indirizzo_posta, D.indirizzo_posta, C.id, CONCAT(C.nome, ' ', C.cognome) 
			  FROM Tirocinio T
			  LEFT JOIN Azienda A ON T.azienda = A.id
			  LEFT JOIN UtenteGoogle S ON T.studente = S.id
			  LEFT JOIN UtenteGoogle D ON T.docenteTutore = D.id
			  LEFT JOIN Contatto C ON T.tutoreAziendale = C.id
			  WHERE (? OR docenteTutore = ?)
				AND (? OR T.studente = ?)
				AND (CURRENT_DATE()>=dataInizio AND (dataTermine IS NULL OR CURRENT_DATE()<=dataTermine))
			  ORDER BY dataInizio ASC, T.id
			  LIMIT 1 OFFSET ?");
		break;
	case 2: // Futuri
		$train = $server->prepare(
			"
		SELECT T.id, A.id, A.nominativo, dataInizio, dataTermine, visibilita, S.indirizzo_posta, D.indirizzo_posta, C.id, CONCAT(C.nome, ' ', C.cognome)
			  FROM Tirocinio T
			  LEFT JOIN Azienda A ON T.azienda = A.id
			  LEFT JOIN UtenteGoogle S ON T.studente = S.id
			  LEFT JOIN UtenteGoogle D ON T.docenteTutore = D.id
			  LEFT JOIN Contatto C ON T.tutoreAziendale = C.id
		WHERE (? OR docenteTutore = ?)
		AND (? OR T.studente = ?)
		AND CURRENT_DATE()<dataInizio
		ORDER BY dataInizio ASC, T.id
		LIMIT 1 OFFSET ?");
		break;
}

$train->bind_param(
	"iiiii",
	$docente_all,
	$docente_id,
	$studenti_all,
	$studente_filter,
	$index
);

$train->execute();
$train->bind_result($db_id, $business_id ,$business_name, $data_inizio, $data_termine, $visibilita, $studente_posta, $docente_posta, $contact_id, $contact_fullname);

if (!$train->fetch())
	return;
?>

<article class="tirocinio" id="tirocinio_<?= $index ?>" data-nextid="<?= $index + 1 ?>">
	<div class='card'>
		<header class="card-header">
			<h1 class="card-header-title">
				<span>Tirocinio a <?= sanitize_html($business_name) ?></span>
				<a href="../../azienda/?id=<?= $business_id ?>" target="_blank">
					<span class="icon"><i class="fa fa-external-link" aria-hidden="true"></i></span>
				</a>
			</h1>
		</header>
		<div class="card-content">
			<div class="content">
				<strong>Studente: </strong>
				<a href="mailto:<?= sanitize_html($studente_posta) ?>" target="_blank">
					<?= sanitize_html($studente_posta) ?>
				</a>
				<br>
				<strong>Docente: </strong>
				<a href="mailto:<?= sanitize_html($docente_posta) ?>" target="_blank">
					<?= sanitize_html($docente_posta) ?>
				</a>
				<?php if($contact_id !== null)
				{
					?>
					<strong>Contatto aziendale: </strong>
					<a href="../../azienda/contatto?id=<?= $contact_id ?>" target="_blank">
						<?= sanitize_html($contact_fullname) ?>
					</a>
					<br>
					<?php
				}
				?>
				<br>
				<?php if ($data_termine === null)
				{
					?>
					<time datetime="<?= $data_inizio ?>">Dal <?= $data_inizio ?>. Data termine non pervenuta</time>
					<?php
				} else
				{
					?>
					<time datetime="<?= $data_inizio ?>/<?= $data_termine ?>">Dal <?= $data_inizio ?>
						al <?= $data_termine ?></time>
					<?php
				}
				?>
			</div>
		</div>
		<footer class="card-footer">
			<?php if ($tempo != 2) { ?>
				<a href="../tirocinio/?tirocinio=<?= $db_id ?>&page=resoconto" class="card-footer-item">
		<span class="icon">
		  <i class="fa fa-book" aria-hidden="true"></i>
		</span>
					Leggi resoconto
				</a>
				<?php ;
			} ?>
			<a href="../tirocinio/?tirocinio=<?= $db_id ?>&page=info" class="card-footer-item">
			  <span class="icon">
				<i class="fa fa-info" aria-hidden="true"></i>
			  </span>
				Info
			</a>
			<a href="../tirocinio/?tirocinio=<?= $db_id ?>&page=comments" class="card-footer-item">
			  <span class="icon">
				<i class="fa fa-comment" aria-hidden="true"></i>
			  </span>
				Commenta
			</a>
		</footer>
	</div>
</article>
<br>
