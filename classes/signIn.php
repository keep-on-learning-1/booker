<?php
class SignIn extends PagePattern {
    private $csrf;
    function __construct(){
       /* $this->csrf = sha1(rand(0, PHP_INT_MAX));
        setcookie('setup_user_csrf', $this->csrf, time()+300);*/
    }
    function render(){
        $this->getHeader('Sign In')
        ?>
        <div class="form_page_header">
            <h1>BoardroomBooker</h1>
        </div>

        <?php echo $this->renderAppMessages();?>

        <div class="default_form_container">
            <form id="sign_in_form" action="index.php?action=login" method="POST">
                <input type="hidden" value="<?php echo $this->csrf; ?>" name="token">
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
        <?php
        $this->getFooter();
    }
}