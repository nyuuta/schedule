class CalendarElement {

    constructor(year, month, day, weekday) {
        this.year = year;
        this.month = month;
        this.day = day;
        this.weekday = weekday;
        this.date = [this.year, this.month, this.day].join("-");
    }

    toTableCellElement() {

        let $div = $("<div>").text(this.day);
        let $table_cell = $("<td>").addClass("calendar-element").attr({
            "data-date": this.date,
            "data-weekday": this.weekday,
        })
        $table_cell.append($div);

        return $table_cell;
    }

}