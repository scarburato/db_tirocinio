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

$("#csv_start").on("click", function ()
{
	if(current_file === undefined)
		return;

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

	// Simulo il rinnego @see associa
	let kill_me = false;
	let stop_fn = () =>
	{
		kill_me = true;
		$("#csv_correct_false").click();
	};

	stop.prop("disabled", false);
	stop.on("click", stop_fn);

	// Mostro solo le prime 30 righe
	let righe = 30;

	let res = Papa.parse( current_file, {
		delimiter: $("#csv_col").val(),
		quoteChar: $("#csv_quote").val(),
		header: $("#csv_header").is(':checked'),
		worker: $("#csv_multithread").is(':checked'),
		step: (riga, parser) =>
		{
			if(kill_me)
			{
				parser.abort ();
				return;
			}

			progress.prop("value", riga.meta.cursor);

			if(head === undefined)
			{
				head = riga.meta.fields;
				if(head === undefined)
				{
					head = [];
					for(let i = 0; i < riga.data[0].length; i++)
						head.push("Colonna " + i);
				}

				head.forEach((col) =>
				{
					$("<th/>",{
						text: col
					}).appendTo(thead);
				});
			}

			current_result.push(riga.data[0]);

			rows.html(current_result.length);

			if(righe <= 0)
				return;

			righe--;

			let row = $("<tr/>");
			head.forEach((col) =>
			{
				let jCol = $("<td/>", {
					text: riga.data[0][col]
				});
				jCol.appendTo(row);
			});
			row.appendTo(tbody);
		},

		//  When streaming, parse results are not available in this callback.
		complete: () =>
		{
			stop.off("click", stop_fn);
			$("#csv_halt").prop("disabled", true);
			$("#csv_halt_field").hide();
			$("#csv_correct").show();
			console.log("sono uqa");
		}
	});
});