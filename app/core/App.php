<?php
/**
 * Main Application Router
 */
class App {
    protected $controller = 'Home';
    protected $method = 'index';
    protected $params = [];
    
    public function __construct() {
        $url = $this->parseUrl();
        
        // Controller
        if (isset($url[0]) && !empty($url[0])) {
            $controllerName = ucfirst(strtolower($url[0]));
            if (file_exists(APP_ROOT . '/app/controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }
        
        require_once APP_ROOT . '/app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;
        
        // Method
        if (isset($url[1])) {
            $method = str_replace('-', '_', $url[1]);
            if (method_exists($this->controller, $method)) {
                $this->method = $method;
                unset($url[1]);
            }
        }
        
        // Params
        $this->params = $url ? array_values($url) : [];
        
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
    
    protected function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
