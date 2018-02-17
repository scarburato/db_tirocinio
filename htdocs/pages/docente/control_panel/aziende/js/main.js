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

$("#aggiungi_sede_aggiungi").on("click", validate_form);
$("#aggiungi_sede_form").find("input").on("keyup", function (e)
{
	if(e.keyCode === 13)
		validate_form();
});

/**
 * Funzione che controlla se la nuova sede è valida
 * Se è valida aggiunge alla tabella della form la nuova sede sotto forma di riga di quest'ultima
 * Memorizza la sede in un vettore assieme ad un ID autogenerato per la modifica
 */
function validate_form()
{
	let form = $ ("#aggiungi_sede_form");
	let nominativo = form.find ('input[name="nominativo"]');

	if (nominativo.val ().length === 0)
	{
		nominativo.addClass ("is-danger");
		return;
	}

	let key = Math.random ().toString (36).substring (2, 15) + Math.random ().toString (36).substring (2, 15) + Date.now ();
	let valori_vettore = form.serializeArray();
	let valori =
		{
			id: key
		};

	// Conversione ad oggetto perché sì
	valori_vettore.forEach(function (elemento)
	{
		valori[elemento.name] = elemento.value;
	});

	sedi.push(valori);

	$ ("#sedi_memoria").append (
		"<tr>\n" +
		"<td>" + nominativo.val () + "</td>\n" +
		"<td style=\"width: 20%\">\n" +
		"<a class=\"button is-fullwidth is-danger is-small rimuovi_sede_trigger\" data-key='" + key + "'>\n" +
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
	listener_nuova_sede.hide ();
}

/**
 * Rimuove la sede selezionata dal vettore sedi e dalla tabella!
 */
$(document).on("click", ".rimuovi_sede_trigger", function ()
{
	let key = $(this).data("key");
	let delme = sedi.findIndex(function (elemento)
	{
		return (elemento.id === key);
	});

	sedi.splice(delme,1);

	$(this).parent().parent().remove();
});


//// SELEZIONE ATECO


//// FORM PRINCIPE

/**
 * Controllo nella form princiaple
 * @return true Se il la form è valida
 * @return false Se la from nonè valida
 */
$("#main_form").submit(function ()
{
	// Controllo nome
	let nome = $(this).find("input[name='nominativo']");

	if(nome.val().length < 1)
	{
		nome.addClass("is-danger");
		nome.parent().find("p").addClass("is-danger");
		nome.focus();

		return false;
	}

	// Controllo parola d'ordine
	let parolaordine = $(this).find("input[name='parolaordine']");

	if(parolaordine.val() <= 8)
	{
		parolaordine.addClass("is-danger");
		nome.parent().find("p").addClass("is-danger");

		parolaordine.focus();

		return false;
	}

	// Controllo ateco
	let ateco = $(this).find("input[name='ateco_unique']");

	if(ateco.val().length < 1)
	{
		ateco.addClass("is-danger");
		ateco.parent().find("p").addClass("is-danger");
		ateco.parent().parent().find("a.button").removeClass("is-info");
		ateco.parent().parent().find("a.button").addClass("is-danger");

		ateco.focus();

		return false;
	}

	$(this).find("input[name='sedi']").val(JSON.stringify(sedi));
	return true;
});


