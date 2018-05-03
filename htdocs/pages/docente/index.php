<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 03/05/18
 * Time: 19.27
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

// Variabili pagina
$page = "Cruscotto";

/**
 * Questa query mostra le attività più recenti riguardati il docente loggato, non spaventarsi, non è nulla di che
 * fa una UNION di più tabelle in modo da poter ordinare gli eventi per il tempo di creazione, al momento mostra
 * - I nuovi commenti scritti sotto i tirocini del docente NON scritti dal docente stesso
 * - I tirocini che sono appena partiti
 * - I contatti che sono appena iniziati
 *
 * Mostra gli eventi già iniziati e quelli che inizierranno nelle prossime sei ore!
 *
 * @ docente non è altro che una variabile per evitare di dover scrivere ogni volta l'ID del docente
 * nella bind param, nel primo WHERE gli viene assegno il valore di ? con l'operatore := e se lo tiene anche per dopo :P
 * Lo scoperto solo il tre di Maggio quindi è probabile trovare vecchie interrogazioni al BD che contegano inutili
 * ripetizioni di '?' PAX
 */
$recenti = $server->prepare("
SELECT * FROM (
  SELECT C.quando AS 'time', 'commento' AS 'type', T2.id AS 'id', CONCAT(G.nome, ' ', G.cognome) AS 'preview'
    FROM Commento C
    INNER JOIN Tirocinio T2 on C.tirocinio = T2.id
    LEFT JOIN UtenteGoogle G on C.autore = G.id
  WHERE T2.docenteTutore = (@docente := ?) AND C.autore <> @docente
  UNION (
    SELECT T.dataInizio as 'time', 'tirocinio' AS 'type', T.id, A.nominativo AS 'preview'
      FROM Tirocinio T
      INNER JOIN Azienda A on T.azienda = A.id
    WHERE T.docenteTutore = @docente
  )
  UNION (
    SELECT E.inizio AS 'time', 'contatto' AS 'type', E.contatto AS 'id', CONCAT(C2.nome, ' ', C2.cognome) AS 'preview'
      FROM EntratoInContatto E
      INNER JOIN Contatto C2 on E.contatto = C2.id
    WHERE E.docente = @docente
  )
  ORDER BY time DESC
) tmp
WHERE time <= DATE_ADD(CURRENT_TIME(), INTERVAL 6 HOUR) LIMIT 4
");

$recenti->bind_param("i", $user->get_database_id());

$recenti->execute();
$recenti->bind_result($time, $type, $id, $prev);

?>
<html lang="it">
<head>
	<?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../common/google_navbar.php"; ?>
<br>
<section class="container">
	<div class="columns">
		<aside class="column is-3 is-fullheight">
			<?php
			$index_menu = 0;
			include "menu.php";
			?>
		</aside>
		<div class="column">
			<?php
			$trigerred = false;
			while($recenti->fetch())
			{
				$trigerred = true;
				?>
				<div class="media box">
					<?php
					switch ($type)
					{
						case "commento":
							?>
							<figure class="media-left">
							<span class="icon is-large">
								<i class="fa fa-comment fa-2x" aria-hidden="true"></i>
							</span>
							</figure>
							<div class="media-content">
								<h4 class="title is-4"><?= sanitize_html($prev) ?> ha scritto un commento</h4>
								<h4 class="subtitle is-4"><?= $time ?></h4>
								<p class="control has-text-right">
									<a class="button is-link" href="tirocini/tirocinio/?page=comments&tirocinio=<?= $id ?>">
										Guarda
									</a>
								</p>
							</div>
							<?php
							break;

						case "tirocinio":
							?>
							<figure class="media-left">
							<span class="icon is-large">
								<i class="fa fa-briefcase fa-2x" aria-hidden="true"></i>
							</span>
							</figure>
							<div class="media-content">
								<h4 class="title is-4">Tirocinio a <?= sanitize_html($prev) ?> è appena iniziato</h4>
								<h4 class="subtitle is-4"><?= $time ?></h4>
								<p class="control has-text-right">
									<a class="button is-link" href="tirocini/tirocinio/?tirocinio=<?= $id ?>">
										Guarda
									</a>
								</p>
							</div>
							<?php
							break;

						case "contatto":
							?>
							<figure class="media-left">
							<span class="icon is-large">
								<i class="fa fa-address-card fa-2x" aria-hidden="true"></i>
							</span>
							</figure>
							<div class="media-content">
								<h4 class="title is-4">Inizio dei contatti con <?= sanitize_html($prev) ?></h4>
								<h4 class="subtitle is-4"><?= $time ?></h4>
								<p class="control has-text-right">
									<a class="button is-link" href="azienda/contatto/?id=<?= $id ?>">
										Guarda
									</a>
								</p>
							</div>
							<?php
							break;

						default:
							throw new RuntimeException("Stato invalido! Stato: $type", -1); // Lul ke simpatico, speriamo non crei problemi
							break;
					}
					?>
				</div>
				<?php
			}

			if(!$trigerred)
			{
                ?>
				<h4 class="title is-4 has-text-centered">
					<span class="icon">
						<i class="fa fa-frown-o" aria-hidden="true"></i>
					</span>
					<span>
						Non c'è nulla da mostrare qua, riprovare in un secondo momento!
					</span>
				</h4>
				<?php
			}
			?>
		</div>
	</div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
</body>
</html>