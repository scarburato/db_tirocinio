let param = $.urlParam.get("page");

let contatti = new DynamicPagination($("#dynamic_contacts"), "list.php");
contatti.setLoading($("#dynamic_contacts_loading"));
//contatti.goto(Number.isInteger(param) ? param : 0);
contatti.goto(0);
contatti.setOnChange((p, pv) =>
{
	$.urlParam.set("page", pv);
});