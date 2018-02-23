let studente_tbody = $ ("#studenti_tbody");
let studente = new GetHandler(studente_tbody, BASE + "rest/users/studenti.php", function (datum, tbody)
{
	tbody.append (
		"<tr data-dbid='"+ datum.id + "'>" +
		"<td data-type='nome'>" + datum.nome + "</td>" +
		"<td data-type='cognome'>" + datum.cognome + "</td>" +
		"<td>" + (datum.matricola === null ? "" : datum.matricola) + "</td>" +
		"<td><a tabindex=''>Seleziona</a></td>" +
		"</tr>"
	);
});

let studente_listener = new TableSelection(studente_tbody);

let studente_panel = new TogglePanel("#studente_modal");

$("#seleziona_studente_trigger").on("click", function ()
{
	studente.get();
	studente_panel.show();
});

$("#seleziona_studente_scarta").on("click", function ()
{
	studente_panel.hide();
});

$("#seleziona_studente_aggiungi").on("click", function ()
{
	let selected = studente_listener.getSelectedRow();

	if(selected === null)
		return;

	let nome = selected.find("td[data-type='nome']").html() + " " + selected.find("td[data-type='cognome']").html();
	console.log(nome);
	$("#studente_name").val(nome);
	main_form.find("input[name='studente']").val(selected.data("dbid"));

	studente_panel.hide();
});

studente.addButton("forward", $ ("#studente_forward"));
studente.addButton("backward", $ ("#studente_back"));

$("#studente_cerca").submit(function ()
{
	let filtro = $(this).find("input[name='query']").val();
	studente.setQuery(filtro);

	return false;
});