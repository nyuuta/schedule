<?php

    require_once "./src/helper/CSRF.php";
    require_once "./src/helper/Helper.php";

?>

<html>
    <head>
        <meta charset="utf-8"/>
        <title>Calendar</title>
        <meta name="description" content="トップページ" />

        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    </head>
    <body>
        <div id="container" class="container">

            <div>
                <h1>トップ</h1>
            </div>
            <?php if ($isLogin === true) : ?>

                <div id="logout-form">
                    <form id="form-logout" method="POST" action="/logout">
                        <input type="hidden" name="token" value="<?= Helper::h(CSRF::generate()) ?>"></input>
                        <button type="submit">ログアウト</button>
                    </form>
                </div>

            <?php endif; ?>

        </div><!-- .container -->

    </body>
</html>