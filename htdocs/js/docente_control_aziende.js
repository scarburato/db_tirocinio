/**
 * Funzione che mette una nuova password
 */
function new_pass()
{
	$("#parolaordine").val
	(
		Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
	);
}
new_pass();

$("#nuovaparola").on("click", new_pass);

/// SEZIONE SEDI
sedi = [];

listener_nuova_sede = new TogglePanel("#aggiungi_sede");
listener_nuova_sede.setOnShow(function (element)
{
	element.value = (element.name === "stato") ? "Italia" : "";
});

$("#aggiungi_sede_trigger").on("click", function ()
{
	listener_nuova_sede.show();
});

$("#aggiungi_sede_scarta").on("click", function ()
{
	listener_nuova_sede.hide();
});

$("#aggiungi_sede_aggiungi").on("click", function ()
{
	let form = $("#aggiungi_sede_form");
	let nominativo = form.find('input[name="nominativo"]');

	if(nominativo.val().length === 0)
	{
		nominativo.addClass("is-danger");
		return;
	}

	let key = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15) + Date.now();
	sedi[key] = form.serializeArray();

	$("#sedi_memoria").append(
		"<tr>\n" +
			"<td>"+ nominativo.val() + "</td>\n" +
			"<td style=\"width: 20%\">\n" +
				"<a class=\"button is-fullwidth is-danger is-small rimuovi_sede_trigger\" data-key='" + key +"'>\n" +
					"<span class=\"icon\">\n" +
						"<i class=\"fa fa-trash\" aria-hidden=\"true\"></i>\n" +
					"</span>\n" +
					"<span>\n" +
						"Rimuovi\n" +
					"</span>\n" +
				"</a>\n" +
			"</td>\n" +
		"</tr>"
	);
	listener_nuova_sede.hide();
});

$(document).on("click", ".rimuovi_sede_trigger", function ()
{
	delete sedi[$(this).data("key")];
	$(this).parent().parent().remove();
});

//// FORM PRINCIPE
