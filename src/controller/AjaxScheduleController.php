<?php

    namespace app\controller;

    use app\helper\Session;
    use app\helper\Helper;
    use app\helper\DB;
    use app\model\PreUsers;
    use app\model\Users;

    use PDOException;

    class AjaxScheduleController {

        public static function read() {

            session_start();

            $logger = new \app\helper\Log();
            $logger->info("START AjaxScheduleController@read");

            $response = array(
                "status" => "ok",
                "message" => "",
                "schedules" => []
            );

            $userID = Users::getUserID();

            // ログイン状態ではない場合は失敗
            if ($userID === false) {
                $response["status"] = "ng";
                $response["message"] = "ログインしてください。";
                $logger->info("END user isn't login");
                echo(json_encode($response));
                exit;
            }

            // パラメータ不適正の場合は失敗
            if ((!$year = filter_input(INPUT_GET, "year")) || (!$month = filter_input(INPUT_GET, "month")+1)) {
                $response["status"] = "ng";
                $response["message"] = "年と月を入力してください。";
                $logger->info("END invalid parameter");
                echo(json_encode($response));
                exit;
            }

            // 年月が妥当な数値ではない場合は失敗
            if (!self::validate($year, $month)) {
                $response["status"] = "ng";
                $response["message"] = "年月が不適切です。(年:1000~9999, 月:1~12)";
                $logger->info("END invalid parameter");
                echo(json_encode($response));
                exit;
            }

            try {
                $dbh = DB::singleton()->get();
                $stmt = $dbh->prepare("SELECT id, user_id, title, progress, DATE_FORMAT(date, '%Y-%c-%e') as date FROM schedules where (DATE_FORMAT(date, '%Y%c') = ?) AND user_id = ?");
                $stmt->execute(array($year.$month, $userID));
                $response["schedules"] = self::groupSchedulesByDate($stmt->fetchAll());
                $logger->info("END OK");
                echo(json_encode($response));
                exit;
            } catch (PDOException $e) {
                $logger->error($e->getMessage());
                $logger->error("END");
                header( $_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
                echo(json_encode($response));
                exit;
            }
        }

        public static function create() {

            session_start();

            $logger = new \app\helper\Log();
            $logger->info("START AjaxScheduleController@create");

            $response = array(
                "status" => "ok",
                "message" => "",
                "schedules" => []
            );

            $userID = Users::getUserID();

            // ログイン状態ではない場合は失敗
            if ($userID === false) {
                $response["status"] = "ng";
                $response["message"] = "ログインしてください。";
                $logger->info("END user isn't login");
                echo(json_encode($response));
                exit;
            }

            // パラメータ不適正の場合は失敗
            if ((!$title = filter_input(INPUT_POST, "title")) || (!$dateStr = filter_input(INPUT_POST, "date")) || (!$day = filter_input(INPUT_POST, "day"))) {
                $response["status"] = "ng";
                $response["message"] = "スケジュールのタイトルと日付と曜日を入力してください。";
                $logger->info("END invalid parameter");
                echo(json_encode($response));
                exit;
            }

            // パラメータが妥当な数値ではない場合は失敗
            list($success, $response["message"]) = self::validateSchedule($title, $dateStr, $day);
            if ($success === false) {
                $response["status"] = "ng";
                $logger->info("END invalid parameter");
                echo(json_encode($response));
                exit;
            }

            list($year, $month, $date) = explode("-", $dateStr);

            // DBにinsert
            try {
                $dbh = DB::singleton()->get();
                $stmt = $dbh->prepare("INSERT INTO schedules values (0, ?, ?, ?, ?, 0)");
                $stmt->execute(array($userID, $title, $dateStr, $day));

                $response["data"]["id"] = $dbh->lastInsertId();
                $logger->info("END OK");
                echo(json_encode($response));
                exit;
            } catch (PDOException $e) {
                $logger->error($e->getMessage());
                $logger->error("END");
                header( $_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
                echo(json_encode($response));
                exit;
            }
        }

        public static function update() {

            session_start();

            $logger = new \app\helper\Log();
            $logger->info("START AjaxScheduleController@update");

            $response = array(
                "status" => "ok",
                "message" => "",
                "schedules" => []
            );

            $userID = Users::getUserID();

            // ログイン状態ではない場合は失敗
            if ($userID === false) {
                $response["status"] = "ng";
                $response["message"] = "ログインしてください。";
                $logger->info("END user isn't login");
                echo(json_encode($response));
                exit;
            }

            // パラメータ不適正の場合は失敗
            if ( (!$title = filter_input(INPUT_POST, "title")) || (!$id = filter_input(INPUT_POST, "id")) ) {
                $response["status"] = "ng";
                $response["message"] = "スケジュールのタイトルを入力してください。";
                $logger->info("END invalid parameter");
                echo(json_encode($response));
                exit;
            }

            // パラメータが妥当な値ではない場合は失敗
            list($success, $response["message"]) = self::validateTitle($title);
            if ($success === false) {
                $response["status"] = "ng";
                $logger->info("END invalid parameter");
                echo(json_encode($response));
                exit;
            }

            // update
            try {
                $dbh = DB::singleton()->get();
                $stmt = $dbh->prepare("UPDATE schedules SET title = ? WHERE id = ?");
                $stmt->execute(array($title, $id));
                $logger->info("END OK");
                echo(json_encode($response));
                exit;
            } catch (PDOException $e) {
                $logger->error($e->getMessage());
                $logger->error("END");
                header( $_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
                echo(json_encode($response));
                exit;
            }
        }

        public static function delete() {

            session_start();

            $logger = new \app\helper\Log();
            $logger->info("START AjaxScheduleController@delete");

            $response = array(
                "status" => "ok",
                "message" => "",
                "schedules" => []
            );

            $userID = Users::getUserID();

            // ログイン状態ではない場合は失敗
            if ($userID === false) {
                $response["status"] = "ng";
                $response["message"] = "ログインしてください。";
                $logger->info("END user isn't login");
                echo(json_encode($response));
                exit;
            }

            // パラメータ不適正の場合は失敗
            if ( (!$ids = filter_input(INPUT_POST, "ids", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)) ) {
                $response["status"] = "ng";
                $response["message"] = "削除するスケジュールを選択してください。";
                $logger->info("END invalid parameter");
                echo(json_encode($response));
                exit;
            }

            $inClause = substr(str_repeat(",?", count($ids)), 1);

            // DBからDELTE
            try {
                $dbh = DB::singleton()->get();
                $stmt = $dbh->prepare("DELETE FROM schedules WHERE id in ({$inClause})");
                $stmt->execute($ids);
                $logger->info("END OK");
                echo(json_encode($response));
                exit;
            } catch (PDOException $e) {
                $logger->error($e->getMessage());
                $logger->error("END");
                header( $_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
                echo(json_encode($response));
                exit;
            }
        }

        private static function groupSchedulesByDate($schedules) {
            $groupedSchedules = array();
            foreach ($schedules as $schedule) {
                $groupedSchedules[$schedule["date"]][] = $schedule;
            }
            return $groupedSchedules;
        }

        /**
         * 入力値のバリデーション
         * 西暦年は1000～9999
         * 月は0～11
         */
        private static function validate($year, $month) {

            if (!(preg_match("/^[0-9]+$/", $month) && $month >= 1 && $month <= 12)) {
                return false;
            }
            if (!(preg_match("/^[0-9]+$/", $year) && $year >= 1000 && $year <= 9999)) {
                return false;
            }

            return true;
        }

        /**
         * 入力値のバリデーション
         * title: 文字数が1文字以上32文字以内 ※空白文字のみの場合はNG
         * date: 日付のフォーマット(yyyy年mm月dd日)
         * day: 0-6
         */
        private static function validateSchedule($title, $fulldate, $day) {

            $messages = array();
            $success = false;

            // スケジュール名は空白以外の文字
            if ((preg_match("/^[\s]+$/", $title)) || (mb_strlen($title) > 32)) {
                $messages[] = "1~32文字で入力してください。（空白文字のみはNGです。）";
            }

            // 日付のフォーマットに従った上で妥当な日付
            if (!(preg_match("/\A[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}\z/", $fulldate))) {
                $messages[] = "日付のフォーマットが不適切です。";
            }

            $separated = explode("-", $fulldate);
            if (!checkdate($separated[1], $separated[2], $separated[0])) {
                $messages[] = "存在しない日付です。";
            }

            // 曜日は0-6の数値
            if (!(preg_match("/^[0-9]+$/", $day) && $day >= 0 && $day <= 6)) {
                $messages[] = "曜日が不適切です。";
            }

            if (count($messages) === 0) {
                $success = true;
            }

            return [$success, $messages];
        }

        /**
         * 入力値のバリデーション
         * title: 文字数が1文字以上32文字以内 ※空白文字のみの場合はNG
         */
        private static function validateTitle($title) {

            $messages = array();
            $success = false;

            // スケジュール名は空白以外の文字
            if ((preg_match("/^[\s]+$/", $title)) || (mb_strlen($title) > 32)) {
                $messages[] = "1~32文字で入力してください。（空白文字のみはNGです。）";
            }

            if (count($messages) === 0) {
                $success = true;
            }

            return [$success, $messages];
        }

    }
?>