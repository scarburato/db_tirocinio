<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 23/02/18
 * Time: 15.24
 */
require_once dirname(__FILE__) . "/../../vendor/autoload.php";
require_once dirname(__FILE__) . "/init.php";

$google_client_2->fetchAccessTokenWithAuthCode($_GET["code"]);
$token = $google_client_2->getAccessToken();

$google_client_2->setAccessToken($token);

$oauth2 = new \Google_Service_Oauth2($google_client_2);
$user = $oauth2->userinfo->get();

if($user->email !== "dario.pagani@itispisa.gov.it")
    die("Credenziali invalide!");

$file = [
    "token" => $token,
    "refresh" => $google_client_2->getRefreshToken()
];

file_put_contents(dirname(__FILE__ ) . "/secret_token.json", json_encode($file));
