<?php
// Simple front-controller router: /controller/action/param1/param2/...

class App
{
    protected $controller = DEFAULT_CONTROLLER;
    protected $action     = DEFAULT_ACTION;
    protected $params     = array();

    public function __construct()
    {
        $url = $this->parseUrl();

        // Resolve controller
        if (!empty($url[0])) {
            $candidate = ucfirst(strtolower($url[0])) . 'Controller';
            $file = APP_ROOT . '/app/controllers/' . $candidate . '.php';
            if (file_exists($file)) {
                $this->controller = $candidate;
                array_shift($url);
            } else {
                $this->controller = ucfirst(DEFAULT_CONTROLLER) . 'Controller';
            }
        } else {
            $this->controller = ucfirst(DEFAULT_CONTROLLER) . 'Controller';
        }

        require_once APP_ROOT . '/app/controllers/' . $this->controller . '.php';
        $instance = new $this->controller;

        // Resolve action
        if (!empty($url[0])) {
            if (method_exists($instance, $url[0])) {
                $this->action = $url[0];
                array_shift($url);
            }
        }

        if (!empty($url)) {
            $this->params = $url;
        } else {
            $this->params = array();
        }

        call_user_func_array(array($instance, $this->action), $this->params);
    }

    private function parseUrl()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = preg_replace('/[^a-zA-Z0-9\/_\-]/', '', $url);
            return explode('/', $url);
        }
        return array();
    }
}
