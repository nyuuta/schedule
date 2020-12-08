<?php

?>

<html>
    <head>
        <meta charset="utf-8"/>
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">

        <script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/EventEmitter/5.2.8/EventEmitter.min.js" integrity="sha512-AbgDRHOu/IQcXzZZ6WrOliwI8umwOgLE7sZgRAsNzmcOWlQA8RhXQzBx99Ho0jlGPWIPoT9pwk4kmeeR4qsV/g==" crossorigin="anonymous"></script>
        <script src="./js/main.js"></script>
        <script src="./js/utility/Calendar.js"></script>
        <script src="./js/utility/security.js"></script>
        <script src="./js/utility/const.js"></script>
        <script src="./js/MainContainerComponent.js"></script>
        <script src="./js/CalendarComponent.js"></script>
        <script src="./js/ScheduleListComponent.js"></script>
        <script src="./js/schedule.js"></script>

        <link rel="stylesheet" href="./css/calendar.css">
        <link rel="stylesheet" href="./css/schedule.css">
    </head>
    <body>
        <div id="container" class="container">

            <div id="calendar-area" class="calendar">

                <div id="calendar-title" class="calendar-title">
                    <div class="button-area">
                        <button id="calendar-change-prev">
                            <i class="button-prev fas fa-caret-left fa-4x"></i>
                        </button>
                    </div>
                    <div class="info-date">
                        <div id="calendar-info-year" class="date-year">
                        </div>
                        <div id="calendar-info-month" class="date-month">
                        </div>
                    </div>
                    <div class="button-area">
                        <button id="calendar-change-next">
                            <i class="button-next fas fa-caret-right fa-4x"></i>
                        </button>
                    </div>
                </div>

                <div id="calendar-table">
                    <table class="calendar-table">
                        <tr class="calendar-header">
                            <th>日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th>土</th>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- 区切り -->
            <div class="vertical-partition"></div>

            <div id="schedule-list-area" class="schedule-list-area">
                <div id="schedule-list-fulldate" class="schedule-list-fulldate">
                </div>
                <div class="schedule-list-content">
                    <form id="schedule-list" action="" method="">
                    </form>
                </div>
            </div>

        </div><!-- .container -->

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

        <div id="debug">
        </div>
    </body>
</html>