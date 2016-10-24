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

        $Object = \FriendsOfREDAXO\Simpleshop\Model::unprepare($this->getValue());
        $columns = \rex_yform_manager_table::get($Object::TABLE)->getFields();

        $content = [];
        foreach($columns as $column)
        {
            $content[] = '<tr><td>'. $column->getLabel() .'</td><td>'. $Object->getValue($column->getName()) .'</td></tr>';
        }

        $this->params['form_output'][$this->getId()] = '
            <div class="form-group" id="'.$this->getHTMLId().'">
                <label for="'.$this->getFieldId().'">'.$this->getLabel().'</label>
                <div class="model-data-panel"><table class="table table-hover table-condensed">'. implode('', $content) .'</table></div>
            </div>
        ';
    }

    function getDescription()
    {
        return 'model_data|name|label|defaultwert|[no_db]|';
    }

    function getDefinitions()
    {
        $parent = parent::getDefinitions();
        $parent['name'] = 'model_data';
        return $parent;
    }
}