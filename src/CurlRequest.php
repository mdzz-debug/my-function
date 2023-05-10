<?php

namespace mdzz\CurlRequest;

use Exception;

class CurlRequest
{
    protected $url;
    protected $method = 'GET';
    protected $headers = [];
    protected $data = [];
    protected $curl;
    protected static $methods = ['GET', 'POST', 'PUT', 'DELETE'];

    public function __construct($url, $method = 'GET', $data = [], $headers = [])
    {
        $this->url = $url;
        if (in_array(strtoupper($method), self::$methods)) {
            $this->method = strtoupper($method);
        }
        $this->data = $data;
        $this->headers = $headers;
        $this->curl = curl_init();
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setMethod($method)
    {
        if (in_array(strtoupper($method), self::$methods)) {
            $this->method = strtoupper($method);
        }
    }

    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @throws Exception
     */
    public function send()
    {
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $this->method);
        if (!empty($this->headers)) {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->parseHeaders());
        }
        if (in_array($this->method, ['POST', 'PUT', 'DELETE'])) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->parseData());
        }
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($this->curl);
        if ($response === false) {
            throw new Exception(curl_error($this->curl));
        }
        curl_close($this->curl);
        return $response;
    }

    protected function parseHeaders()
    {
        $result = [];
        if (!empty($this->headers)) {
            foreach ($this->headers as $key => $value) {
                $result[] = sprintf('%s: %s', $key, $value);
            }
        }
        return $result;
    }

    protected function parseData()
    {
        if (is_array($this->data)) {
            return http_build_query($this->data);
        } else {
            return $this->data;
        }
    }
}