class ToggleTab
{
	/**
	 * @param selector jQuery
	 * @param tabs_container jQuery
	 * @param active_tab undefined | string @since 2018-02-28
	 */
	constructor(selector, tabs_container, active_tab)
	{
		let _self = this;

		this.selector = selector;
		this.tabs_container = tabs_container;

		this.buttons = this.selector.find ("li[data-tab]");
		this.tabs = this.tabs_container.find ("[data-tab]");

		if (active_tab !== undefined)
		{
			this.buttons.removeClass("is-active");
			this.selector.find("[data-tab=\""+active_tab+"\"]").addClass("is-active");
			this.tabs.hide();
			this.tabs_container.find("[data-tab=\""+active_tab+"\"]").show();
		}

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
