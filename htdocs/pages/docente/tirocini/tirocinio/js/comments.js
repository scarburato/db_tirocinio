let page_nav = new DynamicPagination($("#dynamic_comments"), "comments.php", {
	tirocinio: TIR
});

page_nav.setLoading($("#dynamic_comments_loading"));
page_nav.goto(0);

// Per evitare lo spam di commenti
let nonPigiareTroppo = false;
//let newMSG = false;
window.setInterval (function ()
{
	nonPigiareTroppo = false;
}, 1000);

$ ("#bt_comments").on ("click", function ()
{
	if (nonPigiareTroppo)
		alert ("aspetta un secondo prima di inviare un nuovo commento!");

	nonPigiareTroppo = true;
	let jcomment = $("#commento");
	let comment = jcomment.val ();
	if (comment === "" || comment === undefined || comment === null)
		return;

	jcomment.val ("");
	$.post (
		BASE + 'rest/trainings/comments/post.php',
		{
			contenuto: comment,
			tirocinio: TIR
		}
	)
		.always(function (data)
		{
			page_nav.refresh();
		});
});

$ ("#bt_comments_reload").on("click", function ()
{
	page_nav.refresh();
});

// Eliminazione commenti
$("#dynamic_comments").on("click", ".delete-comment", function ()
{
	let id = $(this).data("dbid");

	if(!confirm("Eliminare per davvero il commento? Non sarà possibile recuperarlo in nessun modo!"))
		return;

	$.get (
		BASE + 'rest/trainings/comments/delete.php',
		{
			comment: id
		}
	)
		.done(function (data)
		{
			if(data.rows > 0)
				page_nav.refresh();
		});
});