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
class rex_yform_value_model_data extends rex_yform_value_textarea
{

    public function enterObject()
    {
        parent::enterObject();

        if (rex::isBackend()) {
            $value       = $this->getValue();
            $Object      = \FriendsOfREDAXO\Simpleshop\Model::unprepare($value);
            $content     = $this->unprepare($Object);
            $attributes  = $this->getAttributeElements(["name" => $this->getFieldName()]);
            $content_cnt = count($content);

            if ($content_cnt <= 2) {
                if ($content_cnt <= 1) {
                    $content = ['<table class="table table-condensed">'];
                }
                else if ($content_cnt == 2) {
                    array_pop($content);
                }
                $content[] = '<tr><td><i class="text-muted">'. rex_i18n::msg('list_no_rows') .'</i></td></tr>';
                $content[] = '</table>';
            }
            $this->params['form_output'][$this->getId()] = '
                <div class="form-group" id="' . $this->getHTMLId() . '">
                    <label for="' . $this->getFieldId() . '">' . $this->getLabel() . '</label>
                    <div class="model-data-panel">' . \Sprog\Wildcard::parse(implode('', $content)) . '</div>
                    <input type="hidden" ' . implode(" ", $attributes) . ' value="' . htmlspecialchars($this->getValue()) . '"/>        
                </div>
            ';
        }
    }

    private function unprepare($Object)
    {
        $close_table = true;
        $content     = ['<table class="table table-condensed">'];

        if (!is_object($Object) && is_array($Object)) {
            foreach ($Object as $key => $val) {
                if (!is_numeric($key) && in_array($key, ['submit'])) {
                    continue;
                }
                else {
                    $content[] = '<tr><td>' . (is_numeric($key) ? 'index: ' . $key : $key) . '</td><td>' . implode('', $this->unprepare($val)) . '</td></tr>';
                }
            }
        }
        else if (is_object($Object) && defined(get_class($Object) . "::TABLE")) {
            $columns = \rex_yform_manager_table::get($Object::TABLE)->getFields();

            foreach ($columns as $column) {
                if (in_array($column->getType(), ['validate']) || in_array($column->getTypeName(), ['php', 'html', 'hidden_input'])) {
                    continue;
                }
                $value = $Object->getValue($column->getName());

                if (is_array($value)) {
                    $value = implode('', $this->unprepare($value));
                }
                $content[] = '<tr><td>' . $column->getLabel() . '</td><td>' . $value . '</td></tr>';
            }
        }
        else if (is_object($Object)) {
            $data = $Object->getData();

            foreach ($data as $name => $value) {
                $content[] = '<tr><td>' . $name . '</td><td>' . implode('', $this->unprepare($value)) . '</td></tr>';
            }
        }
        else {
            $content     = [$Object];
            $close_table = false;
        }

        if ($close_table) {
            $content[] = '</table>';
        }
        return $content;
    }

    function getDescription()
    {
        return 'model_data|name|label|defaultwert|[no_db]|';
    }

    function getDefinitions($values = [])
    {
        $parent         = parent::getDefinitions();
        $parent['name'] = 'model_data';
        return $parent;
    }
}