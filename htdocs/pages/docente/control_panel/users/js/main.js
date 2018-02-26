$("#error").hide();
$("#no_output").hide();
$("#output").hide();

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
	$("#output").hide();

	$.get (
		BASE + "rest/domain/users/get.php",
		{
			email: $ ("#query").val ()
		}
	)
		.done (function (data)
		{
			if(data.error !== null)
			{
				$("#error_what").html(data["what"]);
				$("#error").show();
			}
			else if(!data.found)
			{
				$("#no_output").show();
			}
			else if(data.found)
			{
				$("#output_img").html("<img src=\"" + data.thumbnailPhotoUrl + "\">");
				$("#output_nominative").html(data.name.fullName);
				let mail = $("#output_email");
				mail.html(data.email);
				mail.attr("href", "mailto:" + data.email);
				$("#output_orgunit").html(data.orgUnitPath);

				$("#add_user").prop("disabled", !data.no_db);

				if(!data.no_db)
					$("#user_exists").show();
				else
					$("#user_exists").hide();

				$("#output").show();
			}
		})
}