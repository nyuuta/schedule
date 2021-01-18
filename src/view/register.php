<?php

    use app\helper\CSRF;
    use app\helper\Helper;

?>

<html>
    <head>
        <meta charset="utf-8"/>
        <title>本登録 - Calendar</title>
        <meta name="description" content="本登録用ページ。" />

        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
        <link rel="stylesheet" href="./src/css/header.css">
        <link rel="stylesheet" href="./src/css/register.css">
    </head>
    <body>

        <div>
            <?php include($_SERVER["DOCUMENT_ROOT"]."/src/view/common/header.php"); ?>
        </div>

        <div id="container" class="register-container">

            <div class="title">
                <h1>本登録</h1>
            </div>

            <div>
                <p>
                    ご入力いただいたメールアドレスで本登録を行います。<br>
                    パスワード(8～32文字の半角英数字)の設定をお願いします。
                </p>
            </div>

            <div id="register-form">
                <form class="form" id="form-register" method="POST" action="/register">
                    <input type="hidden" name="token" value="<?= Helper::h(CSRF::generate()) ?>"></input>
                    <div>
                        <input id="password" type="password" name="password" placeholder="パスワード" value=""></input>
                    </div>
                    <div>
                        <input id="password-confirm" type="password" name="password-confirm" placeholder="パスワード(確認)" value=""></input>
                    </div>
                    <div>
                        <button type="submit">登録</button>
                    </div>
                </form>
            </div>

            <div class="message-area">
                <?= Helper::h($message) ?>
            </div>

        </div><!-- .container -->

    </body>
</html>