<?php

    require_once "./DB.php";
    require_once "./Log.php";

    class PreUsers {

        private $id;
        private $mail;
        private $token;
        private $expiration;

        public function sendTokenURLMail() {
            $subject = "仮登録完了のお知らせ";
            $message = "以下のURLから、本登録を行ってください。\n\n"."http://192.168.99.100/register?k=".$this->token;
            $headers = "From: from@example.com";

            mail(str_replace(array("\r", "\n"), "", $this->mail), $subject, $message, $headers);
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
                $result = $stmt->fetchAll();
        
                // 登録されていないメールアドレスの場合→新規仮登録し通知
                if (empty($result)) {
                    $stmt = $dbh->prepare("INSERT INTO pre_users values (0, ?, ?, 1, ?)");
                    $stmt->bindValue(1, $this->mail);
                    $stmt->bindValue(2, $this->token);
                    $stmt->bindValue(3, $this->expiration);
                    $stmt->execute();
                    return true;
                }
        
                // 以下、仮登録済み前提

                // 本登録まで済ませている場合
                if ($result[0]["enabled"] == 0) {
                    // その旨をメッセージ表示するviewを展開
                    return false;
                }

                // 本登録がまだの場合は有効期限に問わず、トークンを再発行し通知
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

        private function generateToken() {
            return bin2hex(random_bytes(16));
        }
    }
?>