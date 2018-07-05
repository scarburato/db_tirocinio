let permessi_disponibili = new TableSelection($("#privilegi"));
let permessi_attuali = new TableSelection($("#applicati"));
let permessi = new TableChooser(permessi_attuali, permessi_disponibili);
let semaforo = true;
let current_user;

permessi.addButtonMoveToA($("#aggiungi"));
permessi.addButtonMoveToB($("#rimuovi"));

$("#search").on("click", ricerca);
$("#query").on("keyup", function (e)
{
	if(e.keyCode === 13)
		ricerca();
});

function ricerca()
{
	$("#info").hide();
	$("#error").hide();
	$("#no_output").hide();
	$("#setting").hide();

	$.get(
		BASE + "rest/users/get/docente.php",
		{
			email: $("#query").val()
		}
	)
		.done(function (data)
		{
			// Errore 404, utente non trovato, messaggio sad
			if(data.error === 404)
			{
				$("#no_output").show();
				return;
			}

			// Un errore sconsocuitrutgtrg
			if(data.error !== undefined)
			{
				$("#error_what").text(data["what"]);
				$("#error").show();
				return;
			}

			// Salvo l'id dell'utente corrente
			current_user = data.id;

			let tbody_applicati = $("#applicati");
			let tbody_disponibili = $("#privilegi");

			// Riporto la roba a destra!
			tbody_applicati.children().each(function (index)
			{
				$(this).removeClass("is-selected");
				tbody_disponibili.append($(this));
			});

			tbody_disponibili.children().removeClass("is-selected");

			// Sposto i gruppi conosciuti a sinistra!
			data.gruppi.forEach(function (e)
			{
				tbody_applicati.append(tbody_disponibili.find("tr[data-id='" + e.nome +"']"));
			});


			$("#setting").show();
		})

}

$("#commit").on("click", function ()
{
	if(!confirm("Questo sovrascriverà le impostazioni. Continuare?"))
		return;

	let groups = [];

	$("#applicati").children().each(function (index)
	{
		groups.push($(this).data("id"));
	});
	console.log(groups);

	$.post(
		BASE + "rest/users/set/group.php",
		{
			id: current_user,
			groups: (groups.length > 0 ? groups : 0)
		}
	)
		.done(function (data)
		{
			console.log(data);
		})
});

$("#error").hide();
$("#no_output").hide();
$("#setting").hide();

if($("#query").val() !== "")
	ricerca();