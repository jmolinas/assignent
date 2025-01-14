<?php

class Http
{

    protected $url;

    protected $request;

    public function __construct()
    {
        $this->request = $_SERVER;
        $this->url = !empty($_SERVER['REQUEST_URL']) ? $_SERVER['REQUEST_URL'] : $_SERVER['REQUEST_URI'];
    }

    public function getRequestHeaders()
    {
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if ($key != 'CONTENT_TYPE') {
                if (substr($key, 0, 5) <> 'HTTP_') {
                    continue;
                }
            }
            $trim = $key != 'CONTENT_TYPE' ? strtolower(substr($key, 5)) : strtolower($key);
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', $trim)));
            $headers[$header] = $value;
        }
        return $headers;
    }

    public function imput($key = null)
    {
        $header = $this->getRequestHeaders();
        if (isset($header['Content-Type']) && $header['Content-Type'] == 'application/json') {
            $data = json_decode(file_get_contents('php://input'), true);
            return $key !== null ? $data[$key] : $data;
        }
    }


    public function getUrl($method = 'GET')
    {
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            return false;
        }
        // The request url might be /project/index.php, this will remove the /project part
        $url = (php_sapi_name() !== 'cli-server') ? str_replace(dirname($this->request['SCRIPT_NAME']), '', $this->url) : $this->url;
        // Remove the query string if there is one

        $queryString = strpos($url, '?');

        if ($queryString !== false) {
            $url = substr($url, 0, $queryString);
        }

        // If the URL looks like http://localhost/index.php/path/to/folder remove /index.php
        if (php_sapi_name() !== 'cli-server') {
            if (substr($url, 1, strlen(basename($this->request['SCRIPT_NAME']))) == basename($this->request['SCRIPT_NAME'])) {
                $url = substr($url, strlen(basename($this->request['SCRIPT_NAME'])) + 1);
            }
            $url = '/' . rtrim($url, '/');
        }

        // Make sure the URI ends in a /
        $url = rtrim($url, '/') . '/';

        // Replace multiple slashes in a url, such as /my//dir/url
        $url = preg_replace('/\/+/', '/', $url);
        return $url;
    }

    public function redirect($uri)
    {
        header("Location: {$uri}"); /* Redirect browser */
    }
}
