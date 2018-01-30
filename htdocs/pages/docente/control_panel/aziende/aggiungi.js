/**
 * Funzione che mette una nuova password
 */
function new_pass()
{
	$("#parolaordine").val
	(
		Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
	);
}
new_pass();

$("#nuovaparola").on("click", new_pass);