<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 17/05/18
 * Time: 10.34
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

/* Siccome questa pagina è pensata per essere inclusa nel PHP con "include" è sicuro che $user sia già stato allocato
 * lo alloco solo se la pagina è stata chiamata a se (es. richiesta AJAX ovvero richiesta diretta dal browser) */
if(!isset($user))
	$user = new auth\User();

$user->is_authorized(\auth\LEVEL_FACTORY | \auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

?>

<div class="field is-horizontal">
	<div class="field-label is-normal">
		<label class="label">Dati Anagrafici</label>
	</div>
	<div class="field-body">
		<div class="field">
			<input name="nome" class="input" type="text" required
				   placeholder="Nome" maxlength="128">
			<p class="help">
				Campo obbligatorio
			</p>
		</div>
		<div class="field">
			<div class="field">
				<input name="cognome" class="input" type="text" required
					   placeholder="Cognome" maxlength="128">
				<p class="help">
					Campo obbligatorio
				</p>
			</div>
		</div>
	</div>
</div>
<div class="field is-horizontal">
	<div class="field-label is-normal">
		<label class="label">Recapiti</label>
	</div>
	<div class="field-body">
		<div class="field">
			<input name="posta" class="input" type="email"
				   placeholder="Indirizzo di posta elettronica" maxlength="64">
		</div>
	</div>
</div>
<div class="field is-horizontal">
	<div class="field-label is-normal"></div>
	<div class="field-body">
		<div class="field">
			<input name="tel" class="input" type="tel" pattern="\+[0-9]{1,3}-[0-9()+\-]{1,30}"
				   placeholder="Numero di telefono" maxlength="35">
			<p class="help">
				Inserire un numero di telefono in conformità ad
				<a target="_blank" href="https://www.iso20022.org/standardsrepository/public/wqt/Description/mx/dico/datatypes/_YXvFB9p-Ed-ak6NoX_4Aeg_-1045927120">
					ISO 20022
				</a>
				come
				<code>
					+39-05066666
				</code>
			</p>
		</div>
	</div>
</div>
<div class="field is-horizontal">
	<div class="field-label is-normal"></div>
	<div class="field-body">
		<div class="field">
			<input name="fax" class="input" type="tel" pattern="\+[0-9]{1,3}-[0-9()+\-]{1,30}"
				   placeholder="Numero di telefono telefax" maxlength="35">
			<p class="help">
				Inserire un numero di telefono in conformità ad
				<a target="_blank" href="https://www.iso20022.org/standardsrepository/public/wqt/Description/mx/dico/datatypes/_YXvFB9p-Ed-ak6NoX_4Aeg_-1045927120">
					ISO 20022
				</a>
				come
				<code>
					+39-050-66666
				</code>
			</p>
		</div>
	</div>
</div>
<div class="field is-horizontal">
	<div class="field-label is-normal">
		<label class="label">Qualifica</label>
	</div>
	<div class="field-body">
		<div class="field">
			<input name="qualifica" class="input" type="text"
				   placeholder="Qualifica" maxlength="128">
		</div>
	</div>
</div>
<div class="field is-horizontal">
	<div class="field-label is-normal">
		<label class="label">Ruolo aziendale</label>
	</div>
	<div class="field-body">
		<div class="field">
			<textarea name="ruolo" class="textarea"
					  placeholder="Descrivere brevemente il proprio ruolo aziendale" maxlength="65535"></textarea>
		</div>
	</div>
</div>
<div class="field">
	<button class="button is-large is-primary is-fullwidth" type="submit">
		Registra
	</button>
</div>
