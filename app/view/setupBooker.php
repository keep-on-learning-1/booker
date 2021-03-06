<div class="form_page_header">
    <h1>Configuration of BoardroomBooker</h1>
</div>

<div class="default_form_container">
    <form id="bb_config_form" action="index.php?&action=setupBooker" method="POST">
        <input type="hidden" value="<?php echo $csrf; ?>" name="token">
        <h3>
            Choose first day of the week
        </h3>
        <p>
            <label>Sunday
                <input type="radio" name="first_day" value="sunday" checked>
            </label>
            <label>Monday
                <input type="radio" name="first_day" value="monday">
            </label>
        </p>
        <h3>
            Choose time format
        </h3>
        <p>
            <label>12 hours
                <input type="radio" name="time_format" value="12h" checked>
            </label>
            <label>24 hours
                <input type="radio" name="time_format" value="24h">
            </label>
        </p>
        <h3>
            Specify number of Boardrooms
        </h3>
        <p>
            <label>Number</label>
            <input type="text" value="3" name="number">
        </p>
        <p class="default_form_footer">
            <input type="submit" value="Create">
        </p>
    </form>
</div>
