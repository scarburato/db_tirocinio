<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 22/04/18
 * Time: 18.45
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();

// Se manca l'id della get eccezzione
if(empty($_GET["id"]))
	throw new RuntimeException("Manca l'id!", -1);

// Ottengo dalla base di dati le informazioni del contatto!
$contatto = $server->prepare(
	"SELECT C.nome, C.cognome, C.qualifica, C.ruoloAziendale, C.telefono, C.FAX, C.email, A.id, A.nominativo
			FROM Contatto C 
			LEFT JOIN Azienda A on C.azienda = A.id
		  WHERE C.id = ?");

$contatto->bind_param("i", $_GET["id"]);
$contatto->execute();
$contatto->bind_result($nome, $cognome, $qualifica, $ruolo, $telefono, $telefax, $posta_elettronica, $azienda_id, $azienda_nome);

// Ahia sembra che non esista
if($contatto->fetch() !== true)
	throw new RuntimeException("Richiesta malformata, il contatto richiesto non esiste", 404);

// I risulati li salvo perché dovrò eseguire un'ulteriore interrogazione
$contatto->store_result();
$contatto->close();

/**
 * Sto andando ad ottenere dalla base di dati tutte le interazioni mai avute con tale contatto. Le informazi che otterò
 * le andrò a convertire in JSON e le passerò allo script JS dell'utente. Il programma JS usera Visjs per
 * convertire le informazioni temporali in un grafico stile Gannt. Ogni docente sarà su una riga diversa, questo è
 * possibile perché nella base di dati e inmpossibili che lo stesso docente abbia una sovrapposizione temporale!
 *
 * Per maggiori informazioni su Visjs Charts
 * @see http://visjs.org/docs/timeline/
 */
$docenti_coinvolti = $server->prepare("SELECT DISTINCT E.docente, U.indirizzo_posta FROM EntratoInContatto E LEFT JOIN UtenteGoogle U ON U.id = E.docente WHERE E.contatto = ?");
$docenti_coinvolti->bind_param("i", $_GET["id"]);
$docenti_coinvolti->execute();
$docenti_coinvolti->bind_result($docente, $inidirizzo);

$docenti_coinvolti_res = [];

while($docenti_coinvolti->fetch())
{
	array_push($docenti_coinvolti_res, [
		"id" => $docente,
		"content" => $inidirizzo
	]);
}


$docenti_coinvolti->close();

$lista_interazioni = $server->prepare("
	SELECT E.inizio, E.fine, DATE_ADD(CURRENT_DATE(),INTERVAL 1 MONTH), E.fine IS NULL OR E.fine > CURRENT_DATE(), E.inizio <= CURRENT_DATE(),G.id, G.nome, G.cognome, G.indirizzo_posta
	  FROM EntratoInContatto E 
	  LEFT JOIN Docente D on E.docente = D.utente
	  LEFT JOIN UtenteGoogle G on D.utente = G.id
	WHERE E.contatto = ?
	ORDER BY E.inizio ASC
");

$lista_interazioni->bind_param("i", $_GET["id"]);
$lista_interazioni->execute();
$lista_interazioni->bind_result($inizio, $fine, $un_mese,$presenteOfuturo, $presenteOpassato, $doc_dbid, $doc_nome, $doc_cognome, $doc_posta);

$grafico = [];
// Scorro effettivamente il risulato e lo inserisco in un vettore che convertitò a JS
while($lista_interazioni->fetch())
{
	array_push($grafico, [
		"content" => $presenteOfuturo ? ($presenteOpassato ? "In corso" : "Futuro"): "Terminato",
		"start" => $inizio,
		"end" => $fine === null ? $un_mese : $fine,
		"group" => $doc_dbid,
		"style" => $fine === null ? "background-color: orange;" : "",
		"rawdata" => [
			"end" => $fine,
			"personal" => $doc_dbid === $user->get_database_id(),
			"fullname" => $doc_cognome . " " . $doc_cognome,
			"key" => [ // Questa è la chiave di questa tabella :P
				"docente" => $doc_dbid,
				"contatto" => $_GET["id"],
				"inizio" => $inizio
			]
		]
	]);
}

$page = "Contatto " . $nome . " " . $cognome;
?>

<html lang="it">
<head>
	<script src="<?= BASE_DIR ?>js/lib/vis.min.js"></script>
	<script src="<?= BASE_DIR ?>js/togglePanel.js"></script>
	<link rel="stylesheet" type="text/css" href="<?= BASE_DIR ?>css/vis.min.css">

	<?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/head.phtml"; ?>

	<script>
		const docenti = new vis.DataSet(<?= json_encode($docenti_coinvolti_res, JSON_UNESCAPED_UNICODE) ?>);
		const interazioni = new vis.DataSet(<?= json_encode($grafico, JSON_UNESCAPED_UNICODE) ?>);
	</script>

	<style>
		.calendario {
			width: 100%;
		}

		/** baco @see https://github.com/almende/vis/issues/2032 */
		.vis-labelset .vis-label .vis-inner {
			min-height: 45px;
			width: 100%;
			text-align: left;
			box-sizing: border-box;
		}

		.vis-label {
			max-width: 11rem;
			min-height: 45px;
		}
	</style>
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
		<div class="column is-9">
			<div class="media">
				<figure class="media-left">
					<p>
						<span class="icon is-large" style="width: 96px;">
							<i class="fa fa-address-card fa-3x" aria-hidden="true"></i>
						</span>
					</p>
				</figure>
				<div class="media-content">
					<h2 class="title is-2"><?= sanitize_html($nome . " " .$cognome) ?></h2>

					<table class="table is-narrow is-fullwidth">
						<tr>
							<th style="width: 30%">Nome</th>
							<td><?= sanitize_html($nome) ?></td>
						</tr>
						<tr>
							<th>Cognome</th>
							<td><?= sanitize_html($cognome) ?></td>
						</tr>
						<tr>
							<th>Azienda</th>
							<td>
								<a title="Mostra scheda azienda"
									href="../?id=<?= $azienda_id ?>">
									<?= sanitize_html($azienda_nome) ?>
								</a>
							</td>
						</tr>
						<tr>
							<th>Ruolo aziendale</th>
							<td><?= sanitize_html($ruolo) ?></td>
						</tr>
						<tr>
							<th>Qualifica</th>
							<td><?= sanitize_html($qualifica) ?></td>
						</tr>
						<tr>
							<th>Indirizzo di posta elettronica</th>
							<td>
								<a href="mailto:<?= sanitize_html($posta_elettronica) ?>"><?= sanitize_html($posta_elettronica) ?></a>
							</td>
						</tr>
						<tr>
							<th>Numero di telefono</th>
							<td>
								<a href="tel:<?= sanitize_html($telefono) ?>"><?= sanitize_html($telefono) ?></a>
							</td>
						</tr>
						<tr>
							<th>Numero di telefax</th>
							<td>
								<a href="tel:<?= sanitize_html($telefax) ?>"><?= sanitize_html($telefax) ?></a>
							</td>
						</tr>
					</table>
				</div>
			</div>

			<!-- Calendario! -->
			<p class="help">In arancio i periodi senza data di termine!</p>
			<div class="calendario" id="calendario0">

			</div>
		</div>
	</div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>

<!-- -->
<div class="modal" id="info-event">
	<div class="modal-background"></div>
	<div class="modal-card">
		<header class="modal-card-head">
			<p class="modal-card-title">Informazioni</p>
			<button id="escilo-info-eventi" class="delete" aria-label="close"></button>
		</header>
		<section class="modal-card-body">
			<h4 class="title is-4" id="fullname"></h4>
			<table class="table is-narrow is-fullwidth">
				<tr>
					<th>Data inizio</th>
					<td id="inizio"></td>
				</tr>
				<tr>
					<th>Data fine</th>
					<td>
						<p class="control">
							<input class="input" type="date" id="fine" placeholder="Data fine">
						</p>
					</td>
				</tr>
			</table>
			<p class="control has-text-right">
				<button class="button is-success" id="bao">Cambia data di termine</button>
			</p>
		</section>
	</div>
</div>

<script defer src="grafico.js"></script>
</body>
</html>