<?php

    namespace app\model;

    use PHPMailer\PHPMailer\Exception;

    use app\helper\Mail;
    use app\helper\DB;

    use PDO;
    use PDOException;

    class PreUsers {

        private $id = 0;
        private $mail = "";
        private $token = "";
        private $enabled = 1;
        private $expiration = 0;
        private $tokenExpiration = 60 * 60 * 1;

        public function __construct($mail) {
            $this->mail = $mail;
        }

        public function sendTokenURLMail() {

            $url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . "/register?k=" . $this->token;
            $subject = "【Caledule】仮登録完了のお知らせ";
            $body = "入力いただいたメールアドレスでの仮登録が完了致しました。\n以下のURLから、本登録を行ってください。\n\n" . $url;
            $to = str_replace(array("\r", "\n"), "", $this->mail);

            try {
                $mailer = new Mail();
                $mailer->mail($to, $subject, $body);
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function isUnregistered() {
            return ( $this->enabled === 1 );
        }

        /**
         * pre-userの情報をSELECT文で取得
         * 
         */
        public function getPreUserDataByMail() {

            try {
                $dbh = DB::singleton()->get();
                $stmt = $dbh->prepare("SELECT * FROM pre_users WHERE mail = ?");
                $stmt->execute([$this->mail]);
                $preUser = $stmt->fetch();
            } catch (PDOException $e) {
                // 例外は利用側に処理を任せる
                throw $e;
            }

            // ユーザが存在しなかった場合はfalseを返却
            if ($preUser === false) {
                return false;
            }

            $this->id = $preUser["id"];
            $this->mail = $preUser["mail"];
            $this->token = $preUser["token"];
            $this->enabled = $preUser["enabled"];
            $this->expiration = $preUser["expiration"];

            return true;
        }

        public function createPreUser() {
            try {
                $dbh = DB::singleton()->get();
                $stmt = $dbh->prepare("INSERT INTO pre_users values (0, ?, ?, 1, ?)");
                $stmt->execute([$this->mail, $this->token, $this->expiration]);
                $this->id = $dbh->lastInsertId();
            } catch (PDOException $e) {
                // 例外は利用側に処理を任せる
                throw $e;
            }
        }

        public function reissueOneTimeToken() {

            $this->token = $this->generateToken();
            $this->expiration = time() + $this->tokenExpiration;

            try {

                $dbh = DB::singleton()->get();

                $stmt = $dbh->prepare("UPDATE pre_users SET token = ?, expiration = ? WHERE mail = ?");
                $stmt->bindValue(1, $this->token);
                $stmt->bindValue(2, $this->expiration);
                $stmt->bindValue(3, $this->mail);
                $stmt->execute();

            } catch (PDOException $e) {
                throw $e;
            }
        }

        /**
         * 仮登録から本登録に進んだときのトークンが正しいかチェック
         * 
         * 失敗するのは以下のケース
         * 1. トークンが存在しなかった場合
         * 2. トークンの期限が切れている場合
         * 3. 本登録が完了しているトークンだった場合
         */
        public function validateToken($token) {

            try {
                $dbh = DB::singleton()->get();
        
                $stmt = $dbh->prepare("SELECT * FROM pre_users WHERE token = ?");
                $stmt->bindValue(1, $token);
                $stmt->execute();
                $user = $stmt->fetch();

                if ( $user === false || $user["expiration"] < time() ) {
                    return [false, MSG_INVALID_TOKEN];
                }

                if ( $user["enabled"] == 0) {
                    return [false, MSG_REGISTERED_MAIL];
                }

            } catch (PDOException $e) {
                throw $e;
            }

            $this->mail = $user["mail"];
            return [true, ""];
        }

        public function getMail() {
            return $this->mail;
        }

        private function generateToken() {
            return bin2hex(random_bytes(16));
        }
    }
?>