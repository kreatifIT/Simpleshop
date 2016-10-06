<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author FriendsOfREDAXO
 * @author a.platter@kreatif.it
 * @author jan.kristinus@yakamara.de
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

$product = $this->getVar('product');

?>
<tr class="cart-item">
    <td>
        <div><img src="http://placehold.it/200x160" alt="Cart"></div>
    </td>
    <td>
        <h3>Lorem ipsum dolor sit.</h3>
        <p>Langzeitrasendünger</p>
        <span>Ral 3000 / Rubinrot</span>
        <span>10,00 KG</span>
    </td>
    <td>€ 177,00</td>
    <td>
        <div class="amount-increment clearfix">
            <button class="button minus">-</button>
            <input type="text" value="1" readonly>
            <button class="button plus">+</button>
            <button class="button refresh"><i class="fa fa-refresh" aria-hidden="true"></i></button>
        </div>
    </td>
    <td>€ 177,00</td>
    <td>
        <button class="remove">X</button>
    </td>
</tr>