/**
 * Classe che permette la gestione dinamica delle tabelle.
 * Ad esempio sfogliare le utenze nella selezione dello studente, docente o azienda nella
 * creazione del tirocinio.
 *
 * Funziona solo con le API LIST !
 */
class GetHandler
{
	/**
	 *
	 * @param table_body jQuery
	 * @param resource string
	 * @param on_get function(Object datum, jQuery table, Array fields)
	 * @param table_head jQuery | undefined
	 * @param head_handler function(Array fields, jQuery thead)
	 */
	constructor(table_body, resource, on_get, table_head, head_handler)
	{
		if(!(table_body instanceof jQuery))
			throw "table_body must be jQuery";

		if(table_head !== undefined && !(table_head instanceof jQuery))
			throw "table_body must be jQuery";

		let self = this;

		this.current_page = 0;
		this.next_page = 0;
		this.prev_page = null;
		this.query = "";
		this.params = {};

		this.tbody = table_body;
		this.thead = table_head;

		this.remote = resource;

		this.on_get = on_get;
		this.on_head= head_handler;

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
	 * Funzione che imposta la query di filtro attuale
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

	/**
	 * Imposta parametri aggiunti alla chiamata GET
	 * @param params
	 */
	setParams(params)
	{
		if(!(params instanceof Object))
			throw("Params must e Object!");

		this.params = params;
	}

	/**
	 * Funzione che effettua la chiamata GET ed aggiorna tutti i pulsanti
	 */
	get()
	{
		// Qualcuno sta provando a farlo sullo stesso oggetto, esco.
		if(this.semaphore)
			return;

		// Aquisisco protezione
		this.semaphore = true;

		// Per ogni pulsante memorizzato aggiungo la classe is-loading
		Object.keys(this.buttons).forEach((key) =>
		{
			this.buttons[key].elements.forEach(function (button)
			{
				button.addClass("is-loading");
			})
		});

		// Faccio la GET
		$.get
		(
			this.remote,
			this.bulidArgs({
				page: this.current_page,
				query: this.query
			})
		)
			.done((data) =>
			{
				let res = data;

				// Se il contatore è vuoto, non c'è motivo di mostrare come disponible il pulsante per proseguire
				if(res.data_rows <= 0)
				{
					this.buttons.forward.elements.forEach(function (e)
					{
						e.prop("disabled", true)
					});
				}

				// Aggiorno la tabella
				if(this.on_head !== undefined)
					this.on_head(res.data_fields, this.thead);

				// Svuoto il contenuto attuale
				this.tbody.html ("");

				// Per ogni riga eseguo la funzione on_get!
				res.data.forEach ((datum) =>
				{
					this.on_get(datum, this.tbody, res.data_fields);
				});

				// Sincronizzo gli indici con quelli del servente
				this.current_page = res.current_page;
				this.next_page = res.next_page;
				this.prev_page = res.previus_page;

				// Se non è possibile proseguire disabilito il pulsante, altrimneti [ri]attivo
				let comp = this.next_page === null;
				this.buttons.forward.elements.forEach(function (e)
				{
					e.prop ("disabled", comp);
				});

				// Se non è possibile retrocedere disabilitio il pulsanto, altrimienti [ri]abilito
				comp = this.prev_page === null;
				this.buttons.backward.elements.forEach(function (e)
				{
					e.prop ("disabled", comp);
				});

				// In ogni caso riattivo il pulsante di ricarica
				this.buttons.reload.elements.forEach(function (e)
				{
					e.prop("disabled", false);
				});
		})
			.always(() =>
			{
				// In qualunque caso tolgo is-loading perché, nel bene o nel male, è comunque terminata
				Object.keys(this.buttons).forEach((key) =>
				{
					this.buttons[key].elements.forEach(function (button)
					{
						button.removeClass("is-loading");
					})
				});

				// Rilascio la risorsa
				this.semaphore = false;
			});
	}

	/**
	 * Funzione che va indietro
	 * @param e
	 */
	backward(e)
	{
		if(this.next_page !== null)
			this.current_page = this.prev_page;

		this.get();
	}

	/**
	 * Funzione che va avanti
	 * @param e
	 */
	forward(e)
	{
		if(this.next_page !== null)
			this.current_page = this.next_page;

		this.get();
	}

	/**
	 * Funzione che crea un nuovo oggetto partendo dalle opzioni di defualt e quelle passate
	 * @param opts Object
	 * @return Object
	 * @private
	 */
	bulidArgs(opts)
	{
		let dux = {};
		$.extend(dux, opts, this.params);

		return dux;
	}
}