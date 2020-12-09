<?php

    require_once "./DB.php";
    require_once "./Log.php";

    // TODO: user_idはユーザ認証を実装後、取得

    $response = array(
        "status" => "ok",
        "messages" => "",
        "data" => array(),
    );

    $response["messages"] = validate($_POST["title"], $_POST["date"], $_POST["day"]);

    if (count($response["messages"]) !== 0) {
        $response["status"] = "ng";
        echo(json_encode($response));
        exit;
    }

    $year = explode("-", $_POST["date"])[0];
    $month = explode("-", $_POST["date"])[1];

    // DBにinsert
    try {
        $dbh = DB::singleton()->get();
        $stmt = $dbh->prepare("INSERT INTO schedules values (0, :user_id, :title, :date, :day)");
        $stmt->execute($_POST);

        $stmt = $dbh->prepare("SELECT id, user_id, title, DATE_FORMAT(date, '%Y-%c-%e') as date FROM schedules where (DATE_FORMAT(date, '%Y%c') = ?)");
        $stmt->execute(array($year.$month));

        $response["data"] = groupSchedulesByDate($stmt->fetchAll());
        echo(json_encode($response));
        exit;
    } catch (PDOException $e) {
        Log::error($e->getMessage());
        header('HTTP/1.1 500 Internal Server Error');
        echo(json_encode($response));
        exit;
    }

    /**
     * 入力値のバリデーション
     * title: 文字数が1文字以上32文字以内 ※空白文字のみの場合はNG
     * date: 日付のフォーマット(yyyy年mm月dd日)
     * day: 0-6
     */
    function validate($title, $fulldate, $day) {

        $messages = array();

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

        return $messages;
    }

    function groupSchedulesByDate($schedules) {
        $groupedSchedules = array();
        foreach ($schedules as $schedule) {
            $groupedSchedules[$schedule["date"]][] = $schedule;
        }
        return $groupedSchedules;
    }
?>