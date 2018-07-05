const AZIENDA = parseInt($.urlParam.get("id"));

if(isNaN(AZIENDA))
	throw new Error("Impossibile ottenere id azienda per i caricamenti dinamici!");

let indirizzi_dinamici = new DynamicPagination($("#indirizzi_dinamici"), "indirizzi.php", {
	azienda: AZIENDA
});

indirizzi_dinamici.goto(0);

let sedi_dinamici = new DynamicPagination($("#sedi_dinamici"), "sedi.php", {
	azienda: AZIENDA
});

sedi_dinamici.goto(0);

let contatti_dinamici = new DynamicPagination($("#contatti_dinamici"), "contatti.php", {
	azienda: AZIENDA
});

contatti_dinamici.goto(0);