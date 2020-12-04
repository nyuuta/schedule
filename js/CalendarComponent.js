class CalendarComponent {

    // 一度、このコンポーネントに全部入れる　※viewの状態管理や更新を一か所で、というのは守る
    constructor($rootEl) {

        this.DATE_TYPE = {
            "EMPTY": 0, "WEEKDAY": 1, "SATURDAY": 2, "SUNDAY": 3, "HOLIDAY": 4
        }

        this.DAY_MAP = {
            0: "日", 1: "月", 2: "火", 3: "水", 4: "木", 5: "金", 6: "土" 
        }

        this.STYLE = {
            0: "calendar-empty", 1: "calendar-day",
            2: "calendar-saturday", 3: "calendar-sunday",
            4: "calendar-holiday"
        }

        this.CALENDAR_LIMIT = 50;

        this.$rootEl = $rootEl;
        this.schedules = [];

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

        // スケジュール一覧を表示する日付(デフォルトは当日)
        // this.selectedDate = new Date(2020, 11, 15);
        this.selectedDate = new Date();
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

        // スケジュール一覧
        this.$scheduleListArea = $("#schedule-list-area");
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

        // カレンダー内の日付部分を押下した時
        this.$rootEl.on("click", "td", function (evt) {
            _this.selectedDate.setDate($(this).attr("data-fulldate").split("-")[2]);
            _this.updateView();
        })
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

        // スケジュールの挿入
        this.insertSchedulesIntoCalendar();
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

    insertSchedulesIntoCalendar() {

        let _this = this;

        // 選択状態の(表示している)年月のスケジュール一覧を取得
        $.ajax({
            url: "./readSchedule.php",
            data: {
                year: this.selectedFulldate.getFullYear(),
                month: this.selectedFulldate.getMonth()
            },
            type: "GET",
            dataType: "json"
        }).done(function (response) {
            if (response.status === "ng") {
                alert(reponse.message);
                return;
            }

            _this.schedules = response.schedules;

            //各fulldate毎にscheduleを格納
            // 件数が3件以上の場合は2件appendした後に+XX件という表記を入れる

            let dateSchedules = {};
            for (let schedule of _this.schedules) {
                if (!dateSchedules.hasOwnProperty(schedule.date)) {
                    dateSchedules[schedule.date] = [];
                }
                dateSchedules[schedule.date].push(schedule);
            }

            _this.trueSchedules = dateSchedules;

            for (let dateSchedule in dateSchedules) {
                let count = 0;
                for (let schedule of dateSchedules[dateSchedule]) {
                    let $li = $("<li>").text(_this.escapeHTML(schedule.title)).attr("data-schedule-id", schedule.id);
                    $("td[data-fulldate='" + schedule.date + "'] ul").append($li);
                    count++;
                    if (count == 2 && dateSchedules[dateSchedule].length >= 3) {
                        $li = $("<li>").text(`+${dateSchedules[dateSchedule].length-2}件`);
                        $("td[data-fulldate='" + schedule.date + "'] ul").append($li);
                        break;
                    }
                }
            }

            _this.showSchedulesOfSelectedDate();
            _this.highlightSelectedDate();
        }).fail(function (response) {
            // 通信失敗時のコールバック処理
            window.location.href = "/500.html";
        }).always(function (response) {
            // 常に実行する処理
        });
    }

    showSchedulesOfSelectedDate() {

        $("#schedule-list").empty();

        // 日付の表示
        $("#schedule-list-fulldate").text(`${this.selectedDate.getFullYear()}年${(this.selectedDate.getMonth() + 1)}月${this.selectedDate.getDate()}日 (${this.DAY_MAP[this.selectedDate.getDay()]})`);

        // 選択状態にある日付を取得し、対応するスケジュール一覧を表示
        let formatedDateString = this.selectedDate.getFullYear() + "-" + this.selectedDate.getMonth() + "-" + this.selectedDate.getDate();
        let schedules = this.trueSchedules[formatedDateString];
        if (typeof schedules === "undefined") {
            $("#schedule-list").append($("<p>").text("予定が存在しません。"));
        } else {
            for (let schedule of schedules) {
                let $div = $("<div>");
                let checkbox = `<input id="schedule-${schedule.id}" type="checkbox" name="schedule" value="${schedule.id}">`;
                let label = `<label for="schedule-${schedule.id}">${this.escapeHTML(schedule.title)}</label>`;
                $div.append(checkbox);
                $div.append(label);
                $("#schedule-list").append($div);
            }
        }
    }

    highlightSelectedDate() {

        $(".calendar-selected-date").removeClass("calendar-selected-date");

        let formatedDateString = this.selectedDate.getFullYear() + "-" + this.selectedDate.getMonth() + "-" + this.selectedDate.getDate();
        $("td[data-fulldate='" + formatedDateString + "']").addClass("calendar-selected-date");
    }

    escapeHTML(string){
        return string.replace(/\&/g, '&amp;')
          .replace(/\</g, '&lt;')
          .replace(/\>/g, '&gt;')
          .replace(/\"/g, '&quot;')
          .replace(/\'/g, '&#x27');
      }
}