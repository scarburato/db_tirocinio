/**
 * Classe che permette la navigazione tra pagine a navigazione
 * dinamica che sono state costruite con PaginationBuilder in PHP
 * @author Dario Pagani
 */

class DynamicPagination
{
	/**
	 * @param paginationDiv jQuery la DIV
	 * @param paginationAddr {String | URL} indirizzo che ritorna HTML generato da PHP
	 * @param optionsGET {Object | undefined} opzioni supplementari da passare alla GET
	 */
	constructor(paginationDiv, paginationAddr, optionsGET)
	{
		if(!(paginationDiv instanceof jQuery))
			throw("paginationDiv must be an instance of jQuery");

		if(!(isString(paginationAddr)) && !(paginationAddr instanceof URL))
			throw("paginationAddr must be a String or an instance on URL");

		if(optionsGET !== undefined && !(optionsGET instanceof Object))
			throw("optionsGET must be undefined or an Object!");

		// Element jQuery
		this.div = paginationDiv;
		this.load = $();

		this.defOptions = optionsGET === undefined ? {} : optionsGET;
		this.remAddr = (paginationAddr instanceof URL) ? paginationAddr.href : paginationAddr;

		this.currentPage = undefined;
		this.semaforo = false;

		this.onChange = () => {};

		// Bind ai tasti dinamici
		this.div.on("click", ".js-page-nav", (e) =>
		{
			this.goto($(e.target).data("page"));
		});

		this.div.on("keyup", ".js-page-nav", function (e)
		{
			if(e.which === 13)
				$(this).click();
		});
	}

	/**
	 * Imposta l'oggetto da mostare quando si effettuano richieste GET
	 * @param loadingDiv jQuery
	 */
	setLoading(loadingDiv)
	{
		if( !(loadingDiv instanceof jQuery))
			throw("loadingDIV must be undefined or an instance of jQuery");

		this.load = loadingDiv;
	}

	/**
	 * Registra UNA funzione da eseguire quando è cambianta pagina
	 * @param handler Function(pagina_richiesta, pagina_ottenuta)
	 */
	setOnChange(handler)
	{
		if(!$.isFunction(handler))
			throw("handler must be function");

		this.onChange = handler;
	}

	/**
	 * Pagina correntemente selezionata
	 * @return {undefined|number}
	 */
	getCurrentPage()
	{
		return this.currentPage;
	}

	/**
	 * @param page {number } Pagina di destinazione
	 * @param forceReload {bool | undefined} Forzare il caricamento
	 */
	goto(page, forceReload)
	{
		if(!Number.isInteger(page))
			throw("destination page must be an integer");

		if(page === this.currentPage && !forceReload)
			return;

		if(this.semaforo)
			return;

		this.semaforo = true;

		this.load.show();
		this.div.hide();

		$.get(
			this.remAddr,
			this.bulidArgs({
				pagina: page
			})
		)
			.done((data) =>
			{
				this.div.html(data);
				this.div.show();
				this.load.hide();

				this.currentPage = this.div.find(".ajax_comment").data("current-page");

				this.onChange(page, this.currentPage);
			})
			.always(() =>
			{
				this.semaforo = false;
			});
	}

	/**
	 * Scorciatoia per la chiamata a goto(getCurrentPage(), true);
	 */
	refresh()
	{
		if(this.currentPage !== undefined)
			this.goto(this.currentPage, true);
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
		$.extend(dux, this.defOptions, opts);

		return dux;
	}
}