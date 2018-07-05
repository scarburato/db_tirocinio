/**
 * Verifica validità del codice di controllo della partita IVA.
 * Il valore vuoto è "valido" per semplificare la logica di verifica
 * dell'input, assumendo che l'eventuale l'obbligatorietà del campo
 * sia oggetto di un controllo e retroazione distinti.
 * Per aggiornamenti e ulteriori info v. http://www.icosaedro.it/cf-pi
 * @author Umberto Salsi <salsi@icosaedro.it>
 * @version 2016-12-05
 * @param pi string Partita IVA da controllare.
 * @return boolean
 */
function controllaPIVA(pi)
{
	if (pi.length === 0)
		return true;
	if (!/^[0-9]{11}$/.test (pi))
		return false;

	let s = 0;
	for (i = 0; i <= 9; i += 2)
		s += pi.charCodeAt (i) - '0'.charCodeAt (0);
	for (let i = 1; i <= 9; i += 2)
	{
		let c = 2 * (pi.charCodeAt (i) - '0'.charCodeAt (0));
		if (c > 9) c = c - 9;
		s += c;
	}
	let atteso = (10 - s % 10) % 10;
	return atteso === (pi.charCodeAt (10) - '0'.charCodeAt (0));
}

/**
 * @author Dario Pagani <dario.pagani@itispisa.gov.it>
 */
class InputIconControl
{
	/*
	 * @param control jQuery È l'oggetto che contine controllo di bulma
	 * @param stati Array(Stato)
	 */
	constructor(control, stati = [
		{icon: "ok", class: "is-success"},
		{icon: "error", class: "is-danger"},
		{icon: "warn", class: "is-warning"},
		{icon: "load", class: ""}])

	{
		this.control = control;
		this.stati = [];
		this.input = control.find ("input");
		if (this.input.length === 0)
			throw "No input";

		let errore = false;
		let my = this;
		stati.forEach (function (stato)
		{
			let foo = control.find ("span[data-status=" + stato.icon + "]");
			foo.class_to_switch = stato.class;
			my.stati.push (foo);
			errore = foo.length === 0;
		});

		if (errore)
			throw "No span found";

		this.hide ();
	}

	hide()
	{
		let me = this;
		this.stati.forEach (function (stato)
		{
			me.input.removeClass(stato.class_to_switch);
			stato.hide ();
		})
	}

	/**
	 *
	 * @param stato string
	 */
	switch(stato)
	{
		this.hide ();
		let res = this.stati.find (function (stato_j)
		{
			return stato_j.data ("status") === stato;
		});

		res.show ();
		this.input.addClass(res.class_to_switch);
	}
}
let main_form = $("#main_form");
let codice_fiscale = main_form.find ("input[name='codice_fiscale']");
let codice_fiscale_icone = new InputIconControl (codice_fiscale.parent ());
let codice_fiscale_db_help = codice_fiscale.parent().parent().find("p[data-help='error-db']");
let codice_fiscale_ultimo_input = "";
let codice_fiscale_last_request = 0;

codice_fiscale.on ("keyup", function (tasto)
{
	$(this).val($(this).val().toUpperCase());
	let cf = $(this).val();

	if(cf === codice_fiscale_ultimo_input)
		return;
	codice_fiscale_ultimo_input = cf;

	let actual_request = codice_fiscale_last_request = new Date().getTime();

	codice_fiscale_db_help.addClass("is-hidden");

	if(cf.length === 0)
	{
		codice_fiscale_icone.hide();
		return;
	}

	if(cf.length < 11 || cf.length > 16)
	{
		codice_fiscale_icone.switch ("error");
		return
	}

	codice_fiscale_icone.switch("load");

	$.post(
		"./rest/cf_esiste.php",
		{
			cf: cf
		}
	).done(function (data)
	{
		if(actual_request !== codice_fiscale_last_request)
			return;

		let esiste = data.esiste;

		console.log(data);

		if(esiste)
		{
			codice_fiscale_icone.switch("error");
			codice_fiscale_db_help.removeClass("is-hidden");
		}
		else
			codice_fiscale_icone.switch ("ok");
	});
});

let iva = main_form.find ("input[name='iva']");
let iva_icona = new InputIconControl(iva.parent());
let iva_db_help = iva.parent().parent().find("p[data-help='error-db']");
let iva_format_help = iva.parent().parent().find("p[data-help='error-iva']");
let iva_ultimo_input = "";
let iva_ultima_richiesta = 0;

iva.on ("keyup", function (tasto)
{
	$(this).val($(this).val().toUpperCase());
	let iva = $(this).val();

	if(iva === iva_ultimo_input)
		return;

	iva_ultimo_input = iva;
	let richiesta_attuale = iva_ultima_richiesta = new Date().getTime();
	let valido;

	iva_db_help.addClass("is-hidden");
	iva_format_help.addClass("is-hidden");

	if(iva.length === 0)
	{
		iva_icona.hide();
		return;
	}

	if(iva.length !== 11)
	{
		iva_icona.switch ("error");
		return;
	}

	valido = controllaPIVA(iva);
	if(!valido)
	{
		iva_icona.switch("warn");
		iva_format_help.removeClass("is-hidden");
	}

	$.post(
		"./rest/iva_esiste.php",
		{
			iva: iva
		}
	).done(function (data)
	{
		if(richiesta_attuale !== iva_ultima_richiesta)
			return;

		let esiste = data.esiste;
		if(esiste)
		{
			iva_icona.switch("error");
			iva_db_help.removeClass("is-hidden");
		}
		else if(valido)
			iva_icona.switch ("ok");
	});
});