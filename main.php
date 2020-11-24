<?php

?>

<html>
    <head>

        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">

        <script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
        <script src="./js/main.js"></script>
        <script src="./js/Calendar.js"></script>
        <script src="./js/EmptyCalendarElement.js"></script>
        <script src="./js/CalendarElement.js"></script>
        <script src="./js/schedule.js"></script>

        <link rel="stylesheet" href="./css/calendar.css">
    </head>
    <body>
        <div id="calendar-area" class="calendar">

            <div id="calendar-title" class="calendar-title">
                <div class="button-area">
                    <button id="calendar-change-prev">
                        <i class="button-prev fas fa-caret-left fa-4x"></i>
                    </button>
                </div>
                <div class="info-date">
                    <div id="date-year" class="date-year">
                    </div>
                    <div id="date-month" class="date-month">
                    </div>
                </div>
                <div class="button-area">
                    <button id="calendar-change-next">
                        <i class="button-next fas fa-caret-right fa-4x"></i>
                    </button>
                </div>
            </div>

            <div id="calendar-table">
            </div>
        </div>
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
        <div id="update-area">
            <form id="form-update" method="" action="">
                <input type="text" id="text-update" name="content"/>
                <button type="submit">更新</button>
            </form>
        </div>
    </body>
</html>