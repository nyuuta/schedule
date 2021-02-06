<?php

    use app\helper\CSRF;
    use app\helper\Helper;

?>

<html>
    <head>
        <meta charset="utf-8"/>
        <title>仮登録 - Calendar</title>
        <meta name="description" content="メールアドレスを仮登録するページ。" />

        <script src="./src/js/jquery-3.5.1.min.js"></script>
        <script src="./src/js/pre-register.js"></script>

        <script src="./src/js/component/Component.js"></script>
        <script src="./src/js/component/PreRegisterComponent.js"></script>

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
                        <input id="mail" type="text" name="mail" placeholder="メールアドレス" value="<?= Helper::old("mail") ?>"></input>
                    </div>
                    <div>
                        <button id="pre-register-button" type="submit">仮登録</button>
                    </div>
                </form>
            </div>

            <div id="message-area" class="message-area">
                <?= $errors->get("mail") ?>
            </div>

        </div><!-- .container -->

    </body>
</html>