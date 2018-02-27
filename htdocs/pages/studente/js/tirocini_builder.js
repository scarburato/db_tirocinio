$.fn.isOnScreen = function()
{
	let element = this.get(0);
	let bounds = element.getBoundingClientRect();
	return bounds.top < window.innerHeight && bounds.bottom > 0;
};

let index = 0;
let semaforo = false;
let selezione = 1;
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
				chTrain: selezione,
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

$('.switch').on('click', function() {
	// TODO implementare attesa del semaforo libero
	if ($(this).hasClass("is-active"))
		return;
	if (semaforo)
		return;
	semaforo=true;
	selezione=$(this).data("selezione");
	$("#loading_go_on").show();
	$("#loading_stop").hide();
	$("#tirocinis").html("");
	$('.switch').removeClass("is-active");
	$(this).addClass("is-active");
	index = 0;
	semaforo=false;
});
