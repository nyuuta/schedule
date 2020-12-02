class Calendar {

    /**
     * 西暦year年(month+1)月のカレンダー情報
     * 
     * @param {number} year 
     * @param {number:0-11} month 
     */
    constructor(year, month) {
        this.year = year;
        this.month = month;
        this.DATE_TYPE = {
            "EMPTY": 0, "WEEKDAY": 1, "SATURDAY": 2, "SUNDAY": 3, "HOLIDAY": 4
        }
        this.loadHolidayData();
    }

    create() {
        let calendarData = [];
        for (let i = 1; i <= 42; i++) {
            calendarData.push({
                fulldate: "", date: "", day: "",
                holidayName: "",
                dateType: this.DATE_TYPE.EMPTY
            });
        }

        // 当月の日数と1日の曜日を取得
        let dayOfFirstDate = new Date(this.year, this.month).getDay();
        let dateCount = new Date(this.year, this.month + 1, 0).getDate();
        
        for (let i = 1; i <= dateCount; i++) {

            let day = (dayOfFirstDate + (i - 1)) % 7;
            let dateObjEx = this.getDateObjectEx(this.year, this.month, i, day);

            calendarData[i + dayOfFirstDate - 1] = {
                fulldate: this.year + "-" + (this.month + 1) + "-" + i,
                date: i,
                day: day,
                holidayName: dateObjEx.holidayName,
                dateType: dateObjEx.dateType
            }
        }
        return calendarData;

    }

    getDateObjectEx(year, month, date, day) {

        let dateObjEx = new Date(year, month, date);
        dateObjEx.holidayName = this.isHoliday(year, month, date);

        if (dateObjEx.holidayName !== "") {
            dateObjEx.dateType = this.DATE_TYPE.HOLIDAY;
            return dateObjEx;
        }

        switch (day) {
            case 0:
                dateObjEx.dateType = this.DATE_TYPE.SUNDAY;
                break;
            case 6:
                dateObjEx.dateType = this.DATE_TYPE.SATURDAY;
                break;
            default:
                dateObjEx.dateType = this.DATE_TYPE.WEEKDAY;
                break;
        }

        return dateObjEx;
    }

    loadHolidayData() {
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

    
}