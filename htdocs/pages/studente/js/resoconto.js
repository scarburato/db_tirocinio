// TODO??? aggiungere semaforo

let textarea = $ ("#resoconto");
let editor;
if (textarea[0] !== undefined)
{
	sceditor.create (textarea[0], {
		width: "100%",
		style: (BASE + 'css/default.min.css'),
		toolbarExclude: "emoticon,youtube,maximize,date,time,print,ltr,rtl",
		fonts: "Ubuntu, Ubuntuo Mono, Ubuntu Condensed, Arial,Arial Black,Comic Sans MS,Courier New,Georgia,Impact,Sans-serif,Serif,Times New Roman,Trebuchet MS,Verdana"
	});
	editor = sceditor.instance (textarea[0]);
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
		case "editor": // niente?
			break;
		case "comments": // TODO get dei commenti, ed editor di testo?
			break;
		case "info": // niente?
			break;
	}
});

$ ("#bt_save").on ("click", function ()
{
	let temp = $("#preview_editor").html();
	if ($.md5(temp)!=md5_ATT) {
		if (confirm("Continuando modificherà la descrizione e non potrà risalire al suo precedente valore!")) {
			$.post(
				BASE+'rest/trainings/update_Descrizione.php', {contenuto: temp, tirocinio: TIR}
			).done( function(data) {
				md5_ATT=data.md5;
			});
		}
	}
}
);

let nonPigiareTroppo = false;
// TODO setInterval per resettare il semaforo ogni qualche-secondo.

$("#bt_comments").on("click", function () {
	if (nonPigiareTroppo)
		return;
	nonPigiareTroppo = true;
	console.log("clicked");
	let comment = $("#commento").val();
	console.log(comment);
	if (comment=="" || comment==undefined || comment==null)
		return;
	console.log("would send, this: "+comment);
	$.post(
		BASE+'rest/trainings/commenta.php', {contenuto: comment, tirocinio: TIR}
	)/*.done(function (data) {
		console
		console.log(data);
	}).fail(function (data) {
		console.log("mistake!!!");
	}).always(function(){
		console.log("at least it's something...");
	});*/
});
