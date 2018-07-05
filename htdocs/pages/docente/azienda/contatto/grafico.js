let info = new TogglePanel($("#info-event"));
info.onShow(() => {});

$("#escilo-info-eventi").on("click", () => {info.hide()});

let timeline = new vis.Timeline($("#calendario0")[0],null,{
	editable: false,
	stack: false
});
let current_key;

timeline.setGroups(docenti);
timeline.setItems(interazioni);
timeline.on("doubleClick", (prop) =>
{
	/* Esco se il modal è già mostrato ovvero l'oggetto selezionato non è un item del digramma, es può essere group*/
	if(info.isActive() || prop.what !== "item")
		return;

	const interazione_scelta = interazioni.get(prop.item);

	$("#fullname").text(interazione_scelta.rawdata.fullname);
	$("#inizio").text(interazione_scelta.start);
	$("#fine").prop("readonly", !interazione_scelta.rawdata.personal);
	$("#fine").val(interazione_scelta.rawdata.end);

	$("#bao").prop("disabled", !interazione_scelta.rawdata.personal);

	console.log(interazione_scelta);

	current_key = interazione_scelta.rawdata.key;
	info.show();
	$("#fine").val(interazione_scelta.rawdata.end);

});

/* Evento avviato dal pulsante cambia data di termine. Chiamata lo script PHP per la modifica della roba */
$("#bao").on("click", () =>
{
	let request = {newend: $("#fine").val()};
	$.extend(request, current_key);
	$.get(
		"aggiorna.php",
		request
	)
		.done((data) =>
		{
			if(data.error === undefined)
				location.reload();
			else
				alert(data.what);
		});
});