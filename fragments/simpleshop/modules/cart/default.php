<?php
use FriendsOfREDAXO\Simpleshop;

$values = $this->getVar('values');
$settings = $this->getVar('settings');
?>

<div class="row column margin-top margin-large-bottom">
    <?php if (strlen($values['title']) || strlen($values['text'])): ?>
        <div class="row column">
            <?php if (strlen($values['title'])): ?>
                <h2 class="heading large separator"><?= $values['title'] ?></h2>
            <?php endif; ?>
            <?= $values['text'] ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="column" data-cart-container>
            <?php
            $Controller = Simpleshop\CartController::execute();
            echo $Controller->parse();
            ?>
        </div>
    </div>
</div>
