<?php

    require_once "./DB.php";

    // TODO: validation
    echo(var_dump($_POST));

    // UPDATE文で対象の予定を変更
    $dbh = DB::singleton()->get();
    $stmt = $dbh->prepare("UPDATE schedules SET title = :title WHERE id = :id");
    $stmt->execute($_POST);

    // TODO:例外処理
    echo(json_encode(""));
    exit;
?>