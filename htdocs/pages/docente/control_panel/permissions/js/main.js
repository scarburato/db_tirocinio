let permessi_disponibili = new TableSelection($("#privilegi"));
let permessi_attuali = new TableSelection($("#applicati"));
let semaforo = true;
let current_user;

$("#aggiungi").on("click", function ()
{
	let attuale = permessi_disponibili.getSelectedRow();

	if(attuale === null)
		return;

	attuale.removeClass("is-selected");
	$("#applicati").append(attuale);
});

$("#rimuovi").on("click", function ()
{
	// variabili
	let attuale = permessi_attuali.getSelectedRow();
	let id;

	if(attuale === null)
		return;

	attuale.remove("is-selected");
	$("#privilegi").append(attuale);
});

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
			if(data.error === 404)
			{
				$("#no_output").show();
				return;
			}

			if(data.error !== undefined)
			{
				$("#error_what").html(data["what"]);
				$("#error").show();
				return;
			}
			current_user = data.id;

			let tbody_applicati = $("#applicati");
			let tbody_disponibili = $("#privilegi");

			tbody_applicati.children().each(function (index)
			{
				$(this).removeClass("is-selected");
				tbody_disponibili.append($(this));
			});

			tbody_disponibili.children().removeClass("is-selected");

			data.permessi.forEach(function (e)
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

	let permissions = [];

	$("#applicati").children().each(function (index)
	{
		permissions.push($(this).data("id"));
	});
	console.log(permissions);

	$.post(
		BASE + "rest/users/set/permission.php",
		{
			id: current_user,
			permissions: permissions
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