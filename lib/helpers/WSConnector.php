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


class WSConnector
{
    const CURL_TIMEOUT         = 600;
    const CURL_CONNECT_TIMEOUT = 5;

    private $debug          = FALSE;
    private $_base_url      = '';
    private $_port          = NULL;
    private $_auth_name     = NULL;
    private $_auth_pass     = NULL;
    private $_auth_type     = CURLAUTH_ANY;
    private $_req_format    = 'application/json';
    private $_resp_format   = 'application/json';
    private $_lang          = 'en-US';
    private $_uses_gzip     = FALSE;
    private $_return_header = FALSE;

    private $url      = '';
    private $data     = [];
    private $response = [];

    public function __construct($base_url)
    {
        $this->_base_url = $base_url;
    }

    public function setPort($port)
    {
        $this->_port = $port;
        return $this;
    }

    public function setAuth($auth_name, $auth_pass)
    {
        $this->_auth_name = $auth_name;
        $this->_auth_pass = $auth_pass;
        return $this;
    }

    public function setAuthType($auth_type)
    {
        $this->_auth_type = $auth_type;
        return $this;
    }

    public function setReqFormat($req_format)
    {
        $this->_req_format = $req_format;
        return $this;
    }

    public function setRespFormat($resp_format)
    {
        $this->_resp_format = $resp_format;
        return $this;
    }

    public function setLang($lang)
    {
        $this->_lang = $lang;
        return $this;
    }

    public function setGzip($value)
    {
        $this->_uses_gzip = $value;
        return $this;
    }

    public function setReturnHeader($value)
    {
        $this->_return_header = $value;
        return $this;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
        return $this;
    }

    protected function log($code, $msg, $type, $send_email = TRUE)
    {
        $decoded_json = json_decode($this->response['response']);

        if (json_last_error() == JSON_ERROR_NONE)
        {
            $this->response['response'] = $decoded_json;
        }
        $msg = "{$msg}\n\nURL: {$this->url}\nData: " . print_r($this->data, TRUE) . "\nResponse: " . print_r($this->response, TRUE) . "\n\n";
        Utils::log($code, $msg, $type, $send_email);
    }

    public function request($path, $_data = [], $method = 'get', $name = '')
    {
        $s    = curl_init();
        $args = '';

        if ($this->_auth_name != NULL)
        {
            curl_setopt($s, CURLOPT_HTTPAUTH, $this->_auth_type);
            curl_setopt($s, CURLOPT_USERPWD, $this->_auth_name . ':' . $this->_auth_pass);
        }
        if ($method == 'post')
        {
            $this->data = $_data;
            curl_setopt($s, CURLOPT_POST, TRUE);
            curl_setopt($s, CURLOPT_POSTFIELDS, $this->data);
        }
        else if ($method == 'put')
        {
            $this->data = $_data;
            curl_setopt($s, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($s, CURLOPT_POSTFIELDS, $this->data);
        }
        else if ($method == 'get' && !empty ($_data))
        {
            $args = '?' . http_build_query($_data);
        }
        curl_setopt($s, CURLOPT_FRESH_CONNECT, TRUE);

        $this->url = $this->_base_url . $path . $args;

        $headers = [
            'Accept: ' . $this->_resp_format,
            'Accept-Language: ' . $this->_lang,
        ];

        if ($this->_req_format)
        {
            $headers = ['Content-Type: ' . $this->_req_format];
        }
        if ($this->_uses_gzip)
        {
            $headers = ['Accept-Encoding: gzip'];
        }
        curl_setopt($s, CURLOPT_URL, $this->url);
        curl_setopt($s, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($s, CURLOPT_CONNECTTIMEOUT, self::CURL_CONNECT_TIMEOUT);
        curl_setopt($s, CURLOPT_TIMEOUT, self::CURL_TIMEOUT);
        curl_setopt($s, CURLOPT_ENCODING, '');
        curl_setopt($s, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($s, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($s, CURLOPT_HEADER, $this->_return_header);
        if ($this->_port)
        {
            curl_setopt($s, CURLOPT_PORT, $this->_port);
        }

        $___response                = curl_exec($s);
        $this->response             = curl_getinfo($s);
        $__resp_status              = (int) $this->response['http_code'];
        $this->response['response'] = $___response;

        curl_close($s);

        if ($this->debug)
        {
            pr($this->url);
            pr($_data);
            pr($this->response);
        }

        $this->log($name . ' - Curl-Request', $this->response, 'INFO', FALSE);

        if ($__resp_status == 0)
        {
            if (TRUE || $this->response['total_time'] >= self::CURL_TIMEOUT)
            {
                $message = "Service did not respond within " . self::CURL_TIMEOUT . " milliseconds";
                $this->log('Request.timeout', $message, 'ERROR');
                throw new \ErrorException($message, 1);
            }
            else
            {
                $message = "The Server is not reachable";
                $this->log('Server.not-reachable', $message, 'ERROR');
                throw new \ErrorException($message, 2);
            }
        }
        else if ($__resp_status < 200 || $__resp_status > 299)
        {
            $message = "Response is a {$__resp_status} message";
            $this->log('Response.invalid-status', $message, 'ERROR');
            throw new \ErrorException($message, 3);
        }

        // everything fine - parse response
        if ($this->_resp_format == 'application/json')
        {
            $this->response['response'] = json_decode($this->response['response'], TRUE);
        }
        if ($this->debug)
        {
            pr($this->response['response']);
        }
        return $this->response;
    }
}
