// TODO??? aggiungere semaforo

let textarea = $ ("#resoconto");
let area = $("#preview_editor");
let editor;
if (textarea[0] !== undefined)
{
	sceditor.create (textarea[0], {
		format: 'bbcode',
		width: "100%",
		style: (BASE + 'css/default.min.css'),
		emoticonsRoot: (BASE + 'js/editor/'),
		toolbarExclude: "emoticon,youtube,maximize,date,time,print,ltr,rtl,image",
		fonts: "Ubuntu, Ubuntuo Mono, Ubuntu Condensed, Arial,Arial Black,Comic Sans MS,Courier New,Georgia,Impact,Sans-serif,Serif,Times New Roman,Trebuchet MS,Verdana"
	});
	editor = sceditor.instance (textarea[0]);
	if (textarea.is ("[readonly]"))
		editor.readOnly (true);
}
else if(area[0] !== undefined)
{
	let parser = new sceditor.BBCodeParser ();
	let text = area.html();

	area.html(parser.toHTML(text));
}

let x = new ToggleTab ($ ("#selector"), $ ("#contents"), PASSED);
$("#weknow").hide();

x.onChange (function (e)
{
	$.urlParam.set("page", e);

	if (e === "preview" && editor !== undefined)
	{
		area.html (
			editor.fromBBCode(editor.val(), true)
		);

		if(/<[a-z][\s\S]*>/i.test(editor.val()))
			$("#weknow").show();
		else
			$("#weknow").hide();
	}

	/*if (e === "comments" && PASSED !== "comments")
	{
		//window.location.href = window.location.href + "&page=comments";
		//newMSG = false;
	}*/
});

// Bottoni
// Bottone per salvare modifiche alla descrizione
$ ("#bt_save").on ("click", function ()
	{
		let temp = editor.val();
		if ($.md5 (temp) !== md5_ATT)
		{
			if (confirm ("Continuando modificherà la descrizione e non potrà risalire al suo precedente valore!"))
			{
				$.post (
					BASE + 'rest/trainings/update_Descrizione.php', {contenuto: temp, tirocinio: TIR}
				).done (function (data)
				{
					// FIXME md5 invalido perché convertito lato server
					md5_ATT = data.md5;

					let last_edit = $("#last_edit");
					last_edit.html(data.last_edit);
					last_edit.prop("datetime", data.last_edit);
				});
			}
		}
	}
);

// Bottone per la stampa
$ ("#print").on("click", function ()
{
	area.printElement();
});