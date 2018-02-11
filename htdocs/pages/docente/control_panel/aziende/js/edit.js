$(".edit-button").on("click", function ()
{
	console.log($(this).data("edit"));

	$.post(
		"./rest/edit.php",
		{
			method: "edit",
			column: "IVA",
			value:  "MEGADUCE",
			id: 1
		}
	).done(function (data)
	{
		console.log(data);
	})

});