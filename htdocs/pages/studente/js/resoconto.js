let textarea = $("#resoconto")[0];
sceditor.create(textarea, {
	width: "100%",
	style: (BASE+'css/default.min.css'),
	toolbarExclude: "emoticon,youtube,maximize,date,time,print,ltr,rtl",
	fonts: "Ubuntu, Ubuntuo Mono, Ubuntu Condensed, Arial,Arial Black,Comic Sans MS,Courier New,Georgia,Impact,Sans-serif,Serif,Times New Roman,Trebuchet MS,Verdana"
});
let editor = sceditor.instance(textarea);

let x = new ToggleTab($("#selector"), $("#contents"));
x.onChange(function (e)
{
	if(e === "preview")
	{
		console.log("ciao" + editor.val());
		$("#preview_editor").html(
			editor.val()
		)
	}
});