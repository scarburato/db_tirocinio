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
        <a href="<?= BASE_DIR ?>pages/azienda/index.php" class="<?= ($index_menu !== 0 ?: "is-active")?>">
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
    Imposta
</p>
<ul class="menu-list">
    <li>
        <a href="<?= BASE_DIR ?>pages/azienda/contatti/" class="<?= ($index_menu !== 1 ?: "is-active")?>">
            <span class="icon">
                <i class="fa fa-address-book" aria-hidden="true"></i>
            </span>
            <span>
                Contatti
            </span>
        </a>
    </li>
    <li>
        <a class="<?= ($index_menu !== 2 ?: "is-active")?>">
            <span class="icon">
            <i class="fa fa-file-text" aria-hidden="true"></i>
            </span>
            <span>
                Indirizzi di Studio
            </span>
        </a>
    </li>
</ul>