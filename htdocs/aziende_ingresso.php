<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 28/01/18
 * Time: 18.27
 */

require_once "utils/const.hphp";

$captcha_key = json_decode(file_get_contents("../client_secret_captcha.json"), true);

$post = stream_context_create([
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query([
            'secret' => $captcha_key["private_key"], // <- Your secret key
            'token' => $_POST['coinhive-captcha-token'],
            'hashes' => 256
        ])
    ]
]);

$risposta_captcha = json_decode(file_get_contents('https://api.coinhive.com/token/verify', false, $post));

if(!$risposta_captcha || !$risposta_captcha->success)
{
    header("Location: index.php?coinhive_error=true");
    die("NON SI È USATO IL CAPTCHA!¡");
}

$server = new mysqli(DBMS_SERVER, DBMS_USER, DBMS_PASS, DBMS_DB_NAME);
$indirizzo = (inet_pton($_SERVER["REMOTE_ADDR"]));

$controllo_indirizzo = $server->prepare("SELECT indirizzo_rete FROM AziendeTentativiAccesso WHERE indirizzo_rete = ?");
$controllo_indirizzo->bind_param(
    "s",
    $indirizzo
);

$controllo_indirizzo->execute();
$esiste = $controllo_indirizzo->fetch() !== NULL;
$controllo_indirizzo->close();
if($esiste)
{
    $incremento = $server->prepare(
        "UPDATE AziendeTentativiAccesso
                SET tentativi_falliti = tentativi_falliti + 1
                WHERE indirizzo_rete = ?");
    $incremento->bind_param(
        "s",
        $indirizzo
    );
    $incremento->execute();
    $incremento->close();
}
else
{
    $aggiungi = $server->prepare(
        "INSERT INTO AziendeTentativiAccesso(indirizzo_rete, tentativi_falliti, ultimo_tentativo) VALUES (?, 1, CURRENT_TIMESTAMP())"
    );

    $aggiungi->bind_param(
        "s",
        $indirizzo
    );
    $aggiungi->execute();
    $aggiungi->close();
}