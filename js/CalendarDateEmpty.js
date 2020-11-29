class CalendarDateEmpty {

    constructor(year, month, day, weekday, holiday) {
        this.year = year;
        this.month = month;
        this.day = day;
        this.weekday = weekday;
        this.date = [this.year, this.month, this.day].join("-");
        this.holiday = holiday;
    }

    toTableCellElement() {

        let $div_day = $("<div>").text("").addClass("calendar-day");
        let $div_holiday = $("<div>").text("").addClass("calendar-holiday-name");

        let $ret = $("<div class='calendar-day-info'>").append($div_day);

        $ret.append($div_holiday).attr({
            "data-date": this.date,
            "data-weekday": this.weekday,
        })

        return $ret;
    }

    getStyle() {
        return "calendar-day";
    }

}