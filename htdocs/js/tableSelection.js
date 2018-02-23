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
	}

	/**
	 *
	 * @returns {null, jQuery}
	 */
	getSelectedRow()
	{
		return this.selected_row;
	}

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
	}
}