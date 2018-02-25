<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 11/02/18
 * Time: 18.53
 */
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

(new auth\User())->redirect_if_unauthorized(\auth\LEVEL_FACTORY);

?>

<a href="../../utils/logout.php">Esci.</a>