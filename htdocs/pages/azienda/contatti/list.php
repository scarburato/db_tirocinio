<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 04/06/18
 * Time: 9.37
 */
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_FACTORY, \auth\User::UNAUTHORIZED_THROW);

$pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 0;

$contatti = new class($server,
	"
	SELECT C.id, C.nome, C.cognome, C.FAX, C.telefono, C.email, C.qualifica, C.ruoloAziendale, EXISTS(SELECT * FROM EntratoInContatto E WHERE E.contatto = C.id) FROM Contatto C WHERE C.azienda = ?
	"
) extends \helper\Pagination
{
	public function compute_rows(): int
	{
		$user = new \auth\User();
		$row_tot = 0;
		$conta = $this->link->prepare(
			"SELECT COUNT(id) FROM Contatto C WHERE C.azienda = ?");

		$conta->bind_param("i", $user->get_database_id());

		$conta->execute();
		$conta->bind_result($row_tot);
		$conta->fetch();
		$conta->close();

		return $row_tot;
	}
};

$contatti->set_limit(4);
$contatti->set_current_page($pagina);

$contatti->bind_param("i", $user->get_database_id());

$contatti->execute();
$contatti->bind_result($id, $nome, $cognome, $telefax, $telefono, $posta_elettronica, $qualifica, $ruolo, $not_eliminabile);
$nav = new \helper\PaginationIndexBuilder($contatti);
$nav->set_pagination_builder(new \helper\IndexJS());

?>
<div>
	<?php
	$nav->generate_index($_GET);
	?>
	<br>
	<?php while ($contatti->fetch())
	{
		?>
		<div class="box ajax_comment" data-current-page="<?= $contatti->get_current_page() ?>">
			<h3 class="title is-3"><?= sanitize_html($nome . " " . $cognome) ?></h3>
			<div class="field">
				<p class="control has-text-right">
					<a class="button is-danger is-small" <?= $not_eliminabile ? "disabled" : "" ?> title="Elimina contatto">
						<span class="icon">
							<i class="fa fa-trash" aria-hidden="true"></i>
						</span>
					</a>
				</p>
			</div>
			<table class="table is-fullwidth is-narrow">
				<tr>
					<th style="width: 35%">Telefono</th>
					<td><a href="tel:<?= sanitize_html($telefono) ?>"><?= sanitize_html($telefono) ?></a></td>
				</tr>
				<tr>
					<th>Telefax</th>
					<td><a href="tel:<?= sanitize_html($telefax) ?>"><?= sanitize_html($telefax) ?></a></td>
				</tr>
				<tr>
					<th>Posta elettronica</th>
					<td><a href="mailto:<?= sanitize_html($posta_elettronica) ?>"><?= sanitize_html($posta_elettronica) ?></a></td>
				</tr>
				<tr>
					<th>Qualifica</th>
					<td><?= sanitize_html($qualifica) ?></a></td>
				</tr>
				<tr>
					<th>Ruolo</th>
					<td><?= sanitize_html($ruolo) ?></a></td>
				</tr>
			</table>
		</div>
		<?php
	}?>

	<?php
	$nav->generate_index($_GET);
	?>
</div>