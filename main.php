<?php

?>

<html>
    <head>
        <script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
        <script src="./js/calendar.js"></script>
        <script src="./js/schedule.js"></script>
    </head>
    <body>
        <div id="calendar-area">
        </div>
        <button id="calendar-change-prev">前月</button>
        <button id="calendar-change-next">次月</button>

        <div id="form-area">
            <form id="form-schedule" method="" action="">
                <input type="text" id="text-title" name="title"/>
                <button type="submit">追加</button>
            </form>
        </div>
        <div id="delete-area">
            <form id="form-delete" method="" action="">
                <button type="submit">削除</button>
            </form>
        </div>
    </body>
</html>