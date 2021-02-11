<?php

    use app\helper\CSRF;
    use app\helper\Helper;
    
?>

<html>
    <head>
        <meta charset="utf-8"/>
        <title>メイン - Calendar</title>
        <meta name="description" content="メインページ" />

        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">

        <script src="./src/js/jquery-3.5.1.min.js"></script>
        <script src="./src/js/main.js"></script>
        <script src="./src/js/utility/Calendar.js"></script>
        <script src="./src/js/utility/security.js"></script>
        <script src="./src/js/utility/const.js"></script>

        <script src="./src/js/component/Component.js"></script>
        <script src="./src/js/component/MainContainer.js"></script>
        <script src="./src/js/component/CalendarComponent.js"></script>
        <script src="./src/js/component/ScheduleComponent.js"></script>

        <link rel="stylesheet" href="./src/css/header.css">
        <link rel="stylesheet" href="./src/css/calendar.css">
        <link rel="stylesheet" href="./src/css/schedule.css">
    </head>
    <body>

        <div>
            <?php include($_SERVER["DOCUMENT_ROOT"]."/src/view/common/header.php"); ?>
        </div>

        <div id="container" class="container">

            <div id="calendar-component" class="calendar">

                <div id="calendar-title" class="calendar-title">
                    <div class="button-area">
                        <button id="calendar-change-prev">
                            <i class="button-prev fas fa-caret-left fa-4x"></i>
                        </button>
                    </div>
                    <div id="calendar-date-info" class="info-date">
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

            </div> <!-- div#calendar-component -->

            <!-- 区切り -->
            <div class="vertical-partition"></div>

            <div id="schedule-list-area" class="schedule-list-area">
                <div id="schedule-list-fulldate" class="schedule-list-fulldate">
                </div>
                <div id="schedule-list-component" class="schedule-list-content">
                    <div id="schedule-list">
                    </div>
                </div>

                <div id="schedule-button-container" class="schedule-button-container">
                    <button data-button-type="add" type="button">新規追加</button>
                    <button data-button-type="delete" type="button">選択した予定をまとめて削除</button>
                </div>

                <div id="schedule-edit-container" class="schedule-edit-container">
                </div>
            </div>

        </div><!-- .container -->

    </body>
</html>