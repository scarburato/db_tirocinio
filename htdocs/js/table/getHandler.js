/**
 * Classe che permette la gestione dinamica delle tabelle
 * Ad esempio sfogliare le utenze nella selezione dello studente, docente o azienda nella
 * creazione del tirocinio.
 */
class GetHandler
{
	/**
	 *
	 * @param table_body jQuery
	 * @param resource string
	 * @param on_get function(Object datum, jQuery table)
	 */
	constructor(table_body, resource, on_get)
	{
		let self = this;

		this.current_page = 0;
		this.next_page = 0;
		this.prev_page = null;
		this.query = "";

		this.tbody = table_body;
		this.remote = resource;
		this.on_get = on_get;

		this.semaphore = false;

		this.buttons = {
			backward: {
				elements: [],
				handler: self.backward
			},
			forward: {
				elements: [],
				handler: self.forward
			},
			reload: {
				elements: [],
				handler: self.get
			}
		}
	}

	/**
	 *
	 * @param query String
	 */
	setQuery(query)
	{
		this.query = query;

		this.current_page = 0;
		this.next_page = 0;
		this.prev_page = null;

		this.get();
	}

	/**
	 *
	 * @param type String
	 * @param button jQuery
	 */
	addButton(type, button)
	{
		this.buttons[type].elements.push(button);
		button.on("click", this.buttons[type].handler.bind(this))
	}

	get()
	{
		if(this.semaphore)
			return;

		this.semaphore = true;

		Object.keys(this.buttons).forEach((key) =>
		{
			this.buttons[key].elements.forEach(function (button)
			{
				button.addClass("is-loading");
			})
		});

		$.get
		(
			this.remote,
			{
				page: this.current_page,
				query: this.query
			}
		)
			.done((data) =>
			{
				let res = data;

				if(res.data_rows <= 0)
				{
					this.buttons.forward.elements.forEach(function (e)
					{
						e.prop("disabled", true)
					});
					return;
				}

				this.tbody.html ("");
				res.data.forEach ((datum) =>
				{
					this.on_get(datum, this.tbody);
				});


				this.current_page = res.current_page;
				this.next_page = res.next_page;
				this.prev_page = res.previus_page;

				let comp = this.next_page === null;
				this.buttons.forward.elements.forEach(function (e)
				{
					e.prop ("disabled", comp);
				});

				comp = this.prev_page === null;
				this.buttons.backward.elements.forEach(function (e)
				{
					e.prop ("disabled", comp);
				});

				this.buttons.reload.elements.forEach(function (e)
				{
					e.prop("disabled", false);
				});
		})
			.always(() =>
			{
				Object.keys(this.buttons).forEach((key) =>
				{
					this.buttons[key].elements.forEach(function (button)
					{
						button.removeClass("is-loading");
					})
				});

				this.semaphore = false;
			});
	}

	backward(e)
	{
		if(this.next_page !== null)
			this.current_page = this.prev_page;

		this.get();
	}

	forward(e)
	{
		if(this.next_page !== null)
			this.current_page = this.next_page;

		this.get();
	}
}