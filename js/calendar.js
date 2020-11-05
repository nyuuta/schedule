$(document).ready(function () {

    const showCalendar = function (calendar) {
        $("#calendar-area").empty();
        let $title = $("<p>").text(date_current.getFullYear() + "年" + (date_current.getMonth() + 1) + "月");
        $("#calendar-area").append($title);
        let $table = $("<table>");
        for (let i = 1; i <= 6; i++) {

            let table_row = $("<tr>");
            for (let j = 1; j <= 7; j++) {
                let place = 7 * (i - 1) + j - 1;
                let table_cell = $("<td>").text(calendar[place].date);
                table_cell.attr("data-year", calendar[place].year);
                table_cell.attr("data-month", calendar[place].month);
                table_cell.attr("data-day", calendar[place].day);
                table_row.append(table_cell);
            }
            $table.append(table_row);
        }
        $("#calendar-area").append($table);
    }

    const createCalendarData = function (year, month) {

        if (year < 2000) {
            year = 2000;
        }

        if (year > 9999) {
            year = 9999;
        }

        // 当月、先月、来月のDateオブジェクト生成(month:0~11)
        let date = new Date(year, month - 1);
        let date_next = new Date(year, month);
        let date_last = new Date(year, month - 2);

        // 当月1日の曜日と当月最終日を取得
        let day_first = date.getDay();
        let count_date = new Date(year, month, 0).getDate();

        // 先月最終日
        let count_date_last = new Date(year, month - 1, 0).getDate();

        // カレンダー情報に日曜日と1日の曜日との差分の数だけ先月の情報を追加
        let calendar = [];
        console.log(day_first);
        for (let i = 0; i < day_first; i++) {
            calendar.unshift({
                year: date_last.getFullYear(),
                month: date_last.getMonth() + 1,
                date: count_date_last - i,
                day: Math.abs((day_first - (i + 1)) % 7)
            });
        }

        for (let i = 1; i <= count_date; i++) {
            calendar.push({
                year: date.getFullYear(),
                month: date.getMonth() + 1,
                date: i,
                day: Math.abs((day_first + (i - 1)) % 7)
            });
        }

        let rest = 42 - calendar.length;
        for (let i = 1; i <= rest; i++) {

            calendar.push({
                year: date_next.getFullYear(),
                month: date_next.getMonth()+1,
                date: i,
                day: Math.abs((date_next.getDay()+(i-1)) % 7)
            });
        }

        return calendar;
    }

    // カレンダーの月移動ボタン押下時
    $("button[id^='calendar-change']").on("click", function () {
        
        console.log("click change button.");

        let amount_change = ($(this).attr("id").endsWith("next")) ? +1 : -1;
        date_current.setMonth(date_current.getMonth() + amount_change);
        
        let calendar = createCalendarData(date_current.getFullYear(), date_current.getMonth()+1);
        showCalendar(calendar);
    });

    // アクセス時の日付データ取得
    let date_current = new Date();
    let calendar = createCalendarData(date_current.getFullYear(), date_current.getMonth()+1);
    showCalendar(calendar);
});