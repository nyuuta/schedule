<?php

    require_once "./DB.php";
    require_once "./Log.php";

    $response = array(
        "status" => "ok",
        "messages" => "",
        "data" => array(),
    );

    $response["messages"] = validate($_POST["title"]);

    if (count($response["messages"]) !== 0) {
        $response["status"] = "ng";
        echo(json_encode($response));
        exit;
    }

    // update
    try {
        $dbh = DB::singleton()->get();
        $stmt = $dbh->prepare("UPDATE schedules SET title = ? WHERE id = ?");
        $stmt->execute(array($_POST["title"], $_POST["id"]));

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
     */
    function validate($title) {

        $messages = array();

        // スケジュール名は空白以外の文字
        if ((preg_match("/^[\s]+$/", $title)) || (mb_strlen($title) > 32)) {
            $messages[] = "1~32文字で入力してください。（空白文字のみはNGです。）";
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