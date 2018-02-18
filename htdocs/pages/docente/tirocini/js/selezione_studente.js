let studente = new GetHandler($ ("#studenti_tbody"), "rest/get_studenti.php", function (datum, tbody)
{
	tbody.append (
		"<tr data-dbid='"+ datum.id + "'>" +
		"<td>" + datum.nome + "</td>" +
		"<td>" + datum.cognome + "</td>" +
		"<td>" + (datum.matricola === null ? "" : datum.matricola) + "</td>" +
		"<td><a tabindex=''>Seleziona</a></td>" +
		"</tr>"
	);
});

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

studente.addButton("forward", $ ("#studente_forward"));
studente.addButton("backward", $ ("#studente_back"));

$("#studente_cerca").submit(function ()
{
	let filtro = $(this).find("input[name='query']").val();

	studente.setQuery(filtro);

	return false;
});