let x = new ToggleTab ($ ("#selector"), $ ("#contents"), PASSED);

x.onChange(function (tab)
{
	$.urlParam.set("page", tab);
});

let parser = new sceditor.BBCodeParser();
let p = $("#preview_editor");
let text = p.html();

p.html(
	parser.toHTML(text)
);

text = undefined;