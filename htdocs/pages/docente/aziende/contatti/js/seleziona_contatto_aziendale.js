let contatti_thead = $ ("#contatti_thead");
let contatti_tbody = $ ("#contatti_tbody");
let input_azienda  = $ ("#azienda_id");
const URL_BTN = $("#contatto-occupato-href").prop("href");

let contatti = new GetHandler (contatti_tbody, BASE + "rest/users/list/contatti.php", function (datum, tbody)
	{
		let row = $ (
			"<tr/>",
			{
				"data-dbid": datum.id,
				"data-occupato": datum.occupato
			}
		);
		const fields = ["nome", "cognome", "qualifica"];

		fields.forEach (function (e)
			{
				$ (
					"<td/>",
					{
						"data-colname": e,
						text: datum[e]
					}
				).appendTo (row);
			}
		);

		row.append($("<td><a tabindex=''>Seleziona</a></td>"));

		row.appendTo (tbody);
	});

input_azienda.on("change", function ()
{
	const azienda = $(this).val();
	$ ("#seleziona_contatto_trigger").prop("disabled", azienda === undefined || azienda === "");

	if(azienda !== undefined)
		contatti.setParams({
			azienda: azienda
		});
});

let contatti_listener = new TableSelection (contatti_tbody);

let contatti_panel = new TogglePanel ("#contatto_modal");

$ ("#seleziona_contatto_trigger").on ("click", function ()
{
	if(input_azienda.val() === undefined)
		return;

	contatti.get ();
	contatti_panel.show ();
});

$ ("#seleziona_contatto_scarta").on ("click", function ()
{
	contatti_panel.hide ();
});

$ ("#seleziona_contatto_aggiungi").on ("click", function ()
{
	let selected = contatti_listener.getSelectedRow ();

	if (selected === null)
		return;

	$("#contatto-id").val(selected.data("dbid"));
	$("#contatto-occupato-href").prop("href", URL_BTN + "?id=" + selected.data("dbid"));
	$("#contatto-occupato").toggle(selected.data("occupato") === 1);

	let nome = selected.find("td[data-colname='nome']").html();
	let cognome = selected.find("td[data-colname='cognome']").html();

	$("#contatto-name").val(nome + " " + cognome);

	contatti_panel.hide ();
});

contatti.addButton ("forward", $ ("#contatto_forward"));
contatti.addButton ("backward", $ ("#contatto_back"));