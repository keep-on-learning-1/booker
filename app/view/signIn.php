<div class="form_page_header">
    <h1>BoardroomBooker</h1>
</div>

<div id="app_messages_container">
    <?php echo BoardroomBooker::getRenderedMessages()?>
</div>

<div class="default_form_container">
    <form id="sign_in_form" action="index.php?action=login" method="POST">
        <p>
            <label>Login</label>
            <input type="text" value="" name="login">
        </p>
        <p>
            <label>Password</label>
            <input type="password" value="" name="password">
        </p>

        <p class="default_form_footer">
            <input type="submit" value="Login">
        </p>
    </form>
</div>