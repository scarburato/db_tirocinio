$.fn.isOnScreen = function()
{
	let element = this.get(0);
	let bounds = element.getBoundingClientRect();
	return bounds.top < window.innerHeight && bounds.bottom > 0;
};

window.setInterval(function ()
{
	// TODO Controllo se arrivato alla fine

	if($("#loading_go_on").isOnScreen())
	{
		// TODO Post 'n' write
	}
},400);