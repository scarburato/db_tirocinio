let textarea = $ ("#resoconto");
if (textarea[0] !== undefined)
{
	sceditor.create (textarea[0], {
		width: "100%",
		style: (BASE + 'css/default.min.css'),
		toolbarExclude: "emoticon,youtube,maximize,date,time,print,ltr,rtl",
		fonts: "Ubuntu, Ubuntuo Mono, Ubuntu Condensed, Arial,Arial Black,Comic Sans MS,Courier New,Georgia,Impact,Sans-serif,Serif,Times New Roman,Trebuchet MS,Verdana"
	});
	let editor = sceditor.instance (textarea[0]);
	if (textarea.is ("[readonly]"))
		editor.readOnly (true);
}

let x = new ToggleTab ($ ("#selector"), $ ("#contents"), PASSED);

x.onChange (function (e)
{
	console.log ("hello there, I'm " + e);
	switch (e)
	{
		case "preview":
			$ ("#preview_editor").html (
				editor.val ()
			);
			break;
		case "editor":
			break;
		case "comments":
			break;
		case "info":
			break;
	}
});

$ ("#bt_save").on ("click", function ()
{
	/* TODO implementare funzione di post per aggiungere tirocini
	$.post("addResoconto.php") {

	}*/
});
