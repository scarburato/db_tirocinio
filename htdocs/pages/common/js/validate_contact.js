const PHONE_ADDRESS_ISO20022 = new RegExp("\\+[0-9]{1,3}-[0-9()+\\-]{1,30}");

$(".valida_iso").submit(function ()
{
	let valido = true;
	let nome = $(this).find("input[name='nome']");
	let cognome = $(this).find("input[name='cognome']");
	let telefono = $(this).find("input[name='tel']");
	let telefax = $(this).find("input[name='fax']");

	if(nome.val().length < 1)
	{
		valido = false;
		nome.addClass("is-danger");
	}

	if(cognome.val().length < 1)
	{
		valido = false;
		cognome.addClass("is-danger");
	}

	if(telefono.val().length > 0  && !PHONE_ADDRESS_ISO20022.test(telefono.val()))
	{
		valido = false;
		telefono.addClass("is-danger");
		telefono.parent().parent().find("p").addClass("is-danger");
		telefono.focus();
	}

	if(telefax.val().length > 0  && !PHONE_ADDRESS_ISO20022.test(telefax.val()))
	{
		valido = false;
		telefax.addClass("is-danger");
		telefax.parent().parent().find("p").addClass("is-danger");
		telefax.focus();
	}

	return valido;
});