<?php
?>

<html>
    <head>
        <meta charset="utf-8"/>
        <title>本登録 - Calendar</title>
        <meta name="description" content="本登録用ページ。" />

        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    </head>
    <body>
        <div id="container" class="container">

            <div>
                <h1>パスワードを設定する</h1>
                <p>
                    ご入力いただいたメールアドレスで本登録を行います。<br>
                    パスワードの設定をお願いします。
                </p>
            </div>

            <div id="register-form">
                <form id="form-register" method="POST" action="/register">
                    <div>
                        <input id="password" type="password" name="password" placeholder="パスワード" value=""></input>
                    </div>
                    <div>
                        <input id="password-confirm" type="password" name="password-confirm" placeholder="パスワード(確認)" value=""></input>
                    </div>
                    <div>
                        <button type="button" onclick="history.back()">もどる</button>
                        <button type="submit">登録</button>
                    </div>
                </form>
            </div>

            <div>
                <?php echo($message) ?>
            </div>

        </div><!-- .container -->

    </body>
</html>