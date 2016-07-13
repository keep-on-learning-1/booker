<div id="app_messages_container">
    <?php echo BoardroomBooker::getRenderedMessages()?>
</div>

<?php if($employee_list):?>
    <div id="employee_container">
        <table  id="employee_table">
            <tbody>
            <?php foreach($employee_list as $employee):?>
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
    </div> <!-- /#employee_container -->

    <script>
        var table = document.getElementById('employee_table');
        table.onclick = function(e){
            var target = e.target;
            while(target != this){
                if(target.classList.contains('remove_link')){
                    return confirm('Are you sure you want to delete this contact?');
                }
                target = target.parentNode;
            }
        }
    </script>
<?php endif; ?>