let semaforo = true;
let org_units = [];
let table_handler = null;
let table_toggle = new TogglePanel("#seleziona_orgunit");
let actual_panel = null;
let tables = [];

$.get (
	BASE + "rest/domain/orgunits/list.php",
)
	.done (function (data)
	{
		org_units = data;

		let table = $("#orgunits_body");

		for(let i = 0; i < data.length; i++)
			table.append(
				"<tr data-raw='" + data[i].orgUnitPath + "'>" +
					"<td>" + data[i].orgUnitPath + "</td>" +
					"<td><a tabindex=''>Seleziona</a></td>" +
				"</tr>"
			);

		table_handler = new TableSelection(table);
		table_handler.addHandler(function (e)
		{
			$("#seleziona_orgunit_aggiungi").prop("disabled", e === null)
		});
	})
	.always (function ()
	{
		semaforo = false;
	});

$(".add-button").on("click", function ()
{
	//console.log($(this).data("orgtype"))
	if(semaforo)
		return;

	semaforo = true;
	actual_panel = $(this).data("orgtype");
	table_toggle.show();
	semaforo = false;
});

$("#seleziona_orgunit_scarta").on("click", function ()
{
	if(semaforo)
		return;

	semaforo = true;
	table_toggle.hide();
	semaforo = false;
});

$("#seleziona_orgunit_aggiungi").on("click", function ()
{
	if(semaforo)
		return;

	semaforo = true;
	table_toggle.hide();

	$(".orgunits[data-orgtype='" + actual_panel + "']").append(
		"<tr data-raw='" + table_handler.getSelectedRow().data("raw") + "'>" +
			table_handler.getSelectedRow().html() +
		"</tr>"
	);
	semaforo = false;
});

$(".orgunits").each(function (index)
{
	tables.push(new TableSelection($(this)));
});