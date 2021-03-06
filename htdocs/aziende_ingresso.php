<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 28/01/18
 * Time: 18.27
 */

require_once "utils/lib.hphp";
require_once "utils/auth.hphp";

// Controllo captcha abilitato
if(!SKIP_CAPTCHA)
{
    $captcha_key = json_decode(file_get_contents("../client_secret_captcha.json"), true);
    $post = stream_context_create([
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query([
                'secret' => $captcha_key["private_key"],
                'token' => $_POST['coinhive-captcha-token'],
                'hashes' => compute_hashes(empty($_SESSION["hash_weight"]) ? 0 : $_SESSION["hash_weight"])
            ])
        ]
    ]);

    // Post alle API per verificare validità del captcha
    $risposta_captcha = json_decode(file_get_contents('https://api.coinhive.com/token/verify', false, $post));

    // Risposta pervenuta e valida?
    if (!$risposta_captcha || !$risposta_captcha->success)
    {
        header("Location: index.php?login_fail=captcha");
    }
}

// Collegamento DB
$server = new \mysqli_wrapper\mysqli();
$indirizzo = (inet_pton($_SERVER["REMOTE_ADDR"]));

// Ottenere parola d'ordine
$azienda = $server->prepare("SELECT parolaOrdine FROM Azienda WHERE id = ?");
$azienda->bind_param(
    "i",
    $_POST["id"]
);

$azienda->execute();
$azienda->bind_result($hash_pass);
$esiste = $azienda->fetch();
$azienda->close();

echo $_POST["id"] . "<br>" . $_POST["pass"] . "<br>" . $hash_pass . "<br>";

$success = false;
if($esiste && password_verify($_POST["pass"], $hash_pass))
{
    // TODO password_needs_rehash

    $_SESSION["user"]["type"] = \auth\LEVEL_FACTORY;
    $_SESSION["user"]["id"] = $_POST["id"];

    $controllo_indirizzo = $server->prepare(/** @lang MySQL */"SELECT successoAccesso(?)");
}
else
{
    $controllo_indirizzo = $server->prepare(/** @lang MySQL */
        "SELECT aggiungiTentativoAccesso(?)");

    $fail = true;
}

$controllo_indirizzo->bind_param(
    "s",
    $indirizzo
);

$controllo_indirizzo->execute();
$controllo_indirizzo->close();

redirect("index.php",[
    "login_fail" => ($fail ? "credentials" : "")
]);