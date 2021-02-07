<?php

    namespace app\model;

    use PHPMailer\PHPMailer\Exception;

    use app\helper\Session;
    use app\helper\Mail;
    use app\helper\DB;

    use PDO;
    use PDOException;

    class Users {

        private $id;
        private $mail;
        private $password;
        private $fault_count;
        private $lock_expiration;
        private $f_lock;

        public function __constructor($id = 0, $mail = "", $password = "") {
            $this->id = $id;
            $this->mail = $mail;
            $this->password = $password;
        }

        public function sendRegisterDoneMail() {

            $subject = "本登録完了のお知らせ";
            $body = "本登録が完了致しました。";
            $to = str_replace(array("\r", "\n"), "", $this->mail);

            try {
                $mailer = new Mail();
                $mailer->mail($to, $subject, $body);
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function sendAccountLockedMail() {

            $subject = "アカウントロックのお知らせ";
            $body = "ログイン失敗回数が一定を超えましたので、アカウントをロックしました。\n30分後に再度お試しください。";
            $to = str_replace(array("\r", "\n"), "", $this->mail);

            try {
                $mailer = new Mail();
                $mailer->mail($to, $subject, $body);
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function sendAccountDeletedMail($mail) {

            $subject = "アカウント削除完了のお知らせ";
            $body = "アカウントの削除処理が完了致しました。";
            $to = str_replace(array("\r", "\n"), "", $mail);

            try {
                $mailer = new Mail();
                $mailer->mail($to, $subject, $body);
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function register($token, $password) {

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);;
        
            // DBにinsert
            try {
                $dbh = DB::singleton()->get();

                $stmt = $dbh->prepare("SELECT * FROM pre_users WHERE token = ?");
                $stmt->bindValue(1, $token);
                $stmt->execute();
                $user = $stmt->fetch();

                $stmt = $dbh->prepare("INSERT INTO users values (0, ?, ?, 0, 0, 0)");
                $stmt->bindValue(1, $user["mail"]);
                $stmt->bindValue(2, $hashedPassword);
                $stmt->execute();

                $stmt = $dbh->prepare("UPDATE pre_users SET enabled = 0 WHERE token = ?");
                $stmt->bindValue(1, $token);
                $stmt->execute();

                $this->mail = $user["mail"];

            } catch (PDOException $e) {
                throw $e;
            }
        }

        public function create($mail, $password) {

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);;
        
            // DBにinsert
            try {
                $dbh = DB::singleton()->get();

                $stmt = $dbh->prepare("INSERT INTO users values (0, ?, ?, 0, 0, 0)");
                $stmt->bindValue(1, $mail);
                $stmt->bindValue(2, $hashedPassword);
                $stmt->execute();

                $this->id = $dbh->lastInsertId();
                $this->mail = $mail;

            } catch (PDOException $e) {
                throw $e;
            }
        }

        public function getUserByMail($mail) {
            try {
                $dbh = DB::singleton()->get();

                $stmt = $dbh->prepare("SELECT * FROM users WHERE mail = ?");
                $stmt->bindValue(1, $mail);
                $stmt->execute();
                $user = $stmt->fetch();

                // メールアドレスが一致するユーザが存在しない場合は失敗
                if ($user === false) {
                    return false;
                }

                $this->id = $user["id"];
                $this->mail = $user["mail"];
                $this->password = $user["password"];
                $this->fault_count = $user["fault_count"];
                $this->lock_expiration = $user["lock_expiration"];
                $this->f_lock = (boolean)$user["f_lock"];

            } catch (PDOException $e) {
                // DB例外は利用側に例外処理を任せる
                throw $e;
            }
        }

        /**
         * アカウントのロック状態をチェック
         */
        public function isAccountLocked() {
            if ($this->f_lock === true) {
                if ($this->lock_expiration > time()) {
                    return true;
                }
            }

            return false;
        }

        /**
         * パスワードの照合
         */
        public function passwordVerify($unencryptedPassword) {

            return password_verify($unencryptedPassword, $this->password);
        }

        public function loginFailed() {
            $this->fault_count++;
            if ($this->fault_count > MAX_LOGIN_FAULT_COUNT) {
                $this->f_lock = true;
                $this->lock_expiration = time()+ACCOUNT_LOCK_EXPIRATION;
            }

            try {
                $dbh = DB::singleton()->get();

                // ロック・アンロック情報を更新
                $stmt = $dbh->prepare("UPDATE users SET fault_count = ?, f_lock = ?, lock_expiration = ? WHERE mail = ?");
                $stmt->bindValue(1, $this->fault_count, PDO::PARAM_INT);
                $stmt->bindValue(2, $this->f_lock, PDO::PARAM_BOOL);
                $stmt->bindValue(3, $this->lock_expiration, PDO::PARAM_INT);
                $stmt->bindValue(4, $this->mail, PDO::PARAM_STR);
                $stmt->execute();

            } catch (PDOException $e) {
                // DB例外は利用側に例外処理を任せる
                throw $e;
            }
        }

        public function loginSuccess() {

            $this->fault_count = 0;

            try {
                $dbh = DB::singleton()->get();

                // ロック・アンロック情報を更新
                $stmt = $dbh->prepare("UPDATE users SET fault_count = ?, f_lock = ?, lock_expiration = ? WHERE mail = ?");
                $stmt->bindValue(1, $this->fault_count, PDO::PARAM_INT);
                $stmt->bindValue(2, $this->f_lock, PDO::PARAM_BOOL);
                $stmt->bindValue(3, $this->lock_expiration, PDO::PARAM_INT);
                $stmt->bindValue(4, $this->mail, PDO::PARAM_STR);
                $stmt->execute();

            } catch (PDOException $e) {
                // DB例外は利用側に例外処理を任せる
                throw $e;
            }
        }

        public function getID() {
            return $this->id;
        }

        public static function isLogin() {
            return ! empty(Session::get("userID"));
        }

        public static function getUserID() {
            $id = Session::get("userID");
            return (empty($id) ? false : $id);
        }

        public function deleteAccount($id) {

            try {
                $dbh = DB::singleton()->get();

                $stmt = $dbh->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->bindValue(1, $id);
                $stmt->execute();
                $user = $stmt->fetch();

                $stmt = $dbh->prepare("DELETE FROM users WHERE id = ?");
                $stmt->bindValue(1, $id);
                $stmt->execute();

                self::sendAccountDeletedMail($user["mail"]);
            } catch (PDOException $e) {
                // DB例外は利用側に例外処理を任せる
                throw $e;
            }
        }

        public static function identifyUserByPassword($id, $password) {
            // $idでユーザ検索
            try {
                $dbh = DB::singleton()->get();
                $stmt = $dbh->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->bindValue(1, $id);
                $stmt->execute();
                $user = $stmt->fetch();

                if ($user === false) {
                    return false;
                }

                return (password_verify($password, $user["password"]));
            } catch (PDOException $e) {
                // DB例外は利用側に例外処理を任せる
                throw $e;
            }
        }
    }
?>