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

        /**
         * ログインを試行
         * 
         * @return [$success:boolean, $message:string]
         * 
         */
        public function login($mail, $password) {

            $success = true;
            $message = "";
            $this->mail = $mail;

            try {
                $dbh = DB::singleton()->get();

                $stmt = $dbh->prepare("SELECT * FROM users WHERE mail = ?");
                $stmt->bindValue(1, $mail);
                $stmt->execute();
                $user = $stmt->fetch();

                // メールアドレスが存在しない場合は失敗
                if ($user === false) {
                    return [false, "fail"];
                }

                // アカウントロック状態の場合は期限をチェック
                if ($user["f_lock"] == true) {
                    if ($user["lock_expiration"] > time()) {
                        // ロック継続中
                        return [false, "lock"];
                    } else  {
                        // アンロック
                        $user["fault_count"] = 0;
                        $user["f_lock"] = false;
                        $user["lock_expiration"] = 0;
                    }
                }

                // // パスワードが不一致の場合は失敗、失敗回数を1増やす
                if (!password_verify($password, $user["password"])) {

                    $success = false;
                    $message = "fail";

                    if (++$user["fault_count"] == 10) {
                        // ロック状態
                        $user["f_lock"] = true;
                        $user["lock_expiration"] = time()+30*60;
                        $message = "lock";

                        $this->sendAccountLockedMail();
                    }
                } else {
                    $user["fault_count"] = 0;
                }

                // ロック・アンロック情報を更新
                $stmt = $dbh->prepare("UPDATE users SET fault_count = ?, f_lock = ?, lock_expiration = ? WHERE mail = ?");
                $stmt->bindValue(1, $user["fault_count"], PDO::PARAM_INT);
                $stmt->bindValue(2, $user["f_lock"], PDO::PARAM_BOOL);
                $stmt->bindValue(3, $user["lock_expiration"], PDO::PARAM_INT);
                $stmt->bindValue(4, $mail, PDO::PARAM_STR);
                $stmt->execute();

                $this->mail = $mail;
                $this->id = $user["id"];

                return [$success, $message];

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