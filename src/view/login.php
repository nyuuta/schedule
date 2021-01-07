<?php

    require_once "./src/helper/CSRF.php";
    require_once "./src/helper/Helper.php";

?>

<html>
    <head>
        <meta charset="utf-8"/>
        <title>ログイン - Calendar</title>
        <meta name="description" content="ログイン用ページ。" />

        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    </head>
    <body>
        <div id="container" class="container">

            <div>
                <h1>ログイン</h1>
            </div>

            <div id="login-form">
                <form id="form-login" method="POST" action="/login">
                    <input type="hidden" name="token" value="<?= Helper::h(CSRF::generate()) ?>"></input>
                    <div>
                        <input id="mail" type="text" name="mail" placeholder="メールアドレス" value="<?= Helper::h($mail) ?>"></input>
                    </div>
                    <div>
                        <input id="password" type="password" name="password" placeholder="パスワード" value=""></input>
                    </div>
                    <div>
                        <button type="button" onclick="history.back()">もどる</button>
                        <button type="submit">ログイン</button>
                    </div>
                </form>
            </div>

            <div>
                <?= Helper::h($message) ?>
            </div>

        </div><!-- .container -->

    </body>
</html>