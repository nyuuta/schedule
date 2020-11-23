class Calendar {

    /**
     * @var {num} year_current
     * @var {num:0-11} month_current
     */

    constructor() {
        this.calendar = [];
        this.year_current;
        this.month_current;
    }

    /**
     * 指定された年月でカレンダーを初期化
     * 
     */
    init(year, month) {

        this.calendar = [];

        // 入力値チェック
        let year_now = new Date().getFullYear();
        let year_limit_upper = year_now + 100;
        let year_limit_lower= year_now - 100;

        year = (year < year_limit_lower) ? year_limit_lower : year;
        year = (year > year_limit_upper) ? year_limit_upper : year;

        month = (month >= 0 && month <= 11) ? month : 0;

        // 1日の曜日と最終日を取得
        let weekday_first = new Date(year, month).getDay();
        let date_last = new Date(year, month, 0).getDate();

        for (let i = 0; i < weekday_first; i++) {
            this.calendar.push(new EmptyCalendarElement());
        }
        for (let i = 1; i <= date_last; i++) {
            let weekday = Math.abs((weekday_first + (i - 1)) % 7);
            this.calendar.push(new CalendarElement(year, month, i, weekday));
        }
        for (let i = date_last+weekday_first+1; i <= 42; i++) {
            this.calendar.push(new EmptyCalendarElement());
        }

        this.year_current = year;
        this.month_current = month;
    }

    show() {
        $("#calendar-area").empty();
        let $title = $("<p>").text(this.year_current + "年" + (this.month_current+1) + "月");
        $("#calendar-area").append($title);
        let $table = $("<table>");
        for (let i = 1; i <= 6; i++) {

            let table_row = $("<tr>");
            for (let j = 1; j <= 7; j++) {
                let place = 7 * (i - 1) + j - 1;
                let table_cell = this.calendar[place].toTableCellElement();
                table_row.append(table_cell);
            }
            $table.append(table_row);
        }
        $("#calendar-area").append($table);
    }

    next() {
        this.month_current++;
        let date_new = new Date(this.year_current, this.month_current);
        this.init(date_new.getFullYear(), date_new.getMonth());
        this.show();
    }

    prev() {
        this.month_current--;
        let date_new = new Date(this.year_current, this.month_current);
        this.init(date_new.getFullYear(), date_new.getMonth());
        this.show();
    }
}