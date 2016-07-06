<?php
/*
 * Hidden field 'id' was added for a case of an error will occur during updating employee data.
 * If the error occurs, employee data will be reloaded from $_POST and employee's id will be lost otherwise.
 */
class EditEmployee extends PagePattern {
    private $data;

    public function __construct($data = null){
        if($data){$this->data = $data;}
    }

    public function render(){
        $this->getHeader('EmployeeList');
        ?>
        <?php echo $this->renderTopSection('Edit the employee')?>

        <?php echo $this->renderAppMessages();?>

        <div id="add_employee_container">
            <form method="POST" action="index.php?action=edit_employee&id=<?php echo $this->data['id']?>">
                <input type="hidden" value="<?php echo $this->data['id']?>" name="id">
                <ul>
                    <li>
                        <label>
                            <span class="number">1)</span>
                            <div class="inner_content">
                                Enter new employee name (required).
                            </div>
                            <input type="text" name="name" value="<?php echo $this->data['name']?>">
                        </label>
                    </li>
                    <li>
                        <label>
                            <span class="number">2)</span>
                            <div class="inner_content">
                                Enter new employee e-mail (required).
                            </div>
                            <input type="text" name="email" value="<?php echo $this->data['email']?>">
                        </label>
                    </li>
                </ul>
                <br>
                <input type="submit" value="Edit">
            </form>
        </div>

        <?php
        $this->getFooter();
    }
}
