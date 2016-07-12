<div id="app_messages_container">
    <?php echo BoardroomBooker::getRenderedMessages()?>
</div>

<div id="create_event_form_container">
    <form id="create_event_form" action="index.php?action=create_event" method="POST">
        <p>
            <span class="caption">
                1.) Booked for:
            </span>
            <br>
            <select name="employee">
                <?php foreach($employees_list as $employee):?>
                    <option value="<?php echo $employee['id'];?>" >
                        <?php echo $employee['name'];?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <span class="caption">
                2.) I would like to book this meeting:
            </span>
            <br>
            <select name="event_month">
                <?php for($i=0;$i<12;$i++):?>
                    <option value="<?php echo $i;?>" ><?php echo $months[$i]; ?></option>
                <?php endfor;?>
            </select>
            <select name="event_day">
                <?php for($i=1;$i<=31;$i++):?>
                    <option value="<?php echo $i;?>" ><?php echo $i;?></option>
                <?php endfor;?>
            </select>
            <select name="event_year">
                <?php for($i=0;$i<2;$i++):?>
                    <option value="<?php echo $year + $i;?>" ><?php echo $year + $i;?></option>
                <?php endfor;?>
            </select>
        </p>
        <p>
            <span class="caption">
                3.) Specify what the time and end of the meeting(This will be what people see on the calendar)
            </span>
            <br>
            <?php if($config['booker']['time_format'] == '24h'):?>
                <select  value="admin" name="start_hours">
                    <?php for($i=1; $i<=24; $i++):?>
                        <option value="<?php echo $i;?>" ><?php echo $i;?></option>
                    <?php endfor;?>
                </select>
                <select  value="admin" name="start_minutes">
                    <?php for($i=0; $i<60; $i+=5):?>
                        <option value="<?php echo $i;?>" ><?php echo str_pad($i, 2, '0', STR_PAD_LEFT);?></option>
                    <?php endfor;?>
                </select>
                <br>
                <br>
                <select  value="admin" name="end_hours">
                    <?php for($i=1; $i<=24; $i++):?>
                        <option value=<?php echo $i;?> ><?php echo $i;?></option>
                    <?php endfor;?>
                </select>
                <select  value="admin" name="end_minutes">
                    <?php for($i=0; $i<60; $i+=5):?>
                        <option value="<?php echo $i;?>" ><?php echo str_pad($i, 2, '0', STR_PAD_LEFT);?></option>
                    <?php endfor;?>
                </select>
            <?php elseif($config['booker']['time_format'] == '12h'):?>
                <select  value="admin" name="start_hours">
                    <?php for($i=1; $i<=12; $i++):?>
                        <option value="<?php echo $i;?>" ><?php echo $i;?></option>
                    <?php endfor;?>
                </select>
                <select  value="admin" name="start_minutes">
                    <?php for($i=0; $i<60; $i+=5):?>
                        <option value="<?php echo $i;?>" ><?php echo str_pad($i, 2, '0', STR_PAD_LEFT);?></option>
                    <?php endfor;?>
                </select>
                <select  value="admin" name="start_ampm">
                    <option value="am">AM</option>
                    <option value="pm">PM</option>
                </select>
                <br>
                <br>
                <select  value="admin" name="end_hours">
                    <?php for($i=1; $i<=12; $i++):?>
                        <option value="<?php echo $i;?>" ><?php echo $i;?></option>
                    <?php endfor;?>
                </select>
                <select  value="admin" name="end_minutes">
                    <?php for($i=0; $i<60; $i+=5):?>
                        <option value="<?php echo $i;?>" ><?php echo str_pad($i, 2, '0', STR_PAD_LEFT);?></option>
                    <?php endfor;?>
                </select>
                <select  value="admin" name="end_ampm">
                    <option value="am">AM</option>
                    <option value="pm">PM</option>
                </select>
            <?php endif;?>
        </p>
        <p>
            <span class="caption">
                4.) Enter the specifics for the meeting.(This will be what people see wen they click on the event link.)
            </span>
            <br>
            <textarea name="specifics" rows="4"></textarea>
        </p>
        <p>
            <span class="caption">
                5.) Is this going to be a reccuring event.
            </span>
            <br>
            <label>
                <input type="radio" name="is_recurred" value="0" checked> no<br>
            </label>
            <label>
                <input type="radio" name="is_recurred" value="1" > yes<br>
            </label>
        </p>
        <p>
            <span class="caption">
                6.) If it is reccuring, specify weekly, bi-weekly or monthly
            </span>
            <br>
            <label>
                <input type="radio" name="recurrence" value="weekly" checked>weekly<br>
            </label>
            <label>
                <input type="radio" name="recurrence" value="biweekly">bi-weekly<br>
            </label>
            <label>
                <input type="radio" name="recurrence" value="monthly">monthly<br>
            </label>
        </p>
        <p>
            <span class="caption">
                If weekly or bi-weekly, specify number of weeks for it to keep recurring.
                If monthly, specify the number of months.
                (If you choose "bi-weekly" and put in an odd number of weeks, the compter will round down.)
            </span>
            <br>
            <input type="text" name="duration" id="duration_input"> duration (max 4 weeks)
        </p>
        <p class="default_form_footer">
            <input type="submit" value="Create">
        </p>
    </form>
</div>