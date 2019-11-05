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

    public static function be__searchAddress()
    {
        $term = \rex_api_simpleshop_be_api::$inst->request['term'];
        $stmt = self::query();
        $stmt->alias('m');
        $stmt->resetSelect();
        $stmt->selectRaw('CONCAT("KUNDE: [ID=", jt1.id, "] ", IF(jt1.company_name != "", jt1.company_name, TRIM(CONCAT(jt1.firstname, " ", jt1.lastname))), " --> ADRESSE: [ID=", m.id, "] ", IF(m.company_name != "", m.company_name, TRIM(CONCAT(m.firstname, " ",m.lastname))), " | ", m.street, " | ", m.postal, " ", m.location) AS text, m.id');
        $stmt->leftJoin(Customer::TABLE, 'jt1', 'jt1.id', 'm.customer_id');
        $stmt->orderBy('jt1.id');
        $stmt->orderBy('m.id');
        $stmt->orderBy('m.id');
        $stmt->whereRaw('(
            m.company_name LIKE :term
            OR m.firstname LIKE :term
            OR m.lastname LIKE :term
            OR m.street LIKE :term
            OR m.location LIKE :term
            OR m.id = :id
            OR m.customer_id = :id
            OR jt1.company_name LIKE :term
            OR jt1.firstname LIKE :term
            OR jt1.lastname LIKE :term
        )', ['term' => "%{$term}%", 'id' => $term]);
        $collection = $stmt->find();

        $result = [];
        foreach ($collection as $item) {
            $result[] = $item->getData();
        }
        \rex_api_simpleshop_be_api::$inst->response['results'] = $result;
    }
}