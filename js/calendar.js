class Calendar {

    /**
     * @var {num} year_current
     * @var {num:0-11} month_current
     */
    
    // TODO: 祝日
    
    constructor() {
        this.calendar = [];
        this.year_current;
        this.month_current;

        this.MAP_WEEKDAY = ["日", "月", "火", "水", "木", "金", "土"];
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
        let date_last = new Date(year, month+1, 0).getDate();

        console.log(weekday_first);
        console.log(date_last);

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
        $("#calendar-table").empty();
        $("#date-year").text(this.year_current);
        $("#date-month").text(this.month_current + 1);
        let $table = $("<table>").addClass("calendar-table");

        // 曜日
        let table_row = $("<tr>").addClass("calendar-header");
        for (let i = 0; i <= 6; i++) {
            let table_header = $("<th>");
            table_header.text(this.MAP_WEEKDAY[i]);
            table_row.append(table_header);
        }
        $table.append(table_row);

        for (let i = 1; i <= 6; i++) {

            table_row = $("<tr>");
            for (let j = 1; j <= 7; j++) {
                let place = 7 * (i - 1) + j - 1;
                let table_cell = this.calendar[place].toTableCellElement();
                table_row.append(table_cell);
            }
            $table.append(table_row);
        }
        $("#calendar-table").append($table);
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