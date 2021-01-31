<?php

    namespace app\model;

    use app\model\PreUsers;
    use app\Exception\ValidationException;
    use app\Exception\PreRegisterFailException;
    use app\Exception\SendMailException;
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
    }

?>