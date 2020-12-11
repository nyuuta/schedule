<?php

    require_once "./DB.php";
    require_once "./Log.php";

    $response = array(
        "status" => "ok",
        "messages" => "",
        "data" => array(),
    );

    $inClause = substr(str_repeat(',?', count($_POST["ids"])), 1);

    // DBからDELTE
    try {
        $dbh = DB::singleton()->get();
        $stmt = $dbh->prepare("DELETE FROM schedules WHERE id in ({$inClause})");
        $stmt->execute($_POST["ids"]);

        echo(json_encode($response));
        exit;
    } catch (PDOException $e) {
        Log::error($e->getMessage());
        header('HTTP/1.1 500 Internal Server Error');
        echo(json_encode($response));
        exit;
    }
?>