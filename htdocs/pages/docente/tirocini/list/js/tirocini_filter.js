let opt_list = $("#filter_values");
let docente = opt_list.val();

$("#filter_go").on("click", function ()
{
	if(semaforo)
		return;

	semaforo = true;

	docente = opt_list.val();
	index = 0;

	$ ("#tirocinis").html("");
	$ ("#loading_stop").hide ();
	$ ("#loading_go_on").show ();

	semaforo = false;
});

opt_list.change(function ()
{
	$("#filter_go").click()
});