<?php

    use app\helper\CSRF;
    use app\helper\Helper;
    use app\model\Users;
    use app\Auth\Authorization;

?>

<div id="header" class="header">

    <div class="title">
        Caledule
    </div>

    <div class="account-operation">
        
        <div id="アカウント削除">
            <button onclick="location.href='/main'">カレンダーへ</button>
        </div>

        <?php if (Authorization::isLogin() === true) : ?>

            <div id="logout">
                <form id="form-logout" method="POST" action="/logout">
                    <input type="hidden" name="token" value="<?= Helper::h(CSRF::generate()) ?>"></input>
                    <button type="submit">ログアウト</button>
                </form>
            </div>

            <div id="account-delete">
                <button onclick="location.href='/account-delete'">アカウント削除</button>
            </div>

        <?php else: ?>

            <div id="login">
                <button onclick="location.href='/login'">ログイン/新規登録</button>
            </div>

        <?php endif; ?>
    </div>

</div>