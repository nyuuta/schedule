const DAYMAP = {0: "日", 1: "月", 2: "火", 3: "水", 4: "木", 5: "金", 6: "土" };
const DATE_TYPE = { "EMPTY": 0, "WEEKDAY": 1, "SATURDAY": 2, "SUNDAY": 3, "HOLIDAY": 4 };
const STYLE = { 0: "calendar-empty", 1: "calendar-day", 2: "calendar-saturday", 3: "calendar-sunday", 4: "calendar-holiday" };
const CALENDAR_LIMIT = 50;

const EVENT = {
    SCHEDULE_CHANGE: "schedule has changed.",
    DATE_CHANGE: "selected date has changed.",
    DONE_INIT: "init component has done.",
    START: "start component"
};