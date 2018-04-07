let azienda_tbody = $ ("#aziende_tbody");
let azienda = new GetHandler(azienda_tbody, BASE + "rest/users/list/aziende.php", function (datum, tbody)
{
	tbody.append (
		"<tr data-dbid='"+ datum.id + "'>" +
		"<td data-type='nome'>" + datum.nominativo + "</td>" +
		"<td>" + (datum.IVA === null ? "" : datum.IVA )+ "</td>" +
		"<td>" + (datum.codiceFiscale === null ? "" : datum.codiceFiscale) + "</td>" +
		"<td><a tabindex=''>Seleziona</a></td>" +
		"</tr>"
	);
});
let azienda_listener = new TableSelection(azienda_tbody);

azienda_listener.addHandler(function (row)
{
	$("#seleziona_azienda_aggiungi").prop("disabled", row === null);
});

$("#seleziona_azienda_aggiungi").on("click", function ()
{
	let selected = azienda_listener.getSelectedRow();

	if(selected === null)
		return;

	let nome = selected.find("td[data-type='nome']").html();
	console.log(nome);

	// TODO Prosegui
});

azienda.addButton("forward", $ ("#azienda_forward"));
azienda.addButton("backward", $ ("#azienda_back"));
azienda.addButton("reload", $("#azienda_reload"));

azienda.get();

$("#azienda_cerca").submit(function ()
{
	let filtro = $(this).find("input[name='query']").val();
	azienda.setQuery(filtro);

	return false;
});