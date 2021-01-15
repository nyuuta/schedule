class CalendarComponent extends Component {

    //$rootEl: このコンポーネントが担当するルート要素を表す jQueryオブジェクト
    constructor($rootEl) {

        super();

        this.$rootEl = $rootEl;
        this.subComponents = {};
        this.observers = [];

        this.initSubComponent();
        this.initComponent();
        this.initEventHandler();
    }

    initSubComponent() {

    }

    // コンポーネントで管理する状態・データを初期化しレンダリング
    initComponent() {
    
        // カレンダーとして表示する年月日(デフォルトはアクセスした日付)
        this.dateObjOfDisplayedCalendar = new Date();

        // 表示するカレンダー情報(デフォルトはアクセスした日付)
        this.calendarData = (new Calendar(this.dateObjOfDisplayedCalendar.getFullYear(), this.dateObjOfDisplayedCalendar.getMonth())).create();

        // カレンダー内で選択されている日付を表すDateオブジェクト(デフォルトはアクセスした日付)
        this.selectedDateObj = new Date();

        // 月移動ボタンの有効/無効状態(デフォルトは有効)
        this.prevButtonDisabled = false;
        this.nextButtonDisabled = false;

        // カレンダーに対する操作の有効/無効状態(デフォルトは有効)
        this.operationDisabled = false;

        this.deferred = new $.Deferred();

        this.getSchedulesFromServer();

        this.deferred.promise().then(() => {
            
            super.notify("calendarChanged", {newDateObj: this.selectedDateObj, schedules: this.schedules});
        });
    }

    // コンポーネントに割り当てられているイベントの初期化
    initEventHandler() {

        let _this = this;

        // 月移動ボタン押下：
        super.findElement("[id^='calendar-change']").on("click", (evt) => {
            let changeValue = ($(evt.currentTarget).attr("id") == "calendar-change-next") ? +1 : -1;
            _this.dateObjOfDisplayedCalendar.setMonth(_this.dateObjOfDisplayedCalendar.getMonth()+changeValue);
            _this.calendarData = (new Calendar(_this.dateObjOfDisplayedCalendar.getFullYear(), _this.dateObjOfDisplayedCalendar.getMonth())).create();
            _this.checkDateInRange();

            _this.selectedDateObj.setMonth( _this.selectedDateObj.getMonth()+changeValue);
            _this.selectedDateObj.setDate(1);
            
            this.deferred = new $.Deferred();

            this.getSchedulesFromServer();
            this.deferred.promise().then(() => {
                super.notify("calendarChanged", {newDateObj: _this.selectedDateObj, schedules: _this.schedules});
            });
        });

        // カレンダー内の日付押下： Dateオブジェクトの更新・通知
        this.$rootEl.on("click", "td:not(.calendar-empty)", (evt) => {

            let newDate = $(evt.currentTarget).attr("data-fulldate").split("-")[2];
            _this.selectedDateObj.setDate(newDate);
            super.notify("dateChanged", {newDateObj: _this.selectedDateObj});
        });
    }

    /**
     * 状態・データを用いてviewを描画
     */
    render() {

        if (this.operationDisabled) {
            let maskElmStr = `<div class="operation-disabled"></div>`;
            this.$rootEl.append(maskElmStr);
        } else {
            super.findElement(".operation-disabled").remove();
        }

        super.findElement("#calendar-change-prev").prop("disabled", this.prevButtonDisabled);
        super.findElement("#calendar-change-next").prop("disabled", this.nextButtonDisabled);

        this.renderCalendar();

        this.renderSchedulesToCalendar();

        this.renderHighlightToSelectedDate();
    }

    renderCalendar() {

        super.findElement("tr").not(":first").remove();

        let elmStr = `
            <div class="date-year">
                ${this.dateObjOfDisplayedCalendar.getFullYear()}
            </div>
            <div class="date-month">
                ${this.dateObjOfDisplayedCalendar.getMonth()+1}
            </div>
        `;

        super.findElement("#calendar-date-info").empty();
        super.findElement("#calendar-date-info").append(elmStr);
        let tableRowElmStr = "";

        for (let i = 0; i <= 41; i++) {

            let dateStr = this.calendarData[i].fulldate;
            let date = this.calendarData[i].date;
            let day = this.calendarData[i].day;
            let holidayName = this.calendarData[i].holidayName;
            let style = STYLE[this.calendarData[i].dateType];

            tableRowElmStr += `
                <td class="calendar-element ${style}" data-fulldate="${dateStr}" data-day="${day}">
                    <div class="calendar-day-info">
                        <div class="calendar-day">
                            ${date}
                        </div>
                        <div class="calendar-holiday-name">
                            ${holidayName}
                        </div>
                    </div>
                    <div class="calendar-schedule">
                    </div>
                </td>
            `;
            if (i % 7 === 6) {
                super.findElement("tbody").append("<tr>"+tableRowElmStr+"</tr>");
                tableRowElmStr = "";
            }
        }
    }

    /**
     * カレンダー内にスケジュール情報を挿入
     *  表示する件数は2件までとし、3件以上の場合は+表記を追加する
     */
    renderSchedulesToCalendar() {

        let listElmStr = "";
        let count = 0;

        for (let keyDateStr in this.schedules) {
            for (let schedule of this.schedules[keyDateStr]) {

                listElmStr += `<li id="${escapeHTML(schedule.id)}">${escapeHTML(schedule.title)}</li>`;
                if (++count == 2) {
                    break;
                }
            }
            if (this.schedules[keyDateStr].length >= 3) {
                listElmStr += `<li>+${this.schedules[keyDateStr].length-2}件</li>`;
            }
            super.findElement(`td[data-fulldate="${keyDateStr}"] .calendar-schedule`).empty();
            super.findElement(`td[data-fulldate="${keyDateStr}"] .calendar-schedule`).append("<ul>"+listElmStr+"</ul>");
            count = 0;
            listElmStr = "";
        }
    }

    /**
     * 選択された日付部分をハイライトするview更新メソッド
     */
    renderHighlightToSelectedDate() {

        let dateStr = `${this.selectedDateObj.getFullYear()}-${(this.selectedDateObj.getMonth()+1)}-${this.selectedDateObj.getDate()}`;

        super.findElement(".calendar-selected-date").removeClass("calendar-selected-date");
        super.findElement("td[data-fulldate='" + dateStr + "']").addClass("calendar-selected-date");
    }

    // 親から値をセットするためのメソッド
    setSchedules(schedules) {
        this.schedules = schedules;
    }

    setSelectedDateObj(dateObj) {
        this.selectedDateObj = dateObj;
    }

    setOperationDisabled(disabledFlag) {
        this.operationDisabled = disabledFlag;
    }

    // ロジック
    getSchedulesFromServer() {

        let _this = this;

        // 選択状態の(表示している)年月のスケジュール一覧を取得
        $.ajax({
            url: "/ajax/readSchedule",
            data: {
                year: _this.selectedDateObj.getFullYear(),
                month: _this.selectedDateObj.getMonth()
            },
            type: "GET",
            dataType: "json"
        }).done((response) => {

            if (response.status === "ng") {
                alert(response.message);
                return;
            }

            _this.schedules = response.schedules;
            _this.deferred.resolve();

        }).fail(function (response) {
            // 通信失敗時のコールバック処理
            window.location.href = "/server-error";
        }).always(function (response) {
            // 常に実行する処理
        });
    }

    /**
     * カレンダーの表示限界に到達しているかどうかを判定
     */
    checkDateInRange() {

        let calendarYear = this.dateObjOfDisplayedCalendar.getFullYear();
        let calendarMonth = this.dateObjOfDisplayedCalendar.getMonth();

        let todaysYear = new Date().getFullYear();

        this.nextButtonDisabled = ((calendarYear === (todaysYear + CALENDAR_LIMIT)) && (calendarMonth === 11));
        this.prevButtonDisabled = ((calendarYear === (todaysYear - CALENDAR_LIMIT)) && (calendarMonth === 0));
    }
}