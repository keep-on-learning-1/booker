<?php
class Error extends PagePattern {
   function __construct(){}

    function render(){
        $this->getHeader('Configuration of database')
        ?>

        <div class="form_page_header">
            <h1>Fatal error</h1>
        </div>

        <?php echo $this->renderAppMessages();?>

        <?php
        $this->getFooter();
    }
}