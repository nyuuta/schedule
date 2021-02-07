<?php

    namespace app\model;

    use app\model\PreUsers;
    use app\model\Users;
    use app\Exception\ValidationException;
    use app\Exception\OneTimeTokenException;
    use app\helper\Session;

    use PHPMailer\PHPMailer\Exception;
    
    class AccountManager {

        public function __construct() {

        }

        /**
         * 指定されたメールアドレスで仮登録を行う
         */
        public function preRegister($mail) {

            $preUser = new PreUsers($mail);
            $params = ["mail" => $mail];
            $errors = ["mail" => MSG_REGISTERED_MAIL];

            try {
                // 仮ユーザ情報がなければ新規作成
                if ( $preUser->getPreUserDataByMail() === false ) {
                    $preUser->createPreUser();
                }

                // 本登録済みの場合は仮登録失敗
                if ( $preUser->isUnregistered() === false ) {
                    throw (new ValidationException(implode("\n", $errors), 301))
                        ->setParams($params)
                        ->setErrors($errors);
                }

                // ワンタイムトークンの再発行をして完了通知を出す
                $preUser->reissueOneTimeToken();
                $preUser->sendTokenURLMail();
            } catch (PDOException | Exception $e) {
                throw $e;
            }
        }

        public function tokenValidation($token) {

            $errors = ["message" => MSG_INVALID_TOKEN_URL];

            try {
                // $token
                $preUser = new PreUsers("");
                $preUser->setToken($token);
                if ($preUser->getPreUserDataByToken() === false || $preUser->validateOneTimeToken() === false) {
                    throw (new OneTimeTokenException(implode("\n", $errors), 301))
                        ->setErrors($errors);
                }

            } catch (PDOException $e) {
                throw $e;
            }
        }

        /**
         * トークンとパスワードから本登録を行う
         */
        public function register($token, $password) {

            try {

                // 仮登録ユーザ情報の取得
                $preUser = new PreUsers("");
                $preUser->setToken($token);
                $preUser->getPreUserDataByToken();

                // 本登録ユーザ情報の作成
                $user = new Users();
                $user->create($preUser->getMail(), $password);

                // 当該ユーザの仮登録を禁止
                $preUser->disablePreRegister();

                // メール送信
                $user->sendRegisterDoneMail();
                
            } catch (PDOException | Exception $e) {
                throw $e;
            }
        }

        /**
         * ログイン試行
         * 
         * ログイン可能なメールアドレスとパスワードの組み合わせかチェック
         */
        public function attemptLogin($mail, $password) {

            $user = new Users();
            $params = ["mail" => $mail];
            $errors = ["mail" => MSG_LOGIN_FAIL];

            try {
                // ユーザ情報が存在しなければログイン失敗
                if ( $user->getUserByMail($mail) === false ) {
                    throw (new ValidationException("Target user doesn't exist.", 301))
                        ->setParams($params)
                        ->setErrors(["mail" => MSG_LOGIN_FAIL]);
                }

                // アカウントロック状態の場合はログイン失敗
                if ( $user->isAccountLocked() === true ) {
                    throw (new ValidationException("Target user's account is locked.", 301))
                        ->setParams($params)
                        ->setErrors(["mail" => MSG_ACCOUNT_LOCKED]);
                }

                // パスワード照合
                if ($user->passwordVerify($password) === true) {
                    $user->loginSuccess();
                    Session::regenID();
                    Session::set("userID", $user->getID());
                } else {
                    $user->loginFailed();
                    if ($user->isAccountLocked() === true) {
                        $user->sendAccountLockedMail();
                        throw (new ValidationException("Target user's account is locked.", 301))
                        ->setParams($params)
                        ->setErrors(["mail" => MSG_ACCOUNT_LOCKED]);
                    } else {
                        throw (new ValidationException("Target user doesn't exist.", 301))
                        ->setParams($params)
                        ->setErrors(["mail" => MSG_LOGIN_FAIL]);
                    }
                }
            } catch (PDOException | Exception $e) {
                throw $e;
            }
        }
    }

?>