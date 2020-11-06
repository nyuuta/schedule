<?php

    require_once "./DB.php";

    // TODO: validation

    // DBにinsert
    $dbh = DB::singleton()->get();
    $stmt = $dbh->prepare(
        "INSERT INTO schedules values (0, :user_id, :title, :year, :month, :date, :day)"
    );
    $stmt->execute($_POST);

    // TODO:例外処理
    echo(json_encode("test."));
    exit;
?>