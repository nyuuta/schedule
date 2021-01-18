<?php

    use app\helper\CSRF;
    use app\helper\Helper;

?>

<html>
    <head>
        <meta charset="utf-8"/>
        <title>アカウント削除 - Calendar</title>
        <meta name="description" content="アカウント削除用ページ。" />

        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
        <link rel="stylesheet" href="./src/css/header.css">
        <link rel="stylesheet" href="./src/css/account-delete.css">
    </head>
    <body>

        <div>
            <?php include($_SERVER["DOCUMENT_ROOT"]."/src/view/common/header.php"); ?>
        </div>

        <div class="account-delete-container">

            <div class="title">
                <h1>アカウント削除</h1>
            </div>

            <div id="login-form">
                <form class="form" id="form-account-delete" method="POST" action="/account-delete">
                    <input type="hidden" name="token" value="<?= Helper::h(CSRF::generate()) ?>"></input>
                    <div>
                        <input id="password" type="password" name="password" placeholder="パスワード" value=""></input>
                    </div>
                    <div>
                        <button type="submit">削除</button>
                    </div>
                </form>
            </div>

            <div class="message-area">
                <?= Helper::h($message) ?>
            </div>

        </div><!-- .container -->

    </body>
</html>