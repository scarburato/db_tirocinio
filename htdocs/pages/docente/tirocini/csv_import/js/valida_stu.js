/*
File che valida gli indirizzi di posta elettronica e le aziende!
 */
// Roba di preparazione eseguita al carimaneto della paidshgiuds f
$("#data_stu_end").hide();
$("#data_stu_mail_fail_helper").hide();
$("#data_stu_next").prop("disabled", true);

/**
 * Siamo nel dialogo di associazione studenti trovati con l'indirizzo di posta elettronica di Google! Questo evento
 * viene eseguito quando viene premuto il tasto per validare l'indirizzo con l'API rest, usata anche nel pannello di
 * controllo
 */
$("#data_stu_mail_validate").on("click", function ()
{
	// Disattivo il tasto
	$(this).prop("disabled", true);

	// ottengo roba
	let input = $("#data_stu_mail");
	let _self = this;
	const gimport = $("#data_stu_gimport").is(':checked');

	// Chiamata all'API artigianale
	$.get
	(
		BASE + "rest/users/get/studente.php",
		{
			email: input.val()
		}
	)
		.done(function (data)
		{
			let success = data.error === undefined; // Sono riuscito
			let id = data.id;

			/** se l'utente non è stato trovato AND è stata spuntata la spunta per l'import automatico, cerco di
			 * importarlo chiamando l'altra API */
			if(!success && gimport)
			{
				jQuery.ajax({
					url: BASE + "rest/domain/users/insert.php",
					type: "get",
					data: {
						email: input.val()
					},
					success: function(data) {
						success = data.found !== false; // Sono riuscito
						id = data.id;
					},
					async:false // Chiamata NOT asincrona, l'eseguzione di questa richiesta non crea thread
				});
			}

			// Se ho fatto successo associo la riga allo ID usato nella base di dati
			if(success)
				stu_assoc.assoc.set(stu_assoc.names[stu_assoc.index], {
					id: id,
					mail: input.val()
				});

			// Devo mostrare messaggio d'errore ovvero di successo??????
			input.toggleClass("is-danger",!success);
			$("#data_stu_mail_fail_helper").toggle(!success);
			$("#data_stu_next").prop("disabled", !success);
			input.toggleClass("is-success", success);

			// Posso riabilitarmi
			$(_self).prop("disabled", false);
		});
});

/**
 * Questo evento viene eseguito alla pressione del tasto "Continua" nel dialogo di associaizone studenti. Va proseguire
 * , se ancora presenti, allo studente successivo da associare altrimenti mostra il dialogo di associazione azienda.
 */
$("#data_stu_next").on("click", function ()
{
	// Controllo se lo studente ha stato associazto
	if(!stu_assoc.assoc.has(stu_assoc.names[stu_assoc.index]))
		return;

	let assoc_box = $("#data_assoc_stu");
	let input = $("#data_stu_mail");

	assoc_box.hide();

	// Resetto gli stati
	input.toggleClass("is-danger", false);
	$("#data_stu_mail_fail_helper").toggle(false);
	$("#data_stu_next").prop("disabled", false);
	input.toggleClass("is-success", false);

	stu_assoc.index++;

	// Ci sono ancora studenti? allora proseguo
	if(stu_assoc.index < stu_assoc.names.length)
	{
		stu_assoc.refresh ();

		assoc_box.show ();
	}
	else
	{
		// Altrimento nascondo e vado all'altro dialogo
		$("#data_assoc_stu").hide();

		factory_assoc.refresh();

		$("#data_assoc_fact").show();

		// La logica continua su valida_fact.js ! Ancora voglia di leggere?
		$("#data_assoc_stu").hide();
		$("#data_assoc_stu").hide();
		$("#data_assoc_stu").hide();

	}
});