class ToggleTab
{
	/**
	 * @param selector jQuery
	 * @param tabs_container jQuery
	 */
	constructor(selector, tabs_container)
	{
		let _self = this;

		this.selector = selector;
		this.tabs_container = tabs_container;

		this.buttons = this.selector.find ("li[data-tab]");
		this.tabs = this.tabs_container.find ("[data-tab]");

		this.handler = function ()
		{
		};

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
	 *
	 * @param f function(evento)
	 */
	onChange(f)
	{
		this.handler = f;
	}
}