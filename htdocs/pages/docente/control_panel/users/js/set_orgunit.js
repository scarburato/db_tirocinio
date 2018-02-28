let semaforo = true;
let org_units = [];
let table_handler = null;
let table_toggle = new TogglePanel("#seleziona_orgunit");
let actual_panel = null;
let tables = {

};

/** Faccio una GET al server per ottenere in risposta le unità organizzative
 * Tenere presente che le chiamate all'API di Google sono limitate di base a
 * 150'000 chiamate giornaliere.
 */
$.get (
	BASE + "rest/domain/orgunits/list.php",
)
	.done (function (data)
	{
		// Una volta ottenuta risposta aggiungo i dati alla tabella
		let org_units = data;

		let table = $("#orgunits_body");

		for(let i = 0; i < data.length; i++)
			table.append(
				"<tr data-raw='" + data[i].orgUnitPath + "'>" +
					"<td>" + data[i].orgUnitPath + "</td>" +
					"<td style=\"width: 20%\"><a tabindex=''>Seleziona</a></td>" +
				"</tr>"
			);

		/**
		 * È globlae bif
		 */
		table_handler = new TableSelection(table);
		table_handler.addHandler(function (e)
		{
			$("#seleziona_orgunit_aggiungi").prop("disabled", e === null)
		});

		$(".remove-button").removeClass("is-loading");
		$(".add-button").removeClass("is-loading");

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

	// Variabli
	let tabella_attuale = $(".orgunits[data-orgtype='" + actual_panel + "']");

	if($(".orgunits").find("tr[data-raw=\"" + table_handler.getSelectedRow().data("raw") + "\"]").length > 0)
	{
		alert("Questa unità organizzativa è già stata selezionata!");
	}
	else
	{
		tabella_attuale.append (
			"<tr data-raw='" + table_handler.getSelectedRow ().data ("raw") + "'>" +
			table_handler.getSelectedRow ().html () +
			"</tr>"
		);
	}
	semaforo = false;
});

$(".remove-button").on("click", function ()
{
	if(semaforo)
		return;

	semaforo = true;
	let tabella_attuale = tables[$(this).data("orgtype")];
	let riga = tabella_attuale.getSelectedRow();


	if(riga !== null)
		riga.remove();

	semaforo = false;
});

$(".orgunits").each(function (index)
{
	tables[$(this).data("orgtype")] = (new TableSelection($(this)));
});

/**
 * Questa funzione viene chiamata quando si carica,
 * Si occupa di serializzare la configuarazione attuale e di fare richiesta al server per accettarla!
 */
$("#upload").on("click", function ()
{
	if(semaforo)
		return;

	semaforo = true;

	if(!confirm("Attenzione, le impostazioni sul servente verrano sovrascritte interamente con le presenti!"))
	{
		semaforo = false;
		return;
	}

	let esportare = [];

	$(".orgunits").each(function (index)
	{
		let type = $(this).data("orgtype");
		$(this).children().each(function (index)
		{
			esportare.push(
				{
					type: type,
					path: $(this).data("raw")
				}
			);
		});
	});

	console.log((esportare));

	semaforo = false;
});