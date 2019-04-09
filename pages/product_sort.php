<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 09.04.19
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

$catId = rex_get('id', 'int');

if (\rex_request::isXmlHttpRequest()) {
    \rex_response::cleanOutputBuffers();

    $sql    = \rex_sql::factory();
    $dataid = rex_post('data_id', 'int');
    $nextId = rex_post('next_id', 'int');

    if ($nextId) {
        $prio = ProductHasCategory::get($nextId)
            ->getValue('prio');
    } else {
        $prio = ProductHasCategory::query()
            ->orderBy('prio', 'desc')
            ->findOne()
            ->getValue('prio');
    }

    $query = "
        UPDATE " . ProductHasCategory::TABLE . "
        SET prio = {$prio}
        WHERE id = :id
    ";
    $sql->setQuery($query, ['id' => $dataid]);


    $order = $nextId ? '0, 1' : '1, 0';
    $query = "
        UPDATE " . ProductHasCategory::TABLE . "
        SET `prio` = (SELECT @count := @count + 1)
        WHERE category_id = :catId
        ORDER BY `prio`, IF(`id` = :id, {$order})
    ";
    $sql->setQuery('SET @count = 0');
    $sql->setQuery($query, [
        'id'    => $dataid,
        'catId' => $catId,
    ]);
    exit;
}


$Category = Category::get($catId);

if (!$Category) {
    $Category = \rex_article::get($catId);
}


$stmt = Product::query()
    ->alias('m')
    ->select('jt1.id', 'relation_id')
    ->join(ProductHasCategory::TABLE, 'jt1', 'jt1.product_id', 'm.id')
    ->where('jt1.category_id', $catId)
    ->where('m.status', 1)
    ->orderBy('prio');

$collection = $stmt->find();


$fragment = new \rex_fragment();
$fragment->setVar('cat_name', $Category->getName());
$fragment->setVar('catId', $catId);
$fragment->setVar('collection', $collection);
echo $fragment->parse('simpleshop/backend/product_sort.php');
