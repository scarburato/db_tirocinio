/**
 * Questa classe rende interrativa una tabella in HTML.
 * Supporta solo una modalità: a singola selezione!
 * Tutti i tag <a></a> nella tabella vengono interpretati
 * come "pulsanti" per cambiare riga. A virtù di ciò ricordarsi di aggiungere la
 * proprietà tabindex='' al link. Anche gli utenti con particolari necessità potranno selezionare la riga con l'uso
 * di tastiera e/o assistenti vocali!
 * @example
 * `<a tabindex=''>Seleziona riga</a>`
 * @author Dario Pagani
 */
class TableSelection
{
	/**
	 *
	 * @param tbody jQuery
	 */
	constructor(tbody)
	{
		this.selected_row = null;
		this.tbody = tbody;

		this.tbody.on("click", "tr", this.onClick.bind(this));
		this.tbody.on("keyup", "tr", this.onClick.bind(this));

		this.handlers = [];
	}

	/**
	 * Ritorna la riga selezionata elemento HTML tr
	 * @returns {null, jQuery}
	 */
	getSelectedRow()
	{
		return this.selected_row;
	}

	/**
	 * @private
	 * @param event
	 */
	onClick(event)
	{
		let code = event.which;
		if (code !== 1 && code !== 32 && code !== 13 && code !== 188 && code !== 186)
			return;

		if(this.selected_row !== null)
			this.selected_row.removeClass("is-selected");

		if(this.selected_row === null || event.currentTarget !== this.selected_row[0])
		{
			$ (event.currentTarget).addClass ("is-selected");
			this.selected_row = $(event.currentTarget);
		}
		else
			this.selected_row = null;

		for(let i = 0; i < this.handlers.length; i++)
			this.handlers[i](this.selected_row);
	}

	/**
	 * La funzione passata come argomento verra chiamataogni qualcova una line viene
	 * selezionata ovvero deleselezionata!
	 * @param handler (row). Il primo argomento è il riferimo alla TR selezionata,
	 * null viene passato se nessuna riga è selezionata!
	 */
	addHandler(handler)
	{
		this.handlers.push(handler);
	}
}