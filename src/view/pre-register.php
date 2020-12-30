<?php
?>

<html>
    <head>
        <meta charset="utf-8"/>
        <title>仮登録 - Calendar</title>
        <meta name="description" content="メールアドレスを仮登録するページ。" />

        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    </head>
    <body>
        <div id="container" class="container">

            <div>
                <h1>メールアドレスで仮登録する</h1>
                <p>
                    仮登録後、ご入力いただいたメールアドレス宛に本登録用URLを添付した<br>
                    メールを送信致します。
                </p>
            </div>

            <div id="pre-register-form">
                <form id="form-pre-register" method="POST" action="/pre-register">
                    <div>
                        <input id="mail" type="text" name="mail" placeholder="メールアドレス" value="<?php echo($mail) ?>"></input>
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