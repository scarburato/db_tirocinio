let azienda_tbody = $ ("#aziende_tbody");
console.log(BASE + "rest/users/aziende.php");
let azienda = new GetHandler(azienda_tbody, BASE + "rest/users/aziende.php", function (datum, tbody)
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

let azienda_panel = new TogglePanel("#azienda_modal");

$("#seleziona_azienda_trigger").on("click", function ()
{
	azienda.get();
	azienda_panel.show();
});

$("#seleziona_azienda_scarta").on("click", function ()
{
	azienda_panel.hide();
});

$("#seleziona_azienda_aggiungi").on("click", function ()
{
	let selected = azienda_listener.getSelectedRow();

	if(selected === null)
		return;

	let nome = selected.find("td[data-type='nome']").html();
	console.log(nome);
	$("#azienda_name").val(nome);
	main_form.find("input[name='azienda']").val(selected.data("dbid"));

	azienda_panel.hide();
});

azienda.addButton("forward", $ ("#azienda_forward"));
azienda.addButton("backward", $ ("#azienda_back"));

$("#azienda_cerca").submit(function ()
{
	let filtro = $(this).find("input[name='query']").val();
	azienda.setQuery(filtro);

	return false;
});