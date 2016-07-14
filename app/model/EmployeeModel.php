<?php
/*
 * Methods:
 *   - validateEmployeeData - validating form data
 *   - getErrors            - returns array of errors
 *   - addEmployee          - insert employee data into database
 *   - editEmployee         - edit existing employee data
 *   - deleteEmployee       - remove employee data from database
 *   - getById              - find employee data using id and return them
 */

class EmployeeModel{
    private $errors;
    public function __construct(){}

    /*
     * Validating correctness of employee data obtained from form.
     * input:
     * array(
     *    'name'  - employee name
     *    'email' - employee email
     *  )
     * return: true|false
     */
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
        if(!preg_match('/^[a-zA-Zà-ÿÀ-ß ]+$/', $data['name'])){
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

    public function getErrors(){return $this->errors;}

    /*
     * Insert employee data into database
     * Check if user with specified email already exists. Return false and set appropriate message if it is.
     *
     * input:
     * array(
     *    'name'  - employee name
     *    'email' - employee email
     *  )
     * return: true|false
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

    /*
     * Edit existed employee
     * input:
     * int $id - identity number in database of required employee record.
     * array $data(
     *    'name'  - employee name
     *    'email' - employee email
     *  )
     * return: true|false
     *
     */
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

    /*
     * Delete employee record from database
     * int $id - identity number in database of required employee record.
     *
     * return: true|false
     */
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
        return true;
    }

    /*
     * int $id - identity number in database of required employee record.
     * return false|array('id', 'name', 'email')
     */
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

    /*
     * Returns array of all existed employee records
     * return false|array $employees_list
     *
     *  $employees_list = [
     *      'id',
     *      'name',
     *      'email'
     *  ]
     *
     */
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