<?php

    use app\helper\CSRF;
    use app\helper\Helper;

?>

<html>
    <head>
        <meta charset="utf-8"/>
        <title>Calendar</title>
        <meta name="description" content="トップページ" />

        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
        <link rel="stylesheet" href="./src/css/header.css">
    </head>
    <body>

        <div id="container" class="container">

            <div>
                <?php include($_SERVER["DOCUMENT_ROOT"]."/src/view/common/header.php"); ?>
            </div>

        </div><!-- .container -->

    </body>
</html>