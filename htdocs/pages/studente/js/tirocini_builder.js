$.fn.isOnScreen = function()
{
	let element = this.get(0);
	let bounds = element.getBoundingClientRect();
	return bounds.top < window.innerHeight && bounds.bottom > 0;
};

index = 0;
semaforo = false;
window.setInterval(function ()
{
	if(semaforo)
		return;

	// Notare che quando il div diventerà HIDDEN la condizione sarà sempre false!
	if($("#loading_go_on").isOnScreen())
	{
		semaforo = true;
		$.get(
			"tirocinio.php",
			{
				index: index++,
			}
		).done(function (data)
		{
			if(data.length === 0)
			{
				$("#loading_go_on").hide();
				$("#loading_stop").show();
			}
			else
				$("#tirocinis").append(data);
		}).always(function ()
		{
			semaforo = false;
		});
	}
},250);