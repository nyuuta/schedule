class CalendarDateSunday {

    constructor(year, month, day, weekday) {
        this.year = year;
        this.month = month;
        this.day = day;
        this.weekday = weekday;
        this.date = [this.year, this.month, this.day].join("-");
    }

    toTableCellElement() {

        let $div_day = $("<div>").text(this.day).addClass("calendar-day");
        let $div_holiday = $("<div>").text("").addClass("calendar-holiday-name");;

        let $ret = $("<div class='calendar-day-info'>").append($div_day);

        $ret.append($div_holiday).attr({
            "data-date": this.date,
            "data-weekday": this.weekday,
        })
        return $ret;
    }

    getStyle() {
        return "calendar-sunday";
    }

}