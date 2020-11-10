<?php

    require_once "./DB.php";

    // TODO: validation

    // SELECT文で予定を取得
    $dbh = DB::singleton()->get();
    $stmt = $dbh->prepare("SELECT * FROM schedules where date >= :date_begin and date <= :date_end");
    $stmt->execute($_GET);
    $schedules = $stmt->fetchAll();

    // TODO:例外処理
    echo(json_encode($schedules));
    exit;
?>