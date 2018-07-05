let permessi_disponibili = new TableSelection($("#privilegi"));
let permessi_attuali = new TableSelection($("#applicati"));
let permessi = new TableChooser(permessi_attuali, permessi_disponibili);

permessi.addButtonMoveToA($("#aggiungi"));
permessi.addButtonMoveToB($("#rimuovi"));

$("#commit").on("click", function ()
{
	if(!confirm("Questo andrà a sovrascrivere le impostazioni attuali! Continuare??"))
		return;

	let permessi = [];

	$("#applicati").children().each(function (index)
	{
		permessi.push($(this).data("id"));
	});
	console.log(permessi);

	$.post(
		BASE + "rest/groups/set/permissions.php",
		{
			group: CURRENT_GROUP,
			permissions: (permessi.length > 0 ? permessi : 0)
		}
	)
		.done(function (data)
		{
			console.log(data);
		})
});