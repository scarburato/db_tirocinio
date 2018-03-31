/**
 * Questa classe prende due tabelle TableSelection e le rende compatibili tra loro.
 * Si dovrà poi configurare i pulsanti per gli spostamenti degli elementi da una tabella
 * all'altra!
 */
class TableChooser
{
	/**
	 *
	 * @param tableA TableSelection
	 * @param tableB TableSelection
	 */
	constructor(tableA, tableB)
	{
		if(!(tableA instanceof TableSelection) || !(tableB instanceof TableSelection))
			throw("No TableSelection provided to constructor!");

		this.tableA = tableA;
		this.tableB = tableB;

		this.buttonsMoveToA = [];
		this.buttonsMoveToB = [];
	}

	/**
	 * Aggiunge un pulsante che premuto sposta la selezione attuale nella tabella B in A
	 * @param button jQuery
	 */
	addButtonMoveToA(button)
	{
		if(! button instanceof jQuery)
			throw ("Button MUST be jQuery!");

		button.on("click", () => {this.moveTo("A")});
		this.buttonsMoveToA.push(button);
	}

	/**
	 * Aggiunge un pulsante che premuto sposta la selezione attuale nella tabella A in B
	 * @param button jQuery
	 */
	addButtonMoveToB(button)
	{
		if(! button instanceof jQuery)
			throw ("Button MUST be jQuery!");

		button.on("click", () => {this.moveTo("B")});
		this.buttonsMoveToB.push(button);
	}

	/**
	 * @param where String
	 */
	moveTo(where)
	{
		if(!where instanceof String)
			throw("where must be String");

		//if(where !== "B" || where !== "A")
		//	throw ("Invalid argument");

		let table_from = (where === "A" ? this.tableB : this.tableA);
		let table_to = (where === "A" ? this.tableA : this.tableB);
		let attuale = table_from.getSelectedRow();

		if(attuale === null)
			return;

		attuale.removeClass("is-selected");
		table_to.tbody.append(attuale);
	}
}