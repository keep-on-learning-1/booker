<?php
class EmployeeList extends PagePattern {
    public $db;
    public $employee_list;

    public function __construct(){
        $db = $this->db = BoardroomBooker::getDB();
        $res = $db->query('SELECT * FROM employees');
        if(!$res){
           BoardroomBooker::setMessage($db->errorInfo()[2], 'msg-error');
        }
        $this->employee_list = $res->fetchAll(PDO::FETCH_ASSOC);
        //echo $res->errorCode();
        //$employee_list = $res->fetchAll(PDO::FETCH_ASSOC);

    }

    function render(){

        $this->getHeader('EmployeeList');
?>
        <?php echo $this->renderTopSection('Employee List')?>

        <?php echo $this->renderAppMessages();?>

        <?php if(($this->employee_list)):?>
            <div id="employee_container">
                <table  id="employee_table">
                    <tbody>
                        <?php foreach($this->employee_list as $employee):?>
                            <tr>
                                <td>
                                    <a href="mailto:<?php echo $employee['email']?>">
                                        <?php echo $employee['name'] ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="index.php?action=remove_employee&id=<?php echo $employee['id'];?>" class="remove_link">
                                       REMOVE
                                    </a>
                                </td>
                                <td>
                                    <a href="index.php?action=edit_employee_form&id=<?php echo $employee['id'];?>">
                                       EDIT
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
                <a id="add_employee_button" href="index.php?action=add_employee_form">
                    Add a new employee
                </a>
            </div id="employee_container">
            <script>
                var table = document.getElementById('employee_table');
                table.onclick = function(e){
                    var target = e.target;
                    while(target != this){
                        if(target.classList.contains('remove_link')){
                            alert('ok');
                            return;
                        }
                        target = target.parentNode;
                    }
                }
            </script>
        <?php endif; ?>

<?php
        $this->getFooter();
    }
}