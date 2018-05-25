class TogglePanel
{
	/**
	 *
	 * @param panel String|jQuery String is depracted
	 */
	constructor(panel)
	{
		if(panel instanceof jQuery)
			this.panel = panel;
		else if(isString(panel))
			this.panel = $(panel);
		else
			throw ("First parameter must be a jQuery");

		this.onShow = function (index)
		{
			index.value = "";
		};
	}

	setOnShow(x)
	{
		this.onShow = x;
	}

	show()
	{
		this.panel.addClass ("is-active");

		let elements = this.panel.find ($ ("input"));

		for (let i = 0; i < elements.length; i++)
			this.onShow (elements[i]);
	}

	hide()
	{
		this.panel.removeClass ("is-active");
	}

	toggle()
	{
		if ($ (this.panelName).hasClass ("is-active"))
			this.hide ();
		else
			this.show ();
	}

	/**
	 * Returns true if the panel is visible
	 * @returns {boolean}
	 */
	isActive()
	{
		return this.panel.hasClass("is-active");
	}
}