let referente_tbody = $ ("#referenti_tbody");
let referente = new GetHandler(referente_tbody, BASE + "rest/users/list/contatti.php", function (datum, tbody)
{
	tbody.append (
		"<tr data-dbid='"+ datum.id + "'>" +
		"<td data-type='nome'>" + datum.nome + " " + datum.cognome + "</td>" +
		"<td>" + (datum.email === null ? "" : datum.email )+ "</td>" +
		"<td>" + (datum.telefono === null ? "" : datum.telefono) + "</td>" +
		"<td><a tabindex=''>Seleziona</a></td>" +
		"</tr>"
	);
});
referente.setQuery(undefined);

let referente_listener = new TableSelection(referente_tbody);

let referente_panel = new TogglePanel($("#referente_modal"));

$("#seleziona_tutore_trigger").on("click", function ()
{
	if(azienda_listener.getSelectedRow() === null)
	{
		$(this).prop("disabled", true);
		return;
	}

	referente.setParams({azienda: azienda_listener.getSelectedRow().data("dbid")});
	referente.get();
	referente_panel.show();
});

$("#seleziona_referente_aggiungi").on("click", function ()
{
	let selezione = referente_listener.getSelectedRow();

	if(selezione !== null)
	{
		$("#tutore_aziendale_nome").val(selezione.find("td[data-type='nome']").html());
		$("#tutore_aziendale_id").val(selezione.data("dbid"))
	}

	referente_panel.hide();
});

$("#seleziona_referente_scarta").on("click", function ()
{
	referente_panel.hide();
});