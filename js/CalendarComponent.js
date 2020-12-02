class CalendarComponent {

    constructor($rootEl) {

        this.DATE_TYPE = {
            "EMPTY": 0, "WEEKDAY": 1, "SATURDAY": 2, "SUNDAY": 3, "HOLIDAY": 4
        }

        this.STYLE = {
            0: "calendar-empty", 1: "calendar-day",
            2: "calendar-saturday", 3: "calendar-sunday",
            4: "calendar-holiday"
        }

        this.CALENDAR_LIMIT = 50;

        this.$rootEl = $rootEl;

        this.initViewModel();
        this.buildElements();
        this.attachEvents();
        this.updateView();
    }

    // viewの状態定義
    initViewModel() {

        // カレンダーとして表示する年月状態
        this.selectedFulldate = new Date();

        // 月移動ボタンの有効/無効状態
        this.visiblePrevButton = true;
        this.visibleNextButton = true;
    }

    // コンポーネントで使用する要素の構築、子コンポーネントの作成
    buildElements() {

        // カレンダーの情報（何年、何月）要素
        this.$calendarInfoYear = this.$rootEl.find("#calendar-info-year");
        this.$calendarInfoMonth = this.$rootEl.find("#calendar-info-month");

        // カレンダーテーブルの雛型
        this.$calendarTable = this.$rootEl.find("#calendar-table");

        // ボタン
        this.$nextButton = this.$rootEl.find("#calendar-change-next");
        this.$prevButton = this.$rootEl.find("#calendar-change-prev");

    }

    // イベントの割り当て
    attachEvents() {

        // 月移動ボタン押下時に表示月を変更してupdateView()に任せる
        let _this = this;
        this.$rootEl.on("click", "#calendar-change-prev", function (evt) {
            _this.selectedFulldate.setMonth(_this.selectedFulldate.getMonth() - 1);
            _this.visiblePrevButton = _this.canPrevMonth();
            _this.visibleNextButton = _this.canNextMonth();
            _this.updateView();
        });

        this.$rootEl.on("click", "#calendar-change-next", function (evt) {
            _this.selectedFulldate.setMonth(_this.selectedFulldate.getMonth() + 1);
            _this.visiblePrevButton = _this.canPrevMonth();
            _this.visibleNextButton = _this.canNextMonth();
            _this.updateView();
        });
    }

    // viewの更新
    updateView() {

        // カレンダーデータの表示
        let calendar = new Calendar(this.selectedFulldate.getFullYear(), this.selectedFulldate.getMonth());
        let calendarData = calendar.create();
        this.showCalendar(calendarData);

        // ボタンの有効/無効viewの更新
        if (this.visiblePrevButton) {
            this.$prevButton.removeClass("fa-disabled");
            this.$prevButton.prop("disabled", false);
        } else {
            this.$prevButton.addClass("fa-disabled");
            this.$prevButton.prop("disabled", true);
        }

        if (this.visibleNextButton) {
            this.$nextButton.removeClass("fa-disabled");
            this.$nextButton.prop("disabled", false);
        } else {
            this.$nextButton.addClass("fa-disabled");
            this.$nextButton.prop("disabled", true);
        }
    }

    showCalendar(calendarData) {

        this.$calendarTable.find("tr").not(":first").remove();
        this.$calendarInfoYear.text(this.selectedFulldate.getFullYear());
        this.$calendarInfoMonth.text(this.selectedFulldate.getMonth()+1);

        let $row = $("<tr>");

        for (let i = 0; i <= 41; i++) {

            let $tableCell = $("<td>", {
                "class": `calendar-element ${this.STYLE[calendarData[i].dateType]}`,
                "data-fulldate": calendarData[i].fulldate,
                "data-day": calendarData[i].day,
            });

            let $calendarDayInfo = $("<div>", {
                "class": "calendar-day-info"
            });
            $calendarDayInfo.append($("<div>", {
                "class": "calendar-day"
            }).text(calendarData[i].date));
            $calendarDayInfo.append($("<div>", {
                "class": "calendar-holiday-name"
            }).text(calendarData[i].holidayName));

            $tableCell.append($calendarDayInfo);
            $tableCell.append($("<div>", {
                "class": "calendar-schedule"
            }));

            $row.append($tableCell);

            if (i % 7 === 6) {
                this.$calendarTable.find("tbody").append($row);
                $row = $("<tr>");
            }
        }
    }

    canNextMonth() {
        if ((this.selectedFulldate.getFullYear() === new Date().getFullYear() + this.CALENDAR_LIMIT) && (this.selectedFulldate.getMonth() === 11)) {
            return false;
        } 
        return true;
    }

    canPrevMonth() {
        if ((this.selectedFulldate.getFullYear() === new Date().getFullYear() - this.CALENDAR_LIMIT) && (this.selectedFulldate.getMonth() === 0)) {
            return false;
        } 
        return true;
    }
}