class CalendarComponent {

    constructor($rootEl, option) {

        this.$rootEl = $rootEl;
        this.eventEmitter = option.eventEmitter;
        this.deferred = $.Deferred();

        let _this = this;

        _this.initViewModel();

        this.deferred.promise().then(() => {

            _this.deferred = $.Deferred();
            _this.buildElements();
            _this.attachEvents();
            _this.updateView();
            _this.eventEmitter.emit(EVENT.DONE_INIT, _this.selectedDateObj, _this.scheduleList);
        });
    }

    /**
     * viewの表示に必要なデータや状態を定義
     */
    initViewModel() {

        // カレンダーとして表示する年月(デフォルトはアクセス時の年月)
        this.dateObjOfDisplayedCalendar = new Date();

        // 月移動ボタンの有効/無効状態
        this.visiblePrevButton = true;
        this.visibleNextButton = true;

        // 登録されているスケジュール一覧を表示する日付(デフォルトはアクセス時の年月日)
        this.selectedDateObj = new Date();

        // 表示するカレンダー情報(デフォルトはアクセス時の年月)
        this.calendarData = (new Calendar(this.dateObjOfDisplayedCalendar.getFullYear(), this.dateObjOfDisplayedCalendar.getMonth())).create();

        // 表示されているカレンダーが持つスケジュール一覧
        this.scheduleList = [];
        this.readSchedule();
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

            _this.dateObjOfDisplayedCalendar.setMonth(_this.dateObjOfDisplayedCalendar.getMonth() - 1);
            _this.calendarData = (new Calendar(_this.dateObjOfDisplayedCalendar.getFullYear(), _this.dateObjOfDisplayedCalendar.getMonth())).create();
            _this.visiblePrevButton = _this.canPrevMonth();
            _this.visibleNextButton = _this.canNextMonth();
            _this.selectedDateObj = new Date(_this.dateObjOfDisplayedCalendar.getFullYear(), _this.dateObjOfDisplayedCalendar.getMonth(), 1);
            _this.readSchedule();

            _this.deferred.promise().then(() => {
                _this.deferred = $.Deferred();
                _this.updateView();
                _this.eventEmitter.emit(EVENT.DATE_CHANGE, _this.selectedDateObj, _this.scheduleList);
            });
        });

        this.$rootEl.on("click", "#calendar-change-next", function (evt) {

            _this.dateObjOfDisplayedCalendar.setMonth(_this.dateObjOfDisplayedCalendar.getMonth() + 1);
            _this.calendarData = (new Calendar(_this.dateObjOfDisplayedCalendar.getFullYear(), _this.dateObjOfDisplayedCalendar.getMonth())).create();
            _this.visiblePrevButton = _this.canPrevMonth();
            _this.visibleNextButton = _this.canNextMonth();
            _this.selectedDateObj = new Date(_this.dateObjOfDisplayedCalendar.getFullYear(), _this.dateObjOfDisplayedCalendar.getMonth(), 1);
            _this.readSchedule();

            _this.deferred.promise().then(() => {
                _this.deferred = $.Deferred();
                _this.updateView();
                _this.eventEmitter.emit(EVENT.DATE_CHANGE, _this.selectedDateObj, _this.scheduleList);
            });
        });

        // カレンダー内の日付部分を押下した時
        this.$rootEl.on("click", "td:not(.calendar-empty)", function (evt) {

            _this.selectedDateObj.setDate($(this).attr("data-fulldate").split("-")[2]);
            _this.eventEmitter.emit(EVENT.DATE_CHANGE, _this.selectedDateObj, _this.scheduleList);

            _this.updateView();
        });

        // スケジュールリストコンポーネント側でスケジュールに変更があった場合
        this.eventEmitter.on(EVENT.SCHEDULE_CHANGE, (scheduleList) => {

            _this.scheduleList = scheduleList;
            _this.updateView();
        });
    }

    // viewの更新
    updateView() {

        // カレンダーデータの表示
        this.showCalendar();

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

        // スケジュールの挿入
        this.insertSchedulesIntoCalendar();

        // 選択状態にある日付部分をハイライト
        this.highlightSelectedDate();
    }

    showCalendar() {

        this.$calendarTable.find("tr").not(":first").remove();
        this.$calendarInfoYear.text(this.dateObjOfDisplayedCalendar.getFullYear());
        this.$calendarInfoMonth.text(this.dateObjOfDisplayedCalendar.getMonth()+1);

        let $row = $("<tr>");

        for (let i = 0; i <= 41; i++) {

            let $tableCell = $("<td>", {
                "class": `calendar-element ${STYLE[this.calendarData[i].dateType]}`,
                "data-fulldate": this.calendarData[i].fulldate,
                "data-day": this.calendarData[i].day,
            });

            let $calendarDayInfo = $("<div>", {
                "class": "calendar-day-info"
            });
            $calendarDayInfo.append($("<div>", {
                "class": "calendar-day"
            }).text(this.calendarData[i].date));
            $calendarDayInfo.append($("<div>", {
                "class": "calendar-holiday-name"
            }).text(this.calendarData[i].holidayName));

            let $calendarSchedule = $("<div>", {
                "class": "calendar-schedule"
            });
            $calendarSchedule.append($("<ul>"));

            $tableCell.append($calendarDayInfo);
            $tableCell.append($calendarSchedule);

            $row.append($tableCell);

            if (i % 7 === 6) {
                this.$calendarTable.find("tbody").append($row);
                $row = $("<tr>");
            }
        }
    }

    canNextMonth() {
        if ((this.dateObjOfDisplayedCalendar.getFullYear() === new Date().getFullYear() + CALENDAR_LIMIT) && (this.dateObjOfDisplayedCalendar.getMonth() === 11)) {
            return false;
        } 
        return true;
    }

    canPrevMonth() {
        if ((this.dateObjOfDisplayedCalendar.getFullYear() === new Date().getFullYear() - CALENDAR_LIMIT) && (this.dateObjOfDisplayedCalendar.getMonth() === 0)) {
            return false;
        } 
        return true;
    }

    readSchedule() {

        let _this = this;

        // 選択状態の(表示している)年月のスケジュール一覧を取得
        $.ajax({
            url: "./readSchedule.php",
            data: {
                year: _this.dateObjOfDisplayedCalendar.getFullYear(),
                month: _this.dateObjOfDisplayedCalendar.getMonth()
            },
            type: "GET",
            dataType: "json"
        }).done(function (response) {

            if (response.status === "ng") {
                alert(reponse.message);
                return;
            }

            _this.scheduleList = response.schedules;
            _this.deferred.resolve();

        }).fail(function (response) {
            // 通信失敗時のコールバック処理
            window.location.href = "/500.html";
        }).always(function (response) {
            // 常に実行する処理
        });
        return this.deferred.promise();
    }

    insertSchedulesIntoCalendar() {

        // 件数が3件以上の場合は2件appendした後に+XX件という表記を入れる
        for (let keyDate in this.scheduleList) {
            let count = 0;
            for (let schedule of this.scheduleList[keyDate]) {
                let $li = $("<li>").text(escapeHTML(schedule.title)).attr("data-schedule-id", schedule.id);
                $("td[data-fulldate='" + schedule.date + "'] ul").append($li);
                count++;
                if (count == 2 && this.scheduleList[keyDate].length >= 3) {
                    $li = $("<li>").text(`+${this.scheduleList[keyDate].length-2}件`);
                    $("td[data-fulldate='" + schedule.date + "'] ul").append($li);
                    break;
                }
            }
        }
    }

    highlightSelectedDate() {

        $(".calendar-selected-date").removeClass("calendar-selected-date");

        let formatedDateString = this.selectedDateObj.getFullYear() + "-" + this.selectedDateObj.getMonth() + "-" + this.selectedDateObj.getDate();
        $("td[data-fulldate='" + formatedDateString + "']").addClass("calendar-selected-date");
    }
}