<?php
class AddEmployee extends PagePattern {
    private $data;
    public function __construct(){
        $this->data = $_POST;
    }

    public function render(){

        $this->getHeader('EmployeeList');
        ?>
        <?php echo $this->renderTopSection('Add an employee')?>

        <?php echo $this->renderAppMessages();?>

        <div id="add_employee_container">
            <form method="POST" action="index.php?action=add_employee">
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
                <input type="submit" value="Add">
            </form>
        </div>

        <?php
        $this->getFooter();
    }
}
