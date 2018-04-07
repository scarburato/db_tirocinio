/**
 *
 * @param page Number
 */
let page_nav = {
	loading: $("#dynamic_comments_loading"),
	comments: $("#dynamic_comments"),
	current_page: undefined,

	/**
	 *
	 * @param page Number
	 * @param force_reolad Boolean
	 */
	goto: function(page, force_reolad)
	{
		if(page === this.current_page && !force_reolad)
			return;

		this.loading.show();
		this.comments.hide();

		$.get
		(
			"comments.php",
			{
				tirocinio: TIR,
				pagina: page
			}
		)
			.done( (data) =>
			{
				this.comments.html(data);
				this.comments.show();
				this.loading.hide();

				this.current_page = this.comments.find(".ajax_comment").data("current-page");
			});
	}
};

page_nav.goto(0);

// In ascolto per gli eventi sui pulsanti dei commenti
page_nav.comments.on("click", ".js-page-nav", function ()
{
	page_nav.goto($(this).data("page"));
});

page_nav.comments.on("keyup", ".js-page-nav", function (e)
{
	if(e.which === 13)
		$(this).click();
});

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
		BASE + 'rest/trainings/commenta.php', {contenuto: comment, tirocinio: TIR}
	)
		.always(function (data)
		{
			page_nav.goto(0, true);
		});
});

$ ("#bt_comments_reload").on("click", function ()
{
	page_nav.goto(page_nav.current_page, true);
});

// TODO Eliminazione commenti