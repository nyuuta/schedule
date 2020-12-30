<?php

    require_once "./DB.php";
    require_once "./Log.php";

    class Users {

        private $id;
        private $mail;
        private $password;

        public function sendRegisterDoneMail() {
            $subject = "本登録完了のお知らせ";
            $message = "本登録が完了致しました。";
            $headers = "From: from@example.com";

            mail(str_replace(array("\r", "\n"), "", $this->mail), $subject, $message, $headers);
        }

        public function register($mail, $password) {

            $this->mail = $mail;
            $this->password = password_hash($password, PASSWORD_BCRYPT);
        
            // DBにinsert
            try {
                $dbh = DB::singleton()->get();

                $stmt = $dbh->prepare("INSERT INTO users values (0, ?, ?)");
                $stmt->bindValue(1, $this->mail);
                $stmt->bindValue(2, $this->password);
                $stmt->execute();

                $stmt = $dbh->prepare("UPDATE pre_users SET enabled = 0 WHERE mail = ?");
                $stmt->bindValue(1, $this->mail);
                $stmt->execute();

            } catch (PDOException $e) {
                Log::error($e->getMessage());
                throw $e;
            }
        }
    }
?>