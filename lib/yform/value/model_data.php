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

        if (rex::isBackend())
        {
            $value      = $this->getValue();
            $Object     = \FriendsOfREDAXO\Simpleshop\Model::unprepare($value);
            $content    = $this->unprepare($Object);
            $attributes = $this->getAttributeElements(["name" => $this->getFieldName()]);

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
        $close_table = TRUE;
        $content     = ['<table class="table table-condensed">'];

        if (is_array($Object))
        {
            foreach ($Object as $key => $val)
            {
                if (in_array ($key, ['submit']))
                {
                    continue;
                }
                $content[] = '<tr><td>' . $key . '</td><td>' . implode('', $this->unprepare($val)) . '</td></tr>';
            }
        }
        else if (is_object($Object) && defined(get_class($Object) . "::TABLE"))
        {
            $columns = \rex_yform_manager_table::get($Object::TABLE)->getFields();

            foreach ($columns as $column)
            {
                if (in_array($column->getType(), ['validate']) || in_array($column->getTypeName(), ['php', 'html', 'hidden_input']))
                {
                    continue;
                }
                $value = $Object->getValue($column->getName());

                if (is_array($value))
                {
                    $value = implode('', $this->unprepare($value));
                }
                $content[] = '<tr><td>' . $column->getLabel() . '</td><td>' . $value . '</td></tr>';
            }
        }
        else if (is_object($Object))
        {
            $data = $Object->getData();

            foreach ($data as $name => $value)
            {
                $content[] = '<tr><td>' . $name . '</td><td>' . implode('', $this->unprepare($value)) . '</td></tr>';
            }
        }
        else
        {
            $content     = [$Object];
            $close_table = FALSE;
        }

        if ($close_table)
        {
            $content[] = '</table>';
        }
        return $content;
    }

    function getDescription()
    {
        return 'model_data|name|label|defaultwert|[no_db]|';
    }

    function getDefinitions()
    {
        $parent         = parent::getDefinitions();
        $parent['name'] = 'model_data';
        return $parent;
    }
}