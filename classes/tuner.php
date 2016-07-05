<?php
class Tuner extends PagePattern {
    function __construct(){}

    function render(){
        $this->getHeader('Configuration','tuner.css')
?>
        <h1 id="tuner">Configuration of BoardroomBooker</h1>
        <div class="tuner_form_container">
            <form name="bb_tuner" action="config.php" method="POST">
                <h3>
                    Set login and password for administrator
                </h3>
                <p>
                    <label>Login</label>
                    <input type="text" value="admin" name="login">
                </p>
                <p>
                    <label>Password</label>
                    <input type="password" name="password">
                </p>
                <h3>
                    Set prefix for database
                </h3>
                <p>
                    <label>Prefix: </label>
                    <input type="text" value="bb_" name="prefix">
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
                <p class="form_footer">
                    <input type="submit" value="Create">
                </p>
            </form>
        </div>
<?php
        $this->getFooter();
    }
}