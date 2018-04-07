/*
Script che associa l'importato con i campi
 */

$("#csv_correct_false").on("click", function ()
{
	current_result = [];
	head =undefined;

	$("#csv_head").html("");
	$("#csv_body").html("");

	$(".set_div").show();
	$("#out").hide();
	$("#csv_halt").prop("disabled", true);
	$("#csv_halt_field").show();
	$("#csv_correct").hide();
});

$("#csv_correct_true").on("click", function ()
{
	$("#out").hide();
	$("#load").show();

	// Creazione elementi jQuery per le options
	let cols = [];
	head.forEach((col) =>
	{
		cols.push($("<option/>",{
			text: col,
			value: col
		}));
	});

	let selects = $(".col_out");
	cols.forEach((opt) =>
	{
		opt.appendTo(selects);
	});

	$("#config_cols").show();
	$("#load").hide();
});

// Variabili
const stu_assoc = {
	keys: {
		key: undefined,
		first_name: undefined,
		last_name: undefined,
		mail: undefined
	},

	rows: new Map(),
	assoc: new Map(),

	names: [],
	index: 0,

	update_names: function ()
	{
		this.names = Array.from(this.rows.keys());
	},

	refresh: function ()
	{
		if(this.index >= this.names.length)
			return;

		$("#data_assoc_stu_index").text(this.index + 1);
		$("#data_assoc_stu_max").text(this.names.length);

		let a_row = this.rows.get(this.names[this.index])[0];

		if(this.keys.mail === undefined || this.keys.mail === "")
			$("#data_stu_mail").val(
				a_row[this.keys.first_name].toLowerCase() + "." + a_row[this.keys.last_name].toLowerCase() + "@" + DOMAIN
			);
		else
			$("#data_stu_mail").val(a_row[this.keys.mail]);

		$("#data_assoc_stu").show();
	}
};

const factory_assoc = {
	keys: {
		name: undefined,
	},

	rows: new Map(),
	assoc: new Map(),

	names: [],
	index: 0,

	update_names: function ()
	{
		this.names = Array.from(this.rows.keys());
	},

	refresh: function ()
	{
		if(this.index >= this.names.length)
			return;

		$("#data_assoc_fact_name").text(this.names[this.index]);

		$("#data_assoc_stu").show();
	}
};

$("#data_assoc_goon").on("click", function ()
{
	stu_assoc.keys.key = $("#assoc_stu_key").val();
	stu_assoc.keys.first_name = $("#assoc_stu_name").val();
	stu_assoc.keys.last_name = $("#assoc_stu_last_name").val();
	stu_assoc.keys.mail = $("#assoc_stu_mail").val();

	factory_assoc.keys.name = $("#assoc_fact_name").val();

	current_result.forEach(function (tirocinio)
	{
		// Associazione per azienda
		let fact_key = tirocinio[factory_assoc.keys.name];
		if(fact_key !== undefined)
		{
			if(!factory_assoc.rows.has(fact_key))
				factory_assoc.rows.set(fact_key, [tirocinio]);
			else
				factory_assoc.rows.get(fact_key).push(tirocinio);
		}

		// Associazione per studente
		let stu_key;

		// Scarto le righe che non hanno corrispondenze con la chiave!
		if(stu_assoc.keys.key === "")
		{
			if((tirocinio[stu_assoc.keys.first_name] === undefined) || (undefined === tirocinio[stu_assoc.keys.last_name]))
				return;

			stu_key = (tirocinio[stu_assoc.keys.first_name] + tirocinio[stu_assoc.keys.last_name])
		}
		else
			stu_key = tirocinio[stu_assoc.keys.key];

		if(stu_key === undefined)
			return;

		if(!stu_assoc.rows.has(stu_key))
			stu_assoc.rows.set(stu_key, [tirocinio]);
		else
			stu_assoc.rows.get(stu_key).push(tirocinio);
	});

	// TODO Finire controllo colonne!

	// Ora cerco per ogni persona il suo indirizzo di posta elettronica
	$("#config_cols").hide();

	stu_assoc.update_names();
	factory_assoc.update_names();

	stu_assoc.refresh();

	$("#data_assoc_stu").show();
});