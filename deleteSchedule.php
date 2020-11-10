<?php

    require_once "./DB.php";

    // TODO: validation
    echo(var_dump($_POST));

    // DELETE文で対象の予定を削除
    $dbh = DB::singleton()->get();
    $stmt = $dbh->prepare("DELETE FROM schedules WHERE id = :id");
    $stmt->execute($_POST);

    // TODO:例外処理
    echo(json_encode(""));
    exit;
?>