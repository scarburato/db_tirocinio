/**
 * Questo evento viene generato ogni qualvolta viene cambiato il contatto e/o le date.
 * Controlla se l'inserimento genererà un eccezione SQL
 */
$(".data_dinamica,#contatto-id").on("change", function ()
{
	const contatto = $("#contatto-id").val();
	const start = $("#data_inizio").val();
	const end = $("#data_fine").val();

	// Se non è settato il contatto e la data d'avvio esco
	if(contatto === undefined || contatto === "" || start === undefined || start === "")
		return;

	$.get(
		BASE + "rest/contacts/sovrappone.php",
		{
			contatto: contatto,
			inizio: start,
			fine: end
		}
	)
		.done(function (data)
		{
			$("#contatto-sovrapposto").toggle(data.sovrappone);
		})
});