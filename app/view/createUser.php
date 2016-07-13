<div class="form_page_header">
    <h1>Create a new user</h1>
</div>

<div class="default_form_container">
    <form id="create_user_form" action="index.php?action=createUser" method="POST">
        <input type="hidden" value="<?php echo $csrf; ?>" name="token">
        <h3>
            Create user
        </h3>
        <p>
            <label>User login</label>
            <input type="text" value="admin" name="login">
        </p>
        <p>
            <label>Password</label>
            <input type="password" value="" name="password">
        </p>
        <p>
            <label>Confirm password</label>
            <input type="password" value="" name="confirm_password">
        </p>
        <p class="default_form_footer">
            <input type="submit" value="Create">
        </p>
    </form>
</div>