<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 20.02.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class rex_yform_value_hash_productcode extends rex_yform_value_hashvalue
{

    public function enterObject()
    {
        if ($this->getValue() == '') {
            $hash = $this->params['value_pool']['sql'][$this->getName()];

            if ($hash == '') {
                $hash = $this->createGuid();
            }
            $this->setValue($hash);
        }

        $this->params['value_pool']['email'][$this->getName()] = $this->getValue();

        if ($this->saveInDb()) {
            $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
        }

        $this->params['form_output'][$this->getId()] = '<input type="hidden" name="'. $this->getFieldName() .'" value="'. $this->getValue() .'"/>';
    }

    public function getDefinitions($values = [])
    {
        return [
            'type' => 'value',
            'name' => 'hash_productcode',
            'values' => [
                'name' => ['type' => 'name',    'label' => rex_i18n::msg('yform_values_defaults_name')],
                'no_db' => ['type' => 'no_db',  'label' => rex_i18n::msg('yform_values_defaults_table')],
            ],
            'description' => 'Erstellt automatisch einen Produktcode',
            'db_type' => ['text', 'varchar(191)'],
            'multi_edit' => false,
        ];
    }

    public function createGuid()
    {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_srand(crc32(serialize([microtime(true), 'ETC'])))
        );
    }
}