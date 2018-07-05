/*
Script che associa l'importato con i campi
 */

/**
 * Questo handler viene chiamato alla pressione di "No, riprova" nel dialogo di correttezza. Premere "No, riprova"
 * azzera tutti i dati importati e ripristina la situazione iniziale di selezione del file
 */
$("#csv_correct_false").on("click", function ()
{
	// Azzerro valori importati in precedenza
	current_result = [];
	head = undefined;

	$("#csv_head").html("");
	$("#csv_body").html("");

	$(".set_div").show();
	$("#out").hide();
	$("#csv_halt").prop("disabled", true);
	$("#csv_halt_field").show();
	$("#csv_correct").hide();
});

/**
 * Questo handler viene eseguito alla pressione del pulsante "Sì, continua" nel dialogo di conferma della correttaza.
 * Questa opzione nasconde tutti i dialogi presenti fin'ora a video e mostra il dialogo per associare le colonne ai
 * campi dell'importazione!
 */
$("#csv_correct_true").on("click", function ()
{
	$("#out").hide();
	$("#load").show();

	/* Creazione elementi jQuery per le options. Cioè creo un elemento DOM <option> da usare in una select per ogni
	 * colonna registrata.  */
	let cols = [];
	head.forEach((col) =>
	{
		cols.push($("<option/>",{
			text: col,
			value: col
		}));
	});

	/**
	 * Ottengo tutte le select necessare che nella pagina fanno parte della classe COL_OUT e ci inserisco le option
	 * appena generate
	 * @type {*|jQuery|HTMLElement}
	 */
	let selects = $(".col_out");
	cols.forEach((opt) =>
	{
		opt.appendTo(selects);
	});

	// Mostro finalmente il dialogo
	$("#config_cols").show();
	$("#load").hide();
});

/**
 * Quest'oggetto contiene le associazioni tra le righe del documento e lo studente che ha effettuato tale stage.
 * @type {{keys: {key: undefined, first_name: undefined, last_name: undefined, mail: undefined}, rows: Map<any, any>, assoc: Map<any, any>, names: Array, index: number, update_names: stu_assoc.update_names, refresh: stu_assoc.refresh}}
 */
const stu_assoc = {
	/**
	 * Queste sono le colonne usate per identificare uno studente
	 */
	keys: {
		key: undefined,
		first_name: undefined,
		last_name: undefined,
		mail: undefined
	},

	/** Contiene le righe associate per studente */
	rows: new Map(),
	/** Contine gli studenti associati per l'id della base dati, usato dopo! */
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

/**
 *
 * @type {{keys: {name: undefined}, rows: Map<any, any>, assoc: Map<any, any>, names: Array, index: number, update_names: factory_assoc.update_names, refresh: factory_assoc.refresh}}
 */
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
		$("#data_assoc_fact_index").html(this.index + 1);
		$("#data_assoc_fact_max").html(this.names.length);
	}
};

/**
 * Questo handler viene chiamato una volta confermata l'associaizone colonne <-> informazioni base dati. Salva le
 * associazioni scelte nell'oggetto e avvia il dialogo di associazione degli studenti con il proprio utente di Google
 */
$("#data_assoc_goon").on("click", function ()
{
	// Salvo chiavi studente
	stu_assoc.keys.key = $("#assoc_stu_key").val();
	stu_assoc.keys.first_name = $("#assoc_stu_name").val();
	stu_assoc.keys.last_name = $("#assoc_stu_last_name").val();
	stu_assoc.keys.mail = $("#assoc_stu_mail").val();

	// Salvo chiave azienda
	factory_assoc.keys.name = $("#assoc_fact_name").val();

	/**
	 * Scorro tutti i risulati salvati dal CSV, riga per riga. Nella mappa associazioni azienda <-> riga creo un vettore
	 * dove salvo tutte le aziende di quell'azienda
	 */
	current_result.forEach(function (tirocinio)
	{
		// Associazione per azienda
		let fact_key = tirocinio[factory_assoc.keys.name];
		if(fact_key !== undefined)
		{
			// È la prima volta che trovo una corispondenza con questa chiave nel CSV?
			if(!factory_assoc.rows.has(fact_key))
				// Sì, quindi per questa chiave creo un vettore che contine nella posizione 0 la riga
				factory_assoc.rows.set(fact_key, [tirocinio]);
			else
				// No, quindi ottengo il vettore per tale chiave e ci spingo la riga
				factory_assoc.rows.get(fact_key).push(tirocinio);
		}

		// Associazione per studente
		let stu_key;

		/* È stata seleziona una colonna da usare come chive? Se no allora genero la chiave come nome + cognome.
		 * Viva l'ambiguità! Altrimenti uso come sopra la chiave fornita */
		if(stu_assoc.keys.key === undefined || stu_assoc.keys.key === "")
		{
			// Scarto le righe che non hanno corrispondenze con la chiave!
			if((tirocinio[stu_assoc.keys.first_name] === undefined) || (undefined === tirocinio[stu_assoc.keys.last_name]))
				return;

			stu_key = (tirocinio[stu_assoc.keys.first_name] + tirocinio[stu_assoc.keys.last_name])
		}
		else
			stu_key = tirocinio[stu_assoc.keys.key];

		if(stu_key === undefined)
			return;

		// Come sopra
		if(!stu_assoc.rows.has(stu_key))
			stu_assoc.rows.set(stu_key, [tirocinio]);
		else
			stu_assoc.rows.get(stu_key).push(tirocinio);
	});

	// TODO Finire controllo colonne!

	/* Finalmente nascondo il dialogo per configurare le colonne e mostro il dialogo per mostrare gli studenti appena
	 * trovati ad utenti reali di Google */
	$("#config_cols").hide();


	stu_assoc.update_names();
	factory_assoc.update_names();

	stu_assoc.refresh();
	factory_assoc.refresh();

	$("#data_assoc_stu").show();
	// Ora la logica continua su valida_stu.js ! Buonafortuna
});