class TogglePanel
{
	constructor(panelName)
	{
		this.panelName = panelName;
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
		$ (this.panelName).addClass ("is-active");

		let elements = $ (this.panelName).find ($ ("input"));

		for (let i = 0; i < elements.length; i++)
			this.onShow (elements[i]);
	}

	hide()
	{
		$ (this.panelName).removeClass ("is-active");
	}

	toggle()
	{
		if ($ (this.panelName).hasClass ("is-active"))
			this.hide ();
		else
			this.show ();
	}
}