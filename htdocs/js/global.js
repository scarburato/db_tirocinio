$.ajaxSetup
({
	complete: function (event, xhr, options)
	{
		if (event.getResponseHeader('google_expired'))
		{
			window.location.href = BASE + "index.php";
			return;
		}

		if(event.getResponseHeader('exception'))
		{
			console.error(
				"Error ",
				event.getResponseHeader('exception'));
		}
	}
});