<?php
/*
 * Methods:
 *   - validateEmployeeData
 *   - getErrors
 *   - addEmployee
 *   - editEmployee
 *   - deleteEmployee
 *   - getById
 */

class EmployeeModel{
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

        $stmt = $db->prepare("INSERT INTO employees (name, email) VALUES (:name, :email)");
        $res = $stmt->execute(array('name'=>$data['name'], 'email'=>$data['email']));
        if(!$res){
            $this->errors[] = $stmt->errorInfo()[2];
            return false;
        }
        return true;
    }

    public function editEmployee($id, $data){
        if(!$id || !is_numeric($id)){return false;}
        $db = BoardroomBooker::getDB();
        $stmt = $db->prepare("UPDATE employees SET name=:name, email=:email WHERE id=:id");
        $res = $stmt->execute(array('name'=>$data['name'], 'email'=>$data['email'],'id'=>(string)$id));
        if(!$res){
            $this->errors[] = $stmt->errorInfo()[2];
            return false;
        }
        return true;
    }

    public function deleteEmployee($id){
        if(!$id || !is_numeric($id)){return false;}

        $employee = $this->getById($id);
        if(!$employee){
            //$this->errors[] = 'Employee with specified id does not exist.';
            return false;
        }

        $db = BoardroomBooker::getDB();
        $stmt = $db->prepare("DELETE FROM employees WHERE id=:id");
        $res = $stmt->execute(array('id'=>$id));
        if(!$res){
            $this->errors[] = $stmt->errorInfo()[2];
            return false;
        }
        return $employee;
    }

    public function getById($id){
        if(!$id || !is_numeric($id)){return false;}

        $db = BoardroomBooker::getDB();

        $stmt = $db->prepare('SELECT * FROM employees WHERE id=:id');
        $res = $stmt->execute(array('id' =>$id));
        if(!$res){
            $this->errors[] = $stmt->errorInfo()[2];
            return false;
        }
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$employee){
            $this->errors[] = 'Employee with specified id does not exist.';
            return false;
        }
        return $employee;
    }

    public static function getEmployeeList(){
        $db = BoardroomBooker::getDB();
        $res = $db->query('SELECT * FROM employees');
        if(!$res){
            BoardroomBooker::setMessage('getEmployeeList: '.$db->errorInfo()[2], 'msg-error');
            return false;
        }
        $employees_list = $res->fetchAll(PDO::FETCH_ASSOC);
        return $employees_list;
    }
}