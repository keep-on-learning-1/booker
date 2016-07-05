<?php
class SetupBooker extends PagePattern {
    private $csrf;
    function __construct(){
        $this->csrf = sha1(rand(0, PHP_INT_MAX));
        setcookie('setup_user_csrf', $this->csrf, time()+300);
    }
    function render(){
        $this->getHeader('Configuration of BoardroomBooker');
        ?>
        <div class="form_page_header">
            <h1 id="tuner">Configuration of BoardroomBooker</h1>
        </div>
        <div class="default_form_container">
            <form id="bb_config_form" action="createUser.php" method="POST">
                <input type="hidden" value="<?php echo $this->csrf; ?>" name="token">
                <h3>
                    Create master user
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
        <?php
        $this->getFooter();
    }
}