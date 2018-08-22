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


class CustomerAddress extends Model
{
    const TABLE = 'rex_shop_customer_address';


    public function getName($lang_id = null)
    {
        $name = [];

        if ($this->valueIsset('company_name')) {
            $name[] = trim($this->getValue('company_name'));
        }
        if ($this->valueIsset('firstname') || $this->valueIsset('lastname')) {
            $name[] = trim($this->getValue('firstname') . ' ' . $this->getValue('lastname'));
        }
        return implode(' - ', $name);
    }

    public function getForm($yform = null, $excludedFields = [], $customerId = null)
    {
        if (\rex::isBackend()) {
            $form = parent::getForm();
        }
        else {
            $form = parent::getForm($yform, $excludedFields);
            $form->setValueField('hidden', ['customer_id', $customerId]);

            if (in_array('firstname', $excludedFields)) {
                $form->setValueField('hidden', ['firstname', rex_post('firstname', 'string')]);
            }
            if (in_array('lastname', $excludedFields)) {
                $form->setValueField('hidden', ['lastname', rex_post('lastname', 'string')]);
            }
            if (!$form->getObjectparams('main_id')) {
                $defaultStatus = self::getYformFieldByName('status')->getElement('default');
                $form->setValueField('hidden', ['status', $defaultStatus]);
            }
        }
        return $form;
    }
}