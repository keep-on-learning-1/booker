<?php
class FrontController{
    private static $instance;
    private function __construct(){}
    public $query_vars;

    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function route(){
        parse_str($_SERVER['QUERY_STRING'], $vars);
        $this->query_vars = $vars;

        $controller_name = $vars['controller'];
        $action_name = $vars['action'];

        if(!$controller_name){$controller_name = 'main';}
        if(!$action_name){$action_name = 'main';}

        $controller = new $controller_name();
        $controller->$action_name();
    }

    public function getQueryVars(){
        if(!$this->query_vars){
            return array();
        }
        return $this->query_vars;
    }

}