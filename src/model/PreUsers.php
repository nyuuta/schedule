<?php

    namespace app\model;

    use PHPMailer\PHPMailer\Exception;

    use app\helper\Log;
    use app\helper\Mail;
    use app\helper\DB;
    use app\model\CSRF;

    use PDO;
    use PDOException;

    class PreUsers {

        private $id;
        private $mail;
        private $token;
        private $enabled;
        private $expiration;

        public function sendTokenURLMail() {

            $url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . "/register?k=" . $this->token;
            $subject = "仮登録完了のお知らせ";
            $body = "以下のURLから、本登録を行ってください。\n\n" . $url;
            $to = str_replace(array("\r", "\n"), "", $this->mail);

            try {
                $mailer = new Mail();
                $mailer->mail($to, $subject, $body);
            } catch (Exception $e) {

            }
        }

        public function preRegister($mail) {

            $oneDayTime = 60*60*24;
            $this->token = $this->generateToken();
            $this->expiration = time()+$oneDayTime;
            $this->mail = $mail;
        
            // DBにinsert
            try {
                $dbh = DB::singleton()->get();
                $stmt = $dbh->prepare("SELECT * FROM pre_users WHERE mail = ?");
                $stmt->bindValue(1, $this->mail);
                $stmt->execute();
                $result = $stmt->fetch();
        
                // 仮登録されていないメールアドレスの場合は新しく仮登録
                if ($result === false) {
                    $stmt = $dbh->prepare("INSERT INTO pre_users values (0, ?, ?, 1, ?)");
                    $stmt->bindValue(1, $this->mail);
                    $stmt->bindValue(2, $this->token);
                    $stmt->bindValue(3, $this->expiration);
                    $stmt->execute();
                    return true;
                }

                // 仮登録済みで、さらに本登録済みの場合は仮登録失敗
                if ($result["enabled"] == 0) {
                    return false;
                }

                // 仮登録済みだが本登録がまだの場合は、有効期限に問わずトークンを再発行する
                $stmt = $dbh->prepare("UPDATE pre_users SET token = ?, expiration = ? WHERE mail = ?");
                $stmt->bindValue(1, $this->token);
                $stmt->bindValue(2, $this->expiration);
                $stmt->bindValue(3, $this->mail);
                $stmt->execute();
                return true;

            } catch (PDOException $e) {
                Log::error($e->getMessage());
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