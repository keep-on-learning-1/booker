<div class="form_page_header">
    <h1>Configuration of BoardroomBooker</h1>
</div>
<div class="default_form_container">
    <form id="db_config_form" action="index.php?action=setupDatabase" method="POST">
        <input type="hidden" value="<?php echo $csrf; ?>" name="token">
        <h3>
            Configuration of database
        </h3>
        <p>
            <label>Host</label>
            <input type="text" value="localhost" name="host">
        </p>
        <p>
            <label>Database name</label>
            <input type="text" value="" name="db_name">
        </p>
        <p>
            <label>Database user</label>
            <input type="text" value="root" name="db_user">
        </p>
        <p>
            <label>Password</label>
            <input type="password" name="db_password">
        </p>

        <p class="default_form_footer">
            <input type="submit" value="Create">
        </p>
    </form>
</div>