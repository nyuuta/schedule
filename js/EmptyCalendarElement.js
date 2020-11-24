class EmptyCalendarElement {

    constructor() {

    }

    toTableCellElement() {

        let $div_day = $("<div>").addClass("calendar-day");
        let $div_schedule = $("<div>").addClass("calendar-schedule");
        let $table_cell = $("<td>").addClass("calendar-element");
        $table_cell.append($div_day);
        $table_cell.append($div_schedule);

        return $table_cell;
    }

}