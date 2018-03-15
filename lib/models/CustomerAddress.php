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
            \rex_extension::register('YFORM_DATASET_FORM_SETVALIDATEFIELD', function (\rex_extension_point $Ep) {
                $subject = $Ep->getSubject();
                $Object  = $Ep->getParam('object');

                if (!\rex::isBackend() && $Object->getTable()->getTableName() == self::TABLE) {
                    $excluded = array_merge(['customer_id'], (array) $Ep->getParam('special_fields'));

                    if (in_array($subject[0], $excluded)) {
                        $subject = false;
                    }
                }
                return $subject;
            });
            \rex_extension::register('YFORM_DATASET_FORM_SETVALUEFIELD', function (\rex_extension_point $Ep) {
                $subject = $Ep->getSubject();
                $Object  = $Ep->getParam('object');

                if (!\rex::isBackend() && $Object->getTable()->getTableName() == self::TABLE) {
                    $type     = $Ep->getParam('type_name');
                    $excluded = array_merge(['status', 'customer_id'], (array) $Ep->getParam('special_fields'));

                    if ($type == 'hidden_field' || in_array($subject[0], $excluded)) {
                        $subject = false;
                    }
                    else {
                        $subject['css_class'] = 'column ' . FragmentConfig::getValue('customer.css_class');
                    }
                }
                return $subject;
            });
            $form = parent::getForm($yform, $excludedFields);
            $form->setValueField('hidden', ['customer_id', $customerId]);
        }
        return $form;
    }

    //    public static function action__save_checkout_address($action)
    //    {
    //        $addresses  = [];
    //        $CAddresses = [];
    //        $extras     = [];
    //        $values     = $action->getParam('value_pool');
    //        $hidden     = $action->getParam('form_hiddenfields');
    //        $user_id    = $hidden['customer_id'] ?: 0;
    //
    //        if (!\rex_extension::registerPoint(new \rex_extension_point('simpleshop.CustomerAddress.verifyAddressValues', true, [
    //            'values'  => $values,
    //            'hidden'  => $hidden,
    //            'user_id' => $user_id,
    //        ]))
    //        ) {
    //            return false;
    //        }
    //
    //        foreach ($values['sql'] as $name => $value) {
    //            list ($key, $index, $_name) = explode('.', $name);
    //
    //            if (strlen($name) && $key == 'customer_address') {
    //                $addresses[$index][$_name] = $value;
    //            }
    //            else if (strlen($name)) {
    //                $extras[$name] = $value;
    //            }
    //        }
    //
    //        foreach ($addresses as $index => $address) {
    //            $_this = (int) $hidden['id_' . $index] ? self::get($hidden['id_' . $index]) : self::create();
    //            $_this->setValue('customer_id', $user_id);
    //
    //            foreach ($address as $name => $value) {
    //                $_this->setValue($name, $value);
    //            }
    //            $_this = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.CustomerAddress.preSaveAddress', $_this, [
    //                'hidden'  => $hidden,
    //                'values'  => $values,
    //                'extras'  => $extras,
    //                'address' => $address,
    //                'user_id' => $user_id,
    //            ]));
    //            if ($user_id) {
    //                // just save for registered user
    //                $_this->save();
    //            }
    //            $CAddresses[$index] = $_this;
    //        }
    //        \rex_extension::registerPoint(new \rex_extension_point('simpleshop.CustomerAddress.addresses_saved', $CAddresses, [
    //            'hidden'  => $hidden,
    //            'values'  => $values,
    //            'extras'  => $extras,
    //            'user_id' => $user_id,
    //        ]));
    //    }
}