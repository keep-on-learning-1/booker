<?php
class PagePattern{
    function __construct(){}

    public function getHeader($title = '', $css_files = array(), $js_files = array()){
        if($css_files && !is_array($css_files)){
            $css_files = array($css_files);
        }
        if($js_files && !is_array($js_files)){
            $js_files = array($js_files);
        }
?>
        <!DOCTYPE html>
        <html>
            <head>
                <title><?php echo $title; ?></title>
                <link rel="stylesheet" href="css/style.css">

                <?php foreach($css_files as $css_file): ?>
                    <?php if(file_exists('./css/'.$css_file)): ?>

                    <link rel="stylesheet" href="/css/<?php echo $css_file; ?>">

                    <?php endif; ?>
                <?php endforeach; ?>

                <?php foreach($js_files as $js_file): ?>
                    <?php if(file_exists('./js/'.$js_file)): ?>

                        <script src="/js/<?php echo $js_file; ?>"></script>

                    <?php endif; ?>
                <?php endforeach; ?>

            </head>
            <body>
<?php
    }
    public function getFooter($js_files = array()){
        if($js_files && !is_array($js_files)){
            dd($js_files);
            $js_files = array($js_files);
        }
?>
            <?php foreach($js_files as $js_file): ?>
                <?php if(file_exists('./js/'.$js_file)): ?>

                <script src="/js/<?php echo $js_file; ?>"></script>

                <?php endif; ?>
            <?php endforeach; ?>

            </body>
        </html>
<?php
    }
    public function renderAppMessages(){
        $messages = BoardroomBooker::getMessages();
        if(isset($messages) && is_array($messages)){
            $msg_html[] = '<div id="app_messages_container">';
            foreach($messages as $msg){
                $msg_html[] = "<div class=\"app_message {$msg['class']}\">{$msg['text']}</div><br>";
            }
            $msg_html[] = '</div>';
         }
        return implode("\r\n", $msg_html);
    }

    public function renderTopSection($curr_page = ''){
        $config = BoardroomBooker::getConfig();

        $html[] = '<div id="main_page_header">';
        $html[] = '<ul>';
        for($i=1; $i<=$config['booker']['number_of_bookers'];$i++){
            $html[] = '<li><a href="index.php?action=main&booker='.$i.'">Boardroom '.$i.'</a></li>';
        }
        $html[] ='</ul>';
        $html[] ='</div>';
        $html[] = '<div class="main_page_title">BoardroomBooker</div>';
        if($curr_page){ $html[] = '<div class="booker_title">'.$curr_page.'</div>';}
        return implode("\r\n", $html);
    }
}