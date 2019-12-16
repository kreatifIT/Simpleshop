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
class rex_api_simpleshop_be_api extends rex_api_function
{
    public    $response  = [];
    public    $request   = [];
    public    $errors    = [];
    public    $success   = true;
    protected $published = true;

    public static $inst = null;

    public function execute()
    {
        $controller = rex_request('controller', 'string', null);

        list ($class, $method) = explode('.', $controller);

        $this->request['class']   = $class;
        $this->request['method']  = $method;
        $this->request['debug']   = rex_request('debug', 'bool', false);
        $this->request['lang_id'] = rex_request('lang-id', 'int', rex_request('lid', 'int', 1));
        $this->request['version'] = rex_request('version', 'string', 'latest');
        $this->response['method'] = strtolower($this->request['method']);

        try {
            if (!$this->request['debug']) {
                ob_start();
            }

            $class = $class == 'BeApi' ? get_class() : '\\FriendsOfREDAXO\\Simpleshop\\' . $class;

            if (!method_exists($class, $method)) {
                throw new ApiException("Controller '{$class}.{$method}' doesn't exist");
            }
            else if (!rex::getUser()) {
                throw new ApiException("You must be logged in");
            }

            // do some param parsing/preparing
            $this->request = array_merge($_REQUEST, $this->request);
            self::$inst    = $this;
            $response      = $class::$method();

            if ($response) {
                if ($response instanceof \rex_yform_manager_collection) {
                    $this->response['data'] = [];

                    foreach ($response as $row) {
                        $this->response['data'][] = $row->getData();
                    }
                } else {
                    $this->response = array_merge($response, $this->response);
                }
            }

            if (!$this->request['debug']) {
                ob_clean();
            } else {
                $this->response['request'] = $this->request;
            }
        } catch (\ErrorException $ex) {
            throw new ApiException($ex->getMessage());
        } catch (\rex_sql_exception $ex) {
            throw new ApiException($ex->getMessage());
        }

        $this->response['errors'] = $this->errors;

        if (count($this->response['errors'])) {
            $this->success = false;
        }
        return new \rex_api_result($this->success, $this->response);
    }

    public static function list_functions()
    {
        $fragment = new rex_fragment();

        self::$inst->response['html'] = $fragment->parse('simpleshop/backend/list_functions/' . self::$inst->request['fragment'] . '.php');
    }

    public static function search_product()
    {
        $result = [];
        $steps  = 20;
        $offset = rex_get('page', 'int', 0) * $steps;
        $label  = 'name_' . self::$inst->request['lang_id'];
        $stmt   = \FriendsOfREDAXO\Simpleshop\Product::query()
            ->resetSelect()
            ->selectRaw('id, ' . $label . ' AS label')
            ->where('status', 1)
            ->whereRaw($label . ' LIKE :term OR code = :code', [
                'term' => '%' . self::$inst->request['term'] . '%',
                'code' => self::$inst->request['term'],
            ])
            ->orderBy($label);

        $count      = $stmt->count();
        $collection = $stmt->limit($offset, $steps)
            ->find();

        foreach ($collection as $item) {
            $result[] = [
                'id'   => $item->getId(),
                'text' => $item->getValue('label'),
            ];
        }

        self::$inst->response['result'] = [
            'results'    => $result,
            'pagination' => ['more' => $count > $offset + $steps],
        ];
    }

}


