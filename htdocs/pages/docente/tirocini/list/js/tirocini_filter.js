let opt_list = $("#filter_values");
if($.urlParam.get("docente") !== undefined)
	opt_list.val($.urlParam.get("docente"));
let docente = opt_list.val();

$("#filter_go").on("click", function ()
{
	if(semaforo)
		return;

	semaforo = true;

	docente = opt_list.val();
	$.urlParam.set("docente", opt_list.val());
	index = 0;

	$ ("#tirocinis").html("");
	$ ("#loading_stop").hide ();
	$ ("#loading_go_on").show ();

	semaforo = false;
});

let opt_list_stu = $("#filter_values_stu");
if($.urlParam.get("studente") !== undefined)
	opt_list_stu.val($.urlParam.get("studente"));
let studente = opt_list_stu.val();

$("#filter_go_stu").on("click", function ()
{
	if(semaforo)
		return;

	semaforo = true;

	studente = opt_list_stu.val();
	$.urlParam.set("studente", opt_list_stu.val());
	index = 0;

	$ ("#tirocinis").html("");
	$ ("#loading_stop").hide ();
	$ ("#loading_go_on").show ();

	semaforo = false;
});

opt_list.change(function ()
{
	$("#filter_go").click();
});

opt_list_stu.change(function ()
{
	$("#filter_go_stu").click();
});