/*
import del csv
 */

let current_file;
let current_result = [];
let head;

// Funzione che cambia il file quando necessario
$("#csv_up").on("change", function ()
{
	current_file = this.files[0];
	$("#csv_name").text(current_file.name);

	$("#setting").toggle(current_file !== undefined);
	$("#csv_start").prop("disabled", current_file === undefined);
});

// Evento da esegure premendo il tast d'avvio dell'importazione
$("#csv_start").on("click", function ()
{
	// Il documento è seleziuonato ?
	if(current_file === undefined)
		return;

	// Se sì, allora nascondi tutta la roba e mostra il dialogo di impoazio
	$(".set_div").hide();
	$("#out").show();
	$("#csv_correct").hide();
	$("#csv_halt_field").show();

	// Metto la barra di progresso alla dimensione del documento
	$("#csv_progress").prop("max", current_file.size).prop("value", 0);

	// rif jQuert
	let thead = $("#csv_head");
	let tbody = $("#csv_body");
	let stop  = $("#csv_halt");
	let progress = $("#csv_progress");
	let rows = $("#csv_rows_counter");

	// Handler per la gestione del suicidio
	let kill_me = false;
	let stop_fn = () =>
	{
		kill_me = true;
		$("#csv_correct_false").click();
	};

	stop.prop("disabled", false);
	stop.on("click", stop_fn);

	// Mostro solo le prime 30 righe, questo è necessario per non occupare troppe risolte. Infatti craare oggetti DOM al
	// volo è un'operazione molto costosa
	let righe = 30;

	// Risulato di PapaParse, attenzione siccome è stato usata la modalità "streaming" nella variabile locale res non ci
	// sarà alcun risulato!
	let res = Papa.parse( current_file, {
		delimiter: $("#csv_col").val(),
		quoteChar: $("#csv_quote").val(),
		header: $("#csv_header").is(':checked'),
		worker: $("#csv_multithread").is(':checked'),
		step: (riga, parser) =>
		{
			// Mi è stato chiesto di morire?
			if(kill_me)
			{
				// morto.
				parser.abort ();
				return;
			}

			// Aggiorno la barra del progresso
			progress.prop("value", riga.meta.cursor);

			// Se la testata non è definita me la ricavo!
			if(head === undefined)
			{
				head = riga.meta.fields;
				// se non sono disponibili i nome delle colonne dal CSV allora genero nomi a caso
				if(head === undefined)
				{
					head = [];
					for(let i = 0; i < riga.data[0].length; i++)
						head.push("Colonna " + i);
				}

				// Scrivo creo le celle html
				head.forEach((col) =>
				{
					$("<th/>",{
						text: col
					}).appendTo(thead);
				});
			}

			// Ai risulati salvati aggiungo la linea appena letta, sempre perché in modalità streaming non è possibile
			// ricavare alla fine tutti i risulati
			current_result.push(riga.data[0]);

			// Uso html() anziché text() perché molto più veloce e perché length di Array può essere solo Number ovvero
			// undefined sicché non c'è pericolo di corrompore lo HTML
			rows.html(current_result.length);

			// Se ho raggiunto il limite massimo dell'antemprima me ne esco.
			if(righe <= 0)
				return;
			else
				righe--;

			/* Arrivato qua creo una nuova linea per la tabella e per ogni elemento che ho letto ne creo una cella,
			 * FIXME i nomi delle colonne generati non esistono veramente, prevedere una runtime alternativa! */
			let row = $("<tr/>");
			head.forEach((col) =>
			{
				let jCol = $("<td/>", {
					text: riga.data[0][col]
				});
				jCol.appendTo(row);
			});
			// Scrivo la riga generata nel DOM
			row.appendTo(tbody);
		},

		//  When streaming, parse results are not available in this callback.
		complete: () =>
		{
			// Terminato l'import scolleggo l'handler del suicidio dal pulsante d'arresto
			stop.off("click", stop_fn);

			// Nascondo il pulsante di arresesto e mostro il dialogo se il documento importato è corretto
			$("#csv_halt").prop("disabled", true);
			$("#csv_halt_field").hide();
			$("#csv_correct").show();

			// La logica adesso continua in asocia.js .
		}
	});
});