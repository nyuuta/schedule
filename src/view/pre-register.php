<?php

require_once "./src/helper/CSRF.php";
require_once "./src/helper/Helper.php";

?>

<html>
    <head>
        <meta charset="utf-8"/>
        <title>仮登録 - Calendar</title>
        <meta name="description" content="メールアドレスを仮登録するページ。" />

        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
        <link rel="stylesheet" href="./src/css/header.css">
        <link rel="stylesheet" href="./src/css/pre-register.css">

    </head>
    <body>

        <div>
            <?php include($_SERVER["DOCUMENT_ROOT"]."/src/view/common/header.php"); ?>
        </div>

        <div id="container" class="pre-register-container">

            <div class="title">
                <h1>仮登録</h1>
            </div>

            <div>
                <p>
                    メールアドレスを仮登録後、ご入力いただいたメールアドレス宛に<br>
                    本登録用URLを添付したメールを送信致します。
                </p>
            </div>

            <div id="pre-register-form">
                <form class="form" id="form-pre-register" method="POST" action="/pre-register">
                    <div>
                        <input type="hidden" name="token" value="<?= Helper::h(CSRF::generate()) ?>"></input>
                        <input id="mail" type="text" name="mail" placeholder="メールアドレス" value="<?= Helper::h($mail) ?>"></input>
                    </div>
                    <div>
                        <button type="submit">仮登録</button>
                    </div>
                </form>
            </div>

            <div class="message-area">
                <?= Helper::h($message) ?>
            </div>

        </div><!-- .container -->

    </body>
</html>