<?php

    require_once "./src/model/Users.php";

?>

<div id="header" class="header">

    <div class="title">
        Caledule
    </div>

    <div class="account-operation">
        
        <?php if ($isLogin === true) : ?>

            <div id="logout">
                <form id="form-logout" method="POST" action="/logout">
                    <input type="hidden" name="token" value="<?= Helper::h(CSRF::generate()) ?>"></input>
                    <button type="submit">ログアウト</button>
                </form>
            </div>

        <?php else: ?>

            <div id="login">
                <button onclick="location.href='/login'">ログイン/新規登録</button>
            </div>

        <?php endif; ?>
    </div>

</div>