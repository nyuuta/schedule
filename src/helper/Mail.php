<?php

    namespace app\helper;

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    use app\helper\Log;

    class Mail {

        public function mail($to, $subject, $body) {
            
            // インスタンスを生成（引数に true を指定して例外 Exception を有効に）
            $mail = new PHPMailer(true);
            
            //日本語用設定
            // $mail->CharSet = "iso-2022-jp";
            // $mail->Encoding = "7bit";
            $mail->CharSet = "UTF-8";

            // 環境変数から設定を読み込み
            $debug = (int)($_SERVER["MAIL_DEBUG"]);
            $host = $_SERVER["MAIL_HOST"];
            $user = $_SERVER["MAIL_USER"];
            $password = $_SERVER["MAIL_PASSWORD"];
            $port = (int)($_SERVER["MAIL_PORT"]);
            $from = $_SERVER["MAIL_FROM"];

            try {
                //サーバの設定
                $mail->isSMTP();
                $mail->SMTPDebug = $debug;
                $mail->Host       = $host;
                $mail->Username   = $user;
                $mail->Password   = $password;
                $mail->Port       = $port;
                $mail->SMTPAuth   = true;
                $mail->SMTPSecure = true;
            
                //受信者設定 
                $mail->setFrom($from);  
                $mail->addAddress($to);
            
                //コンテンツ設定
                // $mail->isHTML(true);
                $mail->Subject = $subject; 
                $mail->Body  = $body;  

                // 送信
                $mail->send();
            } catch (Exception $e) {
                // 例外は利用側に処理を任せる
                Log::error($mail->ErrorInfo);
                throw $e;
            }
        }
    }
?>