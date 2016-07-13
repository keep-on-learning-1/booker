<?php
class Autoloader{
    private static $instance;
    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct(){}

    public function registerLoaders(){
        spl_autoload_register(array($this, 'loadClasses'));
        spl_autoload_register(array($this, 'loadModels'));
        spl_autoload_register(array($this, 'loadControllers'));
    }

    public function loadClasses($className){
        if(file_exists(BASE_PATH.'app/'.lcfirst($className).'.php')){
            include_once(BASE_PATH.'app/'.lcfirst($className).'.php');
        }
    }

    public function loadModels($className){
        if(file_exists(BASE_PATH.'app/model/'.lcfirst($className).'.php')){
            include_once(BASE_PATH.'app/model/'.lcfirst($className).'.php');
        }
    }
    public function loadControllers($className){
        if(file_exists(BASE_PATH.'app/controller/'.lcfirst($className).'.php')){
            include_once(BASE_PATH.'app/controller/'.lcfirst($className).'.php');
        }
    }
}