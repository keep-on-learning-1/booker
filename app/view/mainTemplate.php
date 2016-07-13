<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" href="<?php echo BoardroomBooker::getBaseURL()?>/resources/css/style.css">

        <?php foreach($this->getCSS() as $css_file): ?>
            <?php if(file_exists(BASE_PATH.'resources/css/'.$css_file)): ?>

                <link rel="stylesheet" href="<?php echo BoardroomBooker::getBaseURL(),'/resources/css/',$css_file; ?>">

            <?php endif; ?>
        <?php endforeach; ?>
    </head>
    <body>

    <?php if($content && is_array($content)){ echo join("\r\n", $content); } ?>

    <?php foreach($this->getJS() as $js_file): ?>
        <?php if(file_exists(BASE_PATH.'resources/js/'.$js_file)): ?>

            <script src="<?php echo BoardroomBooker::getBaseURL(),'/resources/js/',$js_file; ?>"></script>

        <?php endif; ?>
    <?php endforeach; ?>

    </body>
</html>