<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 22/01/18
 * Time: 19.48
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_STUDENT, "./../../");

$index = (isset($_GET["index"]) ? $_GET["index"] : 0);

// TODO Controllare GET
if($index > 3)
    return;

if($index < 3)
    $next = $index + 1;
else
    $next = NULL;
?>

<article class="card tirocinio" id="tirocinio_<?= $index ?>" data-nextid="<?= (string)($next) ?>">
    <header class="card-header">
        <h1 class="card-header-title">
            Stage a Black Mesa corp.
        </h1>
    </header>
    <div class="card-content">
        <div class="content">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus nec iaculis mauris.
            <a href="#">@bulmaio</a>. <a href="#">#css</a> <a href="#">#responsive</a>
            <br>
            <time datetime="2016-01-01">11:09 PM - 1 Jan 2016</time>
        </div>
    </div>
    <footer class="card-footer">
        <a href="tirocinio_resoconto.php?tirocinio=<?=$index?>" class="card-footer-item">
            <span class="icon">
                <i class="fa fa-pencil-square" aria-hidden="true"></i>
            </span>
            <span>
                Scrivi un resoconto
            </span>
        </a>
    </footer>
</article>
<br>