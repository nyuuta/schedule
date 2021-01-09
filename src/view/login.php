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
        <link rel="stylesheet" href="./src/css/header.css">
        <link rel="stylesheet" href="./src/css/login.css">
    </head>
    <body>

        <div>
            <?php include($_SERVER["DOCUMENT_ROOT"]."/src/view/common/header.php"); ?>
        </div>

        <div class="login-container">

            <div class="title">
                <h1>ログイン</h1>
            </div>

            <div id="login-form">
                <form class="form" id="form-login" method="POST" action="/login">
                    <input type="hidden" name="token" value="<?= Helper::h(CSRF::generate()) ?>"></input>
                    <div>
                        <input id="mail" type="text" name="mail" placeholder="メールアドレス" value="<?= Helper::h($mail) ?>"></input>
                    </div>
                    <div>
                        <input id="password" type="password" name="password" placeholder="パスワード" value=""></input>
                    </div>
                    <div>
                        <button type="submit">ログイン</button>
                    </div>
                </form>
            </div>

            <div class="message-area">
                <?= Helper::h($message) ?>
            </div>

            <div>
                新規登録は<a href="/pre-register">コチラ</a>
            </div>

        </div><!-- .container -->

    </body>
</html>