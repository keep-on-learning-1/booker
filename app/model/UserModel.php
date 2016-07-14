<?php

/*
 * Methods
 * Static
 *  - login             - Log user in the application
 *  - logout            - Log current user out of system
 *  - getCount          - Return total number of users in database.
 *  - getUser           - Return name of current user or false if no one is logged in.
 *  - checkToken        - Compare given token with generated on user user creation page.
 *
 * Public
 *  - validateFormInput -
 *  - create            -
 *  - getErrors         - Return an array of errors
 */
class UserModel{
    private $errors;

    public function __construct(){}

    /*
     * Check given login and password. Log user in the application if they are correct.
     * Store username into $_SESSION.
     *
     * input: array('login', 'password')
     * return: boolean
     */
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

    /*
     *  Log current user out of system
     */
    public static function logout(){
        unset($_SESSION['user']);
        return true;
    }

    /*
     * Return total number of users in database.
     */
    public static function getCount(){
        $db = BoardroomBooker::getDB();
        return $db->query('SELECT COUNT(*) as count FROM users')->fetch(PDO::FETCH_COLUMN);
    }

    /*
     * Return name of current user or false if no one is logged in.
     *
     * return string $name|false
     */
    public static function getUser(){
        return (isset($_SESSION['user']))?$_SESSION['user']:false;
    }

    /*
     * Compare given token with generated on user user creation page.
     *
     * return boolean
     */
    public static function checkToken($data){
        if(!$_COOKIE['create_user_csrf'] || !$data['token'] || $data['token'] != $_COOKIE['create_user_csrf'] ){
            return false;
        }
        setcookie('create_user_csrf', '', time() - 3600);
        return true;
    }

    /*
     * Check correctness of form data
     *
     * input: array('login','password','confirm_password')
     *
     * return boolean
     */
    public function validateFormInput($data){
        if( !$data['login'] || !$data['password'] || !$data['confirm_password']){
            $this->errors[] = ('Required data are missed');
            return false;
        }
        if( $data['password'] != $data['confirm_password']){
            $this->errors[] = 'Password doesn\'t match confirmation';
            return false;
        }
        if(!preg_match('/^[a-zA-Z\d\-_@]*$/', $data['login'])){
            $this->errors[] = 'Login contains unallowed characters.';
            return false;
        }
        if(strlen($data['login'])<4){
            $this->errors[] = 'Login must contain at least 4 symbols.';
            return false;
        }
        if(strlen($data['login'])>50){
            $this->errors[] = 'Login can\'t be longer than 50 symbols.';
            return false;
        }
        return true;
    }

    /*
     * Create a record in 'users' table
     *
     * input: array('login','password')
     *
     * return boolean
     */
    public function create($data){
        $db = BoardroomBooker::getDB();

        $query = "INSERT INTO `users` (`login`, `password`) VALUES(:login, :password)";
        $stmt = $db->prepare($query);
        $res = $stmt->execute(array(':login'=>$_POST['login'], ':password'=>md5($_POST['password'])));
        if(!$res){
            $this->errors = $stmt->errorInfo()[2];
            return false;
        }
        return true;

    }
    public function getErrors(){
        if(!$this->errors){$this->errors = array();}
        return $this->errors;
    }
}