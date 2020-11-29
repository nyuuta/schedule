class Calendar {

    /**
     * @var {num} year_current
     * @var {num:0-11} month_current
     */
    
    constructor() {
        this.calendar = [];
        this.year_current;
        this.month_current;
        this.holidays = [];

        this.MAP_WEEKDAY = ["日", "月", "火", "水", "木", "金", "土"];
    }

    /**
     * 指定された年月でカレンダーを初期化
     * 
     */
    init(year, month) {

        this.getHolidayData();

        this.isHoliday(2020, 4, 6);

        this.calendar = [];

        // 入力値チェック
        let year_now = new Date().getFullYear();
        let year_limit_upper = year_now + 100;
        let year_limit_lower= year_now - 100;

        year = (year < year_limit_lower) ? year_limit_lower : year;
        year = (year > year_limit_upper) ? year_limit_upper : year;

        month = (month >= 0 && month <= 11) ? month : 0;

        // 42個全て空白で埋めてしまい、その後(1日の曜日:0~6番目から末日までを埋める)

        // 1日の曜日と最終日を取得
        let weekday_first = new Date(year, month).getDay();
        let date_last = new Date(year, month+1, 0).getDate();

        for (let i = 0; i < weekday_first; i++) {
            // this.calendar.push(new EmptyCalendarElement());
            this.calendar.push(new CalendarDateEmpty(year, month, i, 0));
        }
        for (let i = 1; i <= date_last; i++) {
            let weekday = Math.abs((weekday_first + (i - 1)) % 7);
            // this.calendar.push(new CalendarElement(year, month, i, weekday));
            this.calendar.push(this.generateDateObject(year, month, i, weekday));

            console.log(this.generateDateObject(year, month, i, weekday));

        }
        for (let i = date_last+weekday_first+1; i <= 42; i++) {
            // this.calendar.push(new EmptyCalendarElement());
            this.calendar.push(new CalendarDateEmpty(year, month, i, 0));

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

                let $table_cell = $("<td>").addClass("calendar-element");

                $table_cell.append(this.calendar[place].toTableCellElement());
                $table_cell.append("<div class='calendar-schedule'>");
                $table_cell.addClass(this.calendar[place].getStyle());
                table_row.append($table_cell);
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

    getCurrentYear() {
        return this.year_current;
    }

    getCurrentMonth() {
        return this.month_current;
    }

    getHolidayData() {
        let req = new XMLHttpRequest();
        req.open("GET", "js/syukujitsu.csv", false);
        req.send();
        this.holidays = req.responseText.split("\n");
    }

    /**
     * year年month月day日が国民の祝日かどうかを判定
     * 
     * 内閣府が出している国民の祝日一覧に従う（特例含め最新の情報が載っているため）
     * 
     * @param {number} year 
     * @param {number} month 
     * @param {number} day 
     */
    isHoliday(year, month, day) {

        let target = year + "/" + (month + 1) + "/" + day;
        for (let i = 0; i < this.holidays.length; i++) {
            let data_holiday = this.holidays[i].split(",");
            if (data_holiday[0] === target) {
                return data_holiday[1];
            }
        }
        return "";
    }

    generateDateObject(year, month, day, weekday) {

        let holiday_name = this.isHoliday(year, month, day);
        if (holiday_name !== "") {
            return new CalendarDateHoliday(year, month, day, weekday, holiday_name);
        }

        if (weekday === 0) {
            return new CalendarDateSunday(year, month, day, weekday, holiday_name);
        } else if (weekday === 6) {
            return new CalendarDateSaturday(year, month, day, weekday, holiday_name);
        } else {
            return new CalendarDateNormal(year, month, day, weekday, holiday_name);
        }
    }
}