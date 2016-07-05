<?php
class CommandController{
    private $booker;
    public function __construct($booker){
        $this->booker = $booker;
    }

    public function index(){
        $this->booker->page = 'main';
        return true;
    }
    public function login(){
        User::login($_POST);
        return true;
    }
    public function logout(){
        return true;
    }
    public function __call($name, $args){
        return false;
    }
}