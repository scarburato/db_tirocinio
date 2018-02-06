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