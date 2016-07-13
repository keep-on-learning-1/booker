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
        //header('Location: ' .BoardroomBooker::getBaseURL() );

        parse_str($_SERVER['QUERY_STRING'], $vars);
        $this->query_vars = $vars;

        $controller_name = $vars['controller'];
        $action_name = $vars['action'];

        /* Check configuration file existence*/
        if(!file_exists(BASE_PATH.'/booker.conf'))
        {
            $controller = new CommandController();
            $controller->setupDatabase($_POST);
            die;
        }
        /* Check accessibility of configuration file*/
        $config = BoardroomBooker::getConfig();
        if(!$config){
            BoardroomBooker::setMessage("Can't read configuration file");
            $controller = new CommandController();
            $controller->error();
            die;
        }

        /* Check configuration options*/
        if(	!$config['database']['db_name'] ||
            !$config['database']['db_host'] ||
            !$config['database']['db_user']
        ){
            /*Did not found one or more options*/
            $controller = new CommandController();
            $controller->setupDatabase($_POST);
            die;
        }

        /* Attempt to connect to database*/
        $db = BoardroomBooker::getDB();
        if(!$db){
            $controller = new CommandController();
            $controller->error();
            die;
        }

        /* At least one user necessarry  */
        $users_count = UserModel::getCount();
        if($users_count == 0){
            $controller = new CommandController();
            $controller->createUser();
            die;
        }

        /* Check Booker configuration*/
        if(!$config['booker']){
            $controller = new CommandController();
            $controller->setupBooker();
            die;
        }

        /* Check if user authorized*/
        if(!$_SESSION['user'] && $action_name != 'login' ){
            $controller = new CommandController();
            $controller->login();
        }

        /*Default controller and action names*/
        if(!$controller_name){$controller_name = 'command';}
        if(!$action_name){$action_name = 'main';}

        /*Execution of requested action*/
        $class_name = ucfirst($controller_name).'Controller';
        if(!class_exists($class_name)){
            throw new Exception("Controller {$class_name} does not exist");
        }
        $controller = new $class_name();
        $controller->$action_name();
    }

    private function getQueryVars(){
        if(!$this->query_vars){
            return array();
        }
        return $this->query_vars;
    }
}