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


use Kreatif\Model;


class CustomerAddress extends Model
{
    const TABLE = 'rex_shop_customer_address';


    public function getName($lang_id = null, $companyFallback = false)
    {
        $name  = [];
        $ctype = $this->getValue('ctype');
        if (in_array($ctype, ['company', '']) && trim($this->getValue('company_name')) != '') {
            $name[] = trim($this->getValue('company_name'));
        }
        if (in_array($ctype, ['person', ''])) {
            $_name = trim($this->getValue('firstname') . ' ' . $this->getValue('lastname'));

            if ($_name != '') {
                $name[] = $_name;
            } else if ($companyFallback && empty($name)) {
                $name[] = trim($this->getValue('company_name'));
            }
        }
        return implode(' - ', $name);
    }

    public function isCompany()
    {
        return $this->getValue('ctype') == 'company';
    }

    public function getCountry()
    {
        return Model\Country::get($this->getValue('country'));
    }

    public function isTaxFree()
    {
        if ($this->isCompany()) {
            $countryId = $this->getValue('country');
            $Country   = $countryId ? Model\Country::get($countryId) : null;
            $result    = $Country && !$Country->getValue('b2b_has_tax');
        } else {
            $result = false;
        }
        return $result;
    }

    public function toAddressArray($asInvoiceString = false)
    {
        $countryId = (int)$this->getValue('country');
        $Country   = $countryId > 0 ? Model\Country::get($countryId) : null;

        $location = [
            $this->getValue('postal'),
            $this->getValue('location'),
        ];
        if ($this->valueIsset('province')) {
            $location[] = '- ' . $this->getValue('province');
        }
        $output = [
            'name'              => trim($this->getName()),
            'street'            => trim($this->getValue('street')),
            'street_additional' => $asInvoiceString ? '' : trim($this->getValue('street_additional')),
            'location'          => trim(implode(' ', $location)),
            'country'           => $Country ? trim($Country->getName()) : '',
            'fiscal_code'       => $asInvoiceString && in_array($this->getValue('ctype'), ['company', 'person']) ? trim($this->getValue('fiscal_code')) : '',
            'vat_num'           => $asInvoiceString && $this->getValue('ctype') == 'company' ? trim($this->getValue('vat_num')) : '',
        ];
        return array_filter($output);
    }

    public function getForm($yform = null, $excludedFields = [], $customerId = null)
    {
        if (\rex::isBackend()) {
            $form = parent::getForm();
        } else {
            $form = parent::getForm($yform, $excludedFields);
            $form->setValueField('hidden', ['customer_id', $customerId]);

            if (in_array('firstname', $excludedFields)) {
                $form->setValueField('hidden', ['firstname', rex_post('firstname', 'string')]);
            }
            if (in_array('lastname', $excludedFields)) {
                $form->setValueField('hidden', ['lastname', rex_post('lastname', 'string')]);
            }
            if (!$form->getObjectparams('main_id')) {
                $defaultStatus = self::getYformFieldByName('status')
                    ->getElement('default');
                $form->setValueField('hidden', ['status', $defaultStatus]);
            }
        }
        return $form;
    }

    public function getNameForOrder()
    {
        $customer    = Customer::get($this->getValue('customer_id'));
        $addressInfo = array_unique(array_filter([
            $this->getName(null, true),
            $this->getValue('street'),
            $this->getValue('postal'),
            $this->getValue('location'),
        ]));
        return "KUNDE: {$customer->getName(null, true)} ---> ADRESSE: " . implode(' | ', $addressInfo);
    }

    public static function be__searchAddress()
    {
        $term       = \rex_api_simpleshop_be_api::$inst->request['term'];
        $customerId = \rex_api_simpleshop_be_api::$inst->request['customer_id'];

        $stmt = self::query();
        $stmt->alias('m');
        $stmt->resetSelect();
        $stmt->select(['m.id', 'm.firstname', 'm.lastname', 'm.company_name', 'm.street', 'm.postal', 'm.location', 'm.customer_id']);
        $stmt->join(Customer::TABLE, 'jt1', 'jt1.id', 'm.customer_id');
        $stmt->orderBy('jt1.id');
        $stmt->orderBy('m.id');

        if ($term == '' && $customerId > 0) {
            $stmt->where('m.customer_id', $customerId);
        } else {
            $term    = trim($term);
            $orWhere = [
                'm.company_name LIKE :term',
                'm.firstname LIKE :term',
                'm.lastname LIKE :term',
                'm.street LIKE :term',
                'm.location LIKE :term',
                'm.location LIKE :term',
                'm.id = :id',
                'm.customer_id = :id',
            ];
            if (Customer::getYformFieldByName('company_name')) {
                $orWhere[] = 'jt1.company_name LIKE :term';
            }
            if (Customer::getYformFieldByName('firstname')) {
                $orWhere[] = 'jt1.firstname LIKE :term';
            }
            if (Customer::getYformFieldByName('lastname')) {
                $orWhere[] = 'jt1.lastname LIKE :term';
            }
            $stmt->whereRaw('(' . implode(' OR ', $orWhere) . ')', ['term' => "%{$term}%", 'id' => $term]);
        }
        $collection = $stmt->find();

        $result = [];
        foreach ($collection as $item) {
            $result[] = [
                'id'   => $item->getId(),
                'text' => $item->getNameForOrder(),
            ];
        }
        \rex_api_simpleshop_be_api::$inst->response['results'] = $result;
    }

    public static function ext__processSettings(\rex_extension_point $ep)
    {
        $sql    = \rex_sql::factory();
        $option = Settings::getValue('customer_addresses_setting');
        $yTable = \rex_yform_manager_table::get(Coupon::TABLE);

        $sql->setTable('rex_yform_field');
        if ($option == 'disabled') {
            $sql->setValue('type_name', 'hidden_input');
            $sql->setValue('list_hidden', 1);
        } else {
            $sql->setValue('type_name', 'be_manager_relation');
            $sql->setValue('list_hidden', 0);
        }
        $sql->setWhere([
            'table_name' => Customer::TABLE,
            'name'       => 'addresses',
        ]);
        $sql->update();

        \rex_yform_manager_table_api::generateTableAndFields($yTable);
    }
}