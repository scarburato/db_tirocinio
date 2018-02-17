<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 11/02/18
 * Time: 18.53
 */
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_FACTORY);
?>

<a href="../../utils/logout.php">Esci.</a>