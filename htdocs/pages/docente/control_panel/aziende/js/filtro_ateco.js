$("#ateco_filtro").submit(function () {
    let filtro = $(this).find("input[name='query']").val();

    $("#ateco_tbody").children().each(function ()
    {
        let valido = false;
        $(this).children().each(function ()
		{
		    if(!valido && $(this).html().search(filtro) !== -1)
		        valido = true;
		});

		if(valido)
			$(this).show();
		else
			$(this).hide();
    });

    return false;
});

let ateco_tbody = $("#ateco_tbody");
ateco_tbody.on("click", "tr", seleziona);
ateco_tbody.on("keyup", "tr", seleziona);

function seleziona(key)
{
	if(key !== undefined)
	{
		let code = key.which;
		if (code !== 1 && code !== 32 && code !== 13 && code !== 188 && code !== 186)
			return;
	}

	let selected = $ ("#ateco_tbody").find ("tr[class='is-selected']");
	selected.removeClass ("is-selected");
	if(this !== selected[0])
		$ (this).addClass ("is-selected");
}

listener_ateco = new TogglePanel("#seleziona_ateco");

$("#seleziona_ateco_trigger").on("click", function ()
{
	listener_ateco.show()
});

$("#seleziona_ateco_scarta").on("click", function ()
{
	listener_ateco.hide();
});

$("#seleziona_ateco_aggiungi").on("click",function ()
{
	let main_form = $("#main_form");
	let ateco_selected = $("#ateco_tbody").find("tr.is-selected").find("td.codice_ateco_value");
	main_form.find("input[name='ateco_unique']").val(ateco_selected.html());
	console.log(ateco_selected.data("dbid"));
	main_form.find("input[name='ateco']").val(ateco_selected.data("dbid"));

	listener_ateco.hide();
});