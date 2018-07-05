let filtri = {
	indirizzo:	undefined,
	filtro:		undefined
};

let param = $.urlParam.get("page");

let aziende = new DynamicPagination($("#dynamic_factories"), "list.php", filtri);
aziende.setLoading($("#dynamic_factories_loading"));
aziende.goto(Number.isInteger(param) ? param : 0);
aziende.setOnChange((p, pv) =>
{
	$.urlParam.set("page", pv);
});

// Filtri
$("#filtralo").on("click", () =>
{
	let filtro = $("#filter_text").val();
	filtri.filtro = filtro === "" ? undefined : filtro;

	let indirizzo = $("#filter_values_ind").val();
	filtri.indirizzo = indirizzo === "all" ? undefined : indirizzo;

	aziende.goto(0, true);
});