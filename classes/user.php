<?php
class User{
    private $login;
    private $password;

    public function __construct(){

    }
    public static function login($data){
        if(!$data['login'] && !$data['password']){
            return false;
        }
        if( !$data['login'] || !$data['password']){
            BoardroomBooker::setMessage('Empty login or password', 'msg-error');
            return false;
        }
        $db = BoardroomBooker::getDB();
        $query = "SELECT * FROM users WHERE login=:login LIMIT 1";
        $sth = $db->prepare($query);
        $sth->execute(array(':login'=>$data['login']));
        $user = $sth->fetch(PDO::FETCH_ASSOC);
        if(!$user || $user['password'] !=  md5($data['password'])){
            BoardroomBooker::setMessage('Incorrect login or password', 'msg-error');
            return false;
        }
        $_SESSION['user'] = $user['login'];
        return true;
    }
}