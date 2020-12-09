/**
 * 選択状態にある日(yyyy年m月d日)が持つスケジュール一覧を表示するコンポーネント
 * 
 */
class ScheduleListComponent {

    constructor($rootEl, option) {

        this.$rootEl = $rootEl;
        this.eventEmitter = option.eventEmitter;

        this.initViewModel(option.data);
        this.buildElements();
        this.attachEvents();
        this.updateView();
    }

    /**
     * viewの表示に必要なデータや状態を定義
     */
    initViewModel(data) {

        // スケジュールを表示するDateオブジェクト
        this.selectedDateObj = data.selectedDateObj;

        // スケジュール一覧
        this.scheduleList = data.scheduleList;
    }

    // コンポーネントで使用する要素の構築、子コンポーネントの作成
    buildElements() {

    }

    // イベントの割り当て
    attachEvents() {

        let _this = this;

        // カレンダーコンポーネント側の変更に合わせてスケジュール一覧を更新する
        this.eventEmitter.on(EVENT.DATE_CHANGE, (dateObj, scheduleList) => {

            _this.selectedDateObj = dateObj;
            _this.scheduleList = scheduleList;

            _this.updateView();
        });

        // スケジュールの変更(更新、削除、追加)に合わせてカレンダーコンポーネント側に通知
        // this.$rootEl.on("change", "[name='schedule']", function () {
        //     _this.eventEmitter.emit(EVENT.SCHEDULE_CHANGE, "schedule test.");
        // });

        // スケジュール追加ボタンが押下されたとき
        $("#form-schedule-add").submit(function (evt) {
        
            let title = $("#text-title").val();
            let date = _this.selectedDateObj.getFullYear()+"-"+_this.selectedDateObj.getMonth()+"-"+_this.selectedDateObj.getDate();
            let day = _this.selectedDateObj.getDay();
    
            evt.preventDefault();

            $.ajax({
                url: "./createSchedule.php",
                data: {
                    user_id: 1,
                    title: title,
                    date: date,
                    day: day
                },
                type: "POST",
                dataType: "json",
            }).done(function (response) {

                if (response.status === "ng") {
                    alert(reponse.message);
                    return;
                }

                _this.scheduleList = response.data;
                _this.eventEmitter.emit(EVENT.SCHEDULE_CHANGE, _this.scheduleList);
                _this.updateView();
            }).fail(function (response) {
                // 通信失敗時のコールバック処理
                window.location.href = "/500.html";
            }).always(function (response) {
                // 常に実行する処理
            });
        });
    }

    // viewの更新
    updateView() {

        // スケジュール一覧の表示
        this.showSchedulesOfSelectedDate();

        // テキストボックスの初期化
        this.$rootEl.find("input").val("");
    }


    showSchedulesOfSelectedDate() {

        this.$rootEl.find("#schedule-list").empty();

        // 日付の表示
        this.$rootEl.find("#schedule-list-fulldate").text(`${this.selectedDateObj.getFullYear()}年${(this.selectedDateObj.getMonth() + 1)}月${this.selectedDateObj.getDate()}日 (${DAYMAP[this.selectedDateObj.getDay()]})`);

        // 選択状態にある日付を取得し、対応するスケジュール一覧を表示
        let formatedDateString = this.selectedDateObj.getFullYear() + "-" + this.selectedDateObj.getMonth() + "-" + this.selectedDateObj.getDate();
        let schedules = this.scheduleList[formatedDateString];
        if (typeof schedules === "undefined") {
            this.$rootEl.find("#schedule-list").append($("<p>").text("予定が存在しません。"));
        } else {
            for (let schedule of schedules) {
                let $div = $("<div>");
                let checkbox = `<input id="schedule-${schedule.id}" type="checkbox" name="schedule" value="${schedule.id}">`;
                let label = `<label for="schedule-${schedule.id}">${escapeHTML(schedule.title)}</label>`;
                $div.append(checkbox);
                $div.append(label);
                this.$rootEl.find("#schedule-list").append($div);
            }
        }
    }
}