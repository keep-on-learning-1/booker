<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" href="css/style.css">

        <?php foreach($this->getCSS() as $css_file): ?>
            <?php if(file_exists('./css/'.$css_file)): ?>

                <link rel="stylesheet" href="/css/<?php echo $css_file; ?>">

            <?php endif; ?>
        <?php endforeach; ?>
    </head>
    <body>

    <?php if($content && is_array($content)){ echo join("\r\n", $content); } ?>

    <?php foreach($this->getJS() as $js_file): ?>
        <?php if(file_exists('./js/'.$js_file)): ?>

            <script src="/js/<?php echo $js_file; ?>"></script>

        <?php endif; ?>
    <?php endforeach; ?>

    </body>
</html>