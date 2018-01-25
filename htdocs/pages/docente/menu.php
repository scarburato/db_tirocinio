<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 23/01/18
 * Time: 20.01
 */

?>

<ul class="menu-list">
    <li>
        <a href="pages/docente/index.php" class="<?= ($index_menu !== 0 ?: "is-active")?>">
            <span class="icon">
            <i class="fa fa-tachometer" aria-hidden="true"></i>
            </span>
                <span>
            Cruscotto
            </span>
        </a>
    </li>
</ul>
<p class="menu-label">
    Tirocini
</p>
<ul class="menu-list">
    <li>
        <a class="<?= ($index_menu !== 1 ?: "is-active")?>">
            <span class="icon">
            <i class="fa fa-briefcase" aria-hidden="true"></i>
            </span>
                <span>
            Tirocini
            </span>
        </a>
        <ul>
            <li>
                <a class="<?= ($index_menu !== 2 ?: "is-active")?>">
                    <span class="icon">
                    <i class="fa fa-play" aria-hidden="true"></i>
                    </span>
                        <span>
                    In corso
                    </span>
                </a>
            </li>
            <li>
                <a class="<?= ($index_menu !== 3 ?: "is-active")?>">
                    <span class="icon">
                    <i class="fa fa-fast-forward" aria-hidden="true"></i>
                    </span>
                        <span>
                    Futuri
                    </span>
                </a>
            </li>
            <li>
                <a class="<?= ($index_menu !== 4 ?: "is-active")?>">
                    <span class="icon">
                    <i class="fa fa-stop" aria-hidden="true"></i>
                    </span>
                        <span>
                    Terminati
                    </span>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a class="<?= ($index_menu !== 5 ?: "is-active")?>">
            <span class="icon">
            <i class="fa fa-file-text" aria-hidden="true"></i>
            </span>
                <span>
            Resoconti
            </span>
        </a>
    </li>
</ul>
<p class="menu-label">
    Aziende
</p>
<ul class="menu-list">
    <li>
        <a class="<?= ($index_menu !== 6 ?: "is-active")?>">
            <span class="icon">
            <i class="fa fa-building" aria-hidden="true"></i>
            </span>
                <span>
            Lista
            </span>
        </a>
    </li>
    <li>
        <a class="<?= ($index_menu !== 7 ?: "is-active")?>">
            <span class="icon">
            <i class="fa fa-address-card" aria-hidden="true"></i>
            </span>
                <span>
            Contatti
            </span>
        </a>
    </li>
</ul>
<p class="menu-label">
    Impostazioni
</p>
<ul class="menu-list">
    <li>
        <a href="pages/docente/control_panel.php" class="<?= ($index_menu !== 8 ?: "is-active")?>">
            <span class="icon">
            <i class="fa fa-wrench" aria-hidden="true"></i>
            </span>
                <span>
            Cassetta degli strumenti
            </span>
        </a>
    </li>
</ul>