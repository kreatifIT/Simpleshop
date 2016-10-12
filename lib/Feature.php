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

class Feature extends \rex_yform_manager_dataset
{
    const TABLE = 'rex_shop_feature';
    private $values = NULL;

    public static function getFeatureByKey($key)
    {
        return self::query()->where('key', $key)->findOne();
    }

    public function getValues($ignore_offline = TRUE)
    {
        if ($this->values === NULL)
        {
            $this->values = FeatureValue::query()
                ->where('feature_id', $this->getValue('id'))
                ->where('status', (int) $ignore_offline)
                ->find();
        }
        return $this->values;
    }
}