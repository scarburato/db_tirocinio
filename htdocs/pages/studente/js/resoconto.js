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

// TODO implementare visualizzazione dinamica di commenti
x.onChange (function (e)
{
	if (e=="preview" && editor!==undefined) {
		$ ("#preview_editor").html (
			editor.val ()
		);
	}
	if (e=="comments" && PASSED!="comments") {
		window.location.href = window.location.href+"&page=comments";
		//newMSG = false;
	}
});

// Bottoni
// Bottone per salvare modifiche alla descrizione
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

// Per evitare lo spam di commenti
let nonPigiareTroppo=false;
//let newMSG = false;
window.setInterval(function() {
	nonPigiareTroppo=false;
}, 1000);

$("#bt_comments").on("click", function () {
	if (nonPigiareTroppo)
		alert ("aspetta un secondo prima di inviare un nuovo commento!");
	nonPigiareTroppo = true;
	let comment = $("#commento").val();
	if (comment=="" || comment==undefined || comment==null)
		return;
	$.post(
		BASE+'rest/trainings/commenta.php', {contenuto: comment, tirocinio: TIR}
	).done(function () {
		window.location.reload();
		//newMSG = false;
	})/*.fail(function () {
		console.log("errore per la post!?!");
	}).always(function(data){
		console.log("at least it's something...");
		console.log(data);
	});*/
});

/* TODO implementare questo intervallo o prendere ed implementare i pesanti WEB SOCKET
let reloadingComments = window.setInterval(function () {
	if (($("#commento").val())=="" || comment==undefined || comment==null) {
		window.location.reload();
		newMSG = false;
	} else
		newMSG = true;
}, 30000)
*/
