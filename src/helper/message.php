<?php

    define("MSG_PASSWORD_INCORRECT", "パスワードが違います。");
    define("MSG_REGISTERED_MAIL", "既に本登録されているメールアドレスです。");
    define("MSG_INVALID_TOKEN", "無効なトークンです。");
    define("MSG_DONE_PREREGISTER", "仮登録処理が完了しました。");
    define("MSG_DONE_REGISTER", "本登録処理が完了しました。");
    define("MSG_INVALID_TOKEN_URL", "既に本登録が完了しているか、URLが無効です。再度、仮登録を行って下さい。");

    // バリデーションエラーメッセージ
    define("MSG_INVALID_MAIL", "正しい形式のメールアドレスを入力してください。");
    define("MSG_INVALID_PASSWORD", "無効なパスワードです。8～32文字の半角英数字でパスワードをお決めください。");
    define("MSG_INCORRECT_PASSWORD", "パスワードが異なります。");
    define("MSG_REQUIRED", "必須項目です。");

    // ログイン処理時メッセージ
    define("MSG_LOGIN_FAIL", "メールアドレスかパスワードに間違いがあります。");
    define("MSG_LOGIN_SUCCESS", "ログインに成功しました。");
    define("MSG_ACCOUNT_LOCKED", "一定回数ログインに失敗しましたので、現在アカウントをロックしています。");

    // ログインの最大失敗回数とアカウントロック期限
    define("MAX_LOGIN_FAULT_COUNT", 10);
    define("ACCOUNT_LOCK_EXPIRATION", 60*30);

    // アカウント削除
    define("MSG_DONE_ACCOUNT_DELETE", "アカウントの削除が完了しました。");

?>