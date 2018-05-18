<?php
/**
 * Created by PhpStorm.
 *
 * Questo file crea una finestra di dialogo utile per mostrare un eventuale errore di
 * processo che ha generato un'eccezzione  mysqli!
 *
 * User: dario
 * Date: 18/05/18
 * Time: 8.22
 */


if (isset($_GET["errors"]))
{
	$id = rand(1, 150000);
	$errori = urldecode($_GET["errors"]);
	?>
	<article class="message is-danger" id="errore_db<?= $id ?>">
		<div class="message-header">
			<p>
				<span class="icon">
					<i class="fa fa-database"></i>
				</span>
				<span>
					Errore di processo
				</span>
			</p>
			<button class="delete" aria-label="delete" id="errore_db_delete<?= $id ?>"></button>
		</div>
		<div class="message-body">
			<p>Si sono verificati dei problemi durante il processo dei dati!</p>
			<pre><?= sanitize_html($errori) ?></pre>
		</div>
		<script>
			$ ("#errore_db_delete<?= $id ?>").on ("click", function ()
			{
				$ ("#errore_db<?= $id ?>").remove ();
			});
		</script>
	</article>
	<?php
}
