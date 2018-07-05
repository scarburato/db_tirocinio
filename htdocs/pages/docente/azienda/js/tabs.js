let param = $.urlParam.get("tab");

let x = new ToggleTab ($ ("#selector"), $ ("#contents"), param === undefined ? "indirizzi" : param);

x.onChange(function (tab)
{
	$.urlParam.set("tab", tab);
});