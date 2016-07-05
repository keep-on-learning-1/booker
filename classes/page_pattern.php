<?php
class PagePattern{
    function getHeader($title = '', $css_files = array(), $js_files = array()){
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
                    <?php if(file_exists('./css/'.$js_file)): ?>

                        <script src="/js/<?php echo $js_file; ?>"></script>

                    <?php endif; ?>
                <?php endforeach; ?>

            </head>
            <body>
<?php
    }
    function getFooter($js_files = array()){
        if($js_files && !is_array($js_files)){
            $js_files = array($js_files);
        }
?>
            <?php foreach($js_files as $js_file): ?>
                <?php if(file_exists('./css/'.$js_file)): ?>

                <script src="/js/<?php echo $js_file; ?>"></script>

                <?php endif; ?>
            <?php endforeach; ?>

            </body>
        </html>
<?php
    }
}