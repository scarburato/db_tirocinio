$.urlParam = {
	params: new URLSearchParams(new URL(window.location).search),

	/**
	 * Ottenere un paramatro della querystring
	 * @author https://stackoverflow.com/a/25359264
	 * @param name String
	 * @return {string | undefined}
	 */
	get: function (name)
	{
		let parm = this.params.get(name);
		return parm === null ? undefined : parm;
	},

	/**
	 * @see URLSearchParams.set
	 * @param name String
	 * @param value String
	 */
	set: function (name, value)
	{
		this.params.set(name, value);

		if (!history.replaceState)
			return;

		let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + "?" + this.params.toString();
		window.history.replaceState({path:newurl},'',newurl);
	}
};