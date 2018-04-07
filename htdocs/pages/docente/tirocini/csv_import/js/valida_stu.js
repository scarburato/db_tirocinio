/*
File che valida gli indirizzi di posta elettronica e le aziende!
 */
$("#data_stu_end").hide();
$("#data_stu_mail_fail_helper").hide();
$("#data_stu_next").prop("disabled", true);

$("#data_stu_mail_validate").on("click", function ()
{
	$(this).prop("disabled", true);

	let input = $("#data_stu_mail");
	let _self = this;
	const gimport = $("#data_stu_gimport").is(':checked');

	$.get
	(
		BASE + "rest/users/get/studente.php",
		{
			email: input.val()
		}
	)
		.done(function (data)
		{
			let success = data.error === undefined;
			let id = data.id;

			// Provo qua ad importare dal dominio!
			if(!success && gimport)
			{
				jQuery.ajax({
					url: BASE + "rest/domain/users/insert.php",
					type: "get",
					data: {
						email: input.val()
					},
					success: function(data) {
						success = data.found !== false;
						id = data.id;
					},
					async:false
				});
			}

			if(success)
				stu_assoc.assoc.set(stu_assoc.names[stu_assoc.index], {
					id: id,
					mail: input.val()
				});

			input.toggleClass("is-danger",!success);
			$("#data_stu_mail_fail_helper").toggle(!success);
			$("#data_stu_next").prop("disabled", !success);
			input.toggleClass("is-success", success);

			$(_self).prop("disabled", false);
		});
});

$("#data_stu_next").on("click", function ()
{
	if(!stu_assoc.assoc.has(stu_assoc.names[stu_assoc.index]))
		return;

	let assoc_box = $("#data_assoc_stu");
	let input = $("#data_stu_mail");

	assoc_box.hide();

	input.toggleClass("is-danger", false);
	$("#data_stu_mail_fail_helper").toggle(false);
	$("#data_stu_next").prop("disabled", false);
	input.toggleClass("is-success", false);

	if(stu_assoc.index < stu_assoc.names.length)
	{
		stu_assoc.index++;
		stu_assoc.refresh ();

		assoc_box.show ();
	}
	else
	{
		$("#data_assoc_stu").hide();

		factory_assoc.refresh();

		$("#data_assoc_fact").show();
	}
});