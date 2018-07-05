let docente_tbody = $ ("#docenti_tbody");
let docente = new GetHandler(docente_tbody, BASE + "rest/users/list/docenti.php", function (datum, tbody)
{
	tbody.append (
		"<tr data-dbid='"+ datum.id + "'>" +
		"<td data-type='nome'>" + datum.nome + "</td>" +
		"<td data-type='cognome'>" + datum.cognome + "</td>" +
		"<td>" + (datum.indirizzo_posta === null ? "" : datum.indirizzo_posta) + "</td>" +
		"<td><a tabindex=''>Seleziona</a></td>" +
		"</tr>"
	);
});

let docente_listener = new TableSelection(docente_tbody);

let docente_panel = new TogglePanel("#docente_modal");

$("#seleziona_docente_trigger").on("click", function ()
{
	docente.get();
	docente_panel.show();
});

$("#seleziona_docente_scarta").on("click", function ()
{
	docente_panel.hide();
});

$("#seleziona_docente_aggiungi").on("click", function ()
{
	let selected = docente_listener.getSelectedRow();

	if(selected === null)
		return;

	let nome = selected.find("td[data-type='nome']").html() + " " + selected.find("td[data-type='cognome']").html();
	console.log(nome);
	$("#docente_name").val(nome);
	main_form.find("input[name='docente']").val(selected.data("dbid"));

	docente_panel.hide();
});

docente.addButton("forward", $ ("#docente_forward"));
docente.addButton("backward", $ ("#docente_back"));

$("#docente_cerca").submit(function ()
{
	let filtro = $(this).find("input[name='query']").val();
	docente.setQuery(filtro);

	return false;
});