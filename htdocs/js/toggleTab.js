/**
 * Classe che permette di creare schede interattive.
 * Si deve passare un menù creato coi i tag html li ed ul, un div che contiene altri div. Ogni div è una scheda.
 *
 * Per associare le schede ai div usare il tag data-tab. Ad esempio premendo su un li con data-tab="info" lo script
 * tenterà di visualizzare la div che ha data-tab="info"!
 */
class ToggleTab
{
	/**
	 * @param selector jQuery										È l'elemento che contine le li
	 * @param tabs_container jQuery									È l'elemento che contiene gli elementi associati
	 * @param active_tab undefined | string @since 2018-02-28		Opzionale stringa che identifica la scheda attiva di def
	 */
	constructor(selector, tabs_container, active_tab)
	{
		if(!(selector instanceof jQuery))
			throw("select must be jQuery");

		if(!(tabs_container instanceof jQuery))
			throw("container must be jQuery");

		if(active_tab !== undefined && !isString(active_tab))
			throw("active_tab must be a Strig or undefined");

		let _self = this;

		this.selector = selector;
		this.tabs_container = tabs_container;

		this.buttons = this.selector.find ("li[data-tab]");
		this.tabs = this.tabs_container.find ("[data-tab]");

		if (active_tab !== undefined)
			this.setActive(active_tab);


		this.handler = function ()
		{
		};

		// Accessibilità prima di tutto!
		this.buttons.each(function (index)
		{
			$(this).prop("tabindex", "0");
		});

		// Emualzione pressione alla pressione di Enter
		this.buttons.on ("keyup", function(e)
		{
			if(e.which === 13)
				$(this).click();
		});

		// Evento da eseguire alla pressione del mouse
		this.buttons.on ("click", function ()
		{
			// Variabili
			let tab = $ (this).data ("tab");

			_self.buttons.removeClass ("is-active");
			$ (this).addClass ("is-active");

			_self.tabs.hide ();
			_self.tabs_container.find ("[data-tab=\"" + tab + "\"]").show ();

			_self.handler(tab);
		})
	}
	/**
	 * @param chTab string
	 */
	setActive(chTab) {
		this.buttons.removeClass("is-active");
		this.selector.find("[data-tab=\""+chTab+"\"]").addClass("is-active");
		this.tabs.hide();
		this.tabs_container.find("[data-tab=\""+chTab+"\"]").show();
	}

	/**
	 * Una (SINGOLA) funzione da eseguire
	 * @param f function(evento)
	 */
	onChange(f)
	{
		if(!$.isFunction(f))
			throw("Argument must me a function!");

		this.handler = f;
	}
}
