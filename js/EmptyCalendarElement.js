class EmptyCalendarElement {

    constructor() {

    }

    toTableCellElement() {

        let $div = $("<div>").text("");
        let $table_cell = $("<td>").addClass("calendar-element");
        $table_cell.append($div);

        return $table_cell;
    }

}