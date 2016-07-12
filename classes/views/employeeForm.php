<div id="app_messages_container">
    <?php echo BoardroomBooker::getRenderedMessages()?>
</div>

<div id="add_employee_container">
    <form method="POST" action="index.php?action=<?php echo $form_action?>&id=<?php echo $id?>">
        <ul>
            <li>
                <label>
                    <span class="number">1)</span>
                    <div class="inner_content">
                        Enter new employee name (required).
                    </div>
                    <input type="text" name="name" value="<?php echo $employee_data['name']?>">
                </label>
            </li>
            <li>
                <label>
                    <span class="number">2)</span>
                    <div class="inner_content">
                        Enter new employee e-mail (required).
                    </div>
                    <input type="text" name="email" value="<?php echo $employee_data['email']?>">
                </label>
            </li>
        </ul>
        <br>
        <input type="submit" value="<?php echo $submit_caption; ?>">
    </form>
</div>
