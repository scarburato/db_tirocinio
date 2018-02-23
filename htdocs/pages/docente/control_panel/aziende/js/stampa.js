window.onbeforeunload = function() {
	return "Data will be lost if you leave the page, are you sure?";
};

$("#stampare_sedi").change(function ()
{
	if(this.checked)
		$(".gao").show();
	else
		$(".gao").hide();
});

/**
 * Spuduratamente copiato da
 * @author https://stackoverflow.com/a/33735423
 */
$("#stampalo").on("click",function ()
{
	$("#print_area").printElement({
		overrideElementCSS:[
			'/css/bulma.min.css'
			]
	});
});