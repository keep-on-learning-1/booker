<?php

class EmployeeManager{
    private $errors;
    public function __construct(){

    }
    public function validateEmployeeData($data){
        if(!is_array($data)){
            $this->errors[] = 'Wrong input data';
            return false;
        }
        if(!$data['name']){
            $this->errors[] = 'Employee name is not set';
        }
        if(!$data['email']){
            $this->errors[] = 'Employee e-mail is not set';
        }
        if( $this->errors){
            return false;
        }
        if(!preg_match('/^[a-zA-Zа-яА-Я ]+$/', $data['name'])){
            $this->errors[] = 'Employee name contains unallowed symbols';
        }
        if(!preg_match('/^[a-z0-9\-\._]+@[a-z0-9\-_]+\.[\w]{2,6}$/', $data['email'])){
            $this->errors[] = 'Incorrect e-mail';
        }
        if( $this->errors){
            return false;
        }

        $db = BoardroomBooker::getDB();
        $stmt = $db->prepare('SELECT COUNT(*) as count FROM employees WHERE email=:email');
        $res = $stmt->execute(array('email' => $data['email']));
        if(!$res){
            $this->errors[] = $stmt->errorInfo()[2];
            return false;
        }
        $count = $stmt->fetch(PDO::FETCH_COLUMN);
        if($count > 0){
            $this->errors[] = 'Employee with specified e-mail already exists in database.';
            return false;
        }
        return true;
    }

    public function getErrors(){
        return $this->errors;
    }

    /*
     * Добавление пользователя. Предполагается, что проверка данных уже была выполнена
     */
    public function addEmployee($data){

        $db = BoardroomBooker::getDB();
        $stmt = $db->prepare("INSERT INTO employees (name, email) VALUES (:name, :email)");
        $res = $stmt->execute(array('name'=>$data['name'], 'email'=>$data['email']));
        if(!$res){
            $this->errors[] = $stmt->errorInfo()[2];
            return false;
        }
        return true;
    }

    public function editEmployee(){

    }

    public function deleteEmployee($id){
        //var_dump($id);

    }
}