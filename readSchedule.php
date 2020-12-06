<?php

    require_once "./DB.php";

    $year = $_GET["year"];
    $month = $_GET["month"];

    $response = array(
        "status" => "ok",
        "message" => "",
        "schedules" => []
    );

    if (!validate($year, $month)) {
        $response["status"] = "ng";
        $response["message"] = "年月が不適切です。(年:1000~9999, 月:0~11)";
        echo(json_encode($response));
        exit;
    }

    // SELECT文で予定を取得
    try {
        $dbh = DB::singleton()->get();
        $stmt = $dbh->prepare("SELECT * FROM schedules where (DATE_FORMAT(date, '%Y%c') = ?)");
        $stmt->execute(array($year.$month));
        $response["schedules"] = groupSchedulesByDate($stmt->fetchAll());
        echo(json_encode($response));
        exit;
    } catch (PDOException $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo(json_encode($response));
        exit;
    }

    function groupSchedulesByDate($schedules) {
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
    function validate($year, $month) {

        if (!(preg_match("/^[0-9]+$/", $month) && $month >= 0 && $month <= 11)) {
            return false;
        }
        if (!(preg_match("/^[0-9]+$/", $year) && $year >= 1000 && $year <= 9999)) {
            return false;
        }

        return true;
    }
?>