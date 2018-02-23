<?php

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$filtro = (isset($_GET["filtro"])) ? "%{$_GET["filtro"]}%" : "%";
$server = new \mysqli_wrapper\mysqli();
$utenze = new class($server, "SELECT id, nome, cognome, indirizzo_posta, D.utente, S.utente FROM UtenteGoogle
LEFT JOIN Docente D ON UtenteGoogle.id = D.utente
LEFT JOIN Studente S ON UtenteGoogle.id = S.utente
  WHERE nome LIKE ? OR cognome LIKE ? OR indirizzo_posta LIKE ?

") extends \helper\Pagination
{
public function compute_rows()
{
$row_tot = 0;

$conta = $this->link->prepare("SELECT COUNT(id) AS 'c' FROM UtenteGoogle");
$conta->execute(true);
$conta->bind_result($row_tot);
$conta->fetch();

return $row_tot;
}
};
$utenze->set_limit((isset($_GET["limite"]) && $_GET["limite"] > 1) ? $_GET["limite"] : 15);
$utenze->set_current_page((isset($_GET["pagina"]) && $_GET["pagina"] >= 0) ? $_GET["pagina"] : 0);

$utenze->bind_param(
"sss",
$filtro,
$filtro,
$filtro
);

$utenze->execute(false);
$utenze->bind_result($id, $nome, $congome, $posta, $doc, $stu);
echo "ciao";

while($utenze->fetch())
{
    echo $id . $nome . "<br>";
}