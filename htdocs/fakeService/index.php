<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 23/02/18
 * Time: 15.17
 */
require_once dirname(__FILE__) . "/../../vendor/autoload.php";
require_once dirname(__FILE__) . "/init.php";

?>

<p>Permettere di fare cose</p>
<a href="<?= filter_var($google_client_2->createAuthUrl(), FILTER_SANITIZE_URL) ?>">Entrare</a>