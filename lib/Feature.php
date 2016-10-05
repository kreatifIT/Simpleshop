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
    private $values = NULL;

    public static function getFeatureByKey($key)
    {
        return self::query()->where('key', $key)->findOne();
    }

    public function getValues()
    {
        if ($this->values === NULL)
        {
            $_values = strlen($this->getValue('values')) ? explode(',', $this->getValue('values')) : [];

            foreach ($_values as $value)
            {
                $this->values[] = FeatureValue::get($value);
            }
        }
        return $this->values;
    }
}