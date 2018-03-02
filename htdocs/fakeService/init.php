<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 23/02/18
 * Time: 15.40
 */

$google_client_2 = new Google_Client();
$google_client_2->setApplicationName('fakeService');
$google_client_2->setAuthConfig(dirname(__FILE__) . "/../../client_secret_fake_service.json");

$google_client_2->setRedirectUri("http://localhost/fakeService/auth.php");
//$google_client_2->setRedirectUri("https://pgnfba.dtdns.net:10101/fakeService/auth.php");

$google_client_2->addScope("https://www.googleapis.com/auth/admin.directory.user.readonly");
$google_client_2->addScope("https://www.googleapis.com/auth/admin.directory.orgunit.readonly");
$google_client_2->addScope("https://www.googleapis.com/auth/userinfo.email");
$google_client_2->addScope("https://www.googleapis.com/auth/userinfo.profile");

$google_client_2->setAccessType('offline');
$google_client_2->setApprovalPrompt ("force");

function build(Google_Client $google_Client)
{
    $roba = json_decode(file_get_contents(dirname(__FILE__) . "/secret_token.json"), JSON_OBJECT_AS_ARRAY);

    $google_Client->setAccessToken($roba["token"]);
    if($google_Client->isAccessTokenExpired())
    {
        $new_token = $google_Client->fetchAccessTokenWithRefreshToken();
        $google_Client->setAccessToken($new_token);
        $roba["token"] = $google_Client->getAccessToken();
        //$roba["refresh"] = $google_Client->getRefreshToken();

        file_put_contents(dirname(__FILE__ ) . "/secret_token.json", json_encode($roba));
    }
}
