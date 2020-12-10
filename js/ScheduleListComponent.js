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

        // スケジュール編集（新規追加、編集、削除）エリアの表示/非表示
        this.isVisibleEditArea = false;

        // 選択されているスケジュール情報
        this.selectedScheduleList = [];

        // スケジュール編集のタイプ
        this.editType = EDITTYPE.ADD;
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
        this.$rootEl.on("click", "#button-schedule-add", function (evt) {

            _this.isVisibleEditArea = true;
            _this.editType = EDITTYPE.ADD;

            _this.updateView();
        });
        

        // スケジュール追加ボタンが押下されたとき
        this.$rootEl.on("click", "#form-schedule-add button", function (evt) {

            let content = _this.$rootEl.find("#form-schedule-add input[name='content']").val();
            let dateStr = _this.selectedDateObj.getFullYear()+"-"+_this.selectedDateObj.getMonth()+"-"+_this.selectedDateObj.getDate();
            let day = _this.selectedDateObj.getDay();

            $.ajax({
                url: "./createSchedule.php",
                data: {
                    user_id: 1,
                    title: content,
                    date: dateStr,
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

        // スケジュール編集ボタンが押下されたとき
        this.$rootEl.on("click", "#schedule-list button", function (evt) {

            evt.preventDefault();

            _this.isVisibleEditArea = true;

            let dateStr = _this.selectedDateObj.getFullYear() + "-" + _this.selectedDateObj.getMonth() + "-" + _this.selectedDateObj.getDate();
            let id = $(this).prev().attr("for").split("-")[1];
            for (let schedule of _this.scheduleList[dateStr]) {
                if (schedule.id == id) {
                    _this.selectedScheduleList = [schedule];
                    _this.editType = EDITTYPE.UPDATE;
                    break;
                }
            }

            _this.updateView();
        });

        // スケジュールが更新された場合
        this.$rootEl.on("click", "#form-schedule-update button", function (evt) {

            evt.preventDefault();

            let newTitle = _this.$rootEl.find("#form-schedule-update input[name='content']").val();

            $.ajax({
                url: "./updateSchedule.php",
                data: {
                    id: _this.selectedScheduleList[0].id,
                    title: newTitle,
                },
                type: "POST",
                dataType: "json",
            }).done(function (response) {

                if (response.status === "ng") {
                    alert(reponse.message);
                    return;
                }

                let dateStr = _this.selectedDateObj.getFullYear() + "-" + _this.selectedDateObj.getMonth() + "-" + _this.selectedDateObj.getDate();
                for (const [key, schedule] of _this.scheduleList[dateStr].entries()) {
                    if (schedule.id == _this.selectedScheduleList[0].id) {
                        _this.scheduleList[dateStr][key].title = newTitle;
                        break;
                    }
                }

                _this.eventEmitter.emit(EVENT.SCHEDULE_CHANGE, _this.scheduleList);
                _this.isVisibleEditArea = false;
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

        let _this = this;

        // スケジュール一覧の表示
        this.showSchedulesOfSelectedDate();

        // テキストボックスの初期化
        this.$rootEl.find("input").val("");

        // 編集エリアの表示/非表示
        this.$rootEl.find("#schedule-edit-container").empty();
        if (this.isVisibleEditArea) {

            switch (_this.editType) {
                case EDITTYPE.ADD:
                    this.showAddScheduleArea();
                    break;
                case EDITTYPE.UPDATE:
                    this.showUpdateScheduleArea();
                    break;
            } 

            this.$rootEl.find("#schedule-edit-container").show();
        } else {
            this.$rootEl.find("#schedule-edit-container").hide();
        }
    }

    showAddScheduleArea() {

        let formElStr = `
            <form id="form-schedule-add" method="" action="">
                <input type="text" name="content"/>
                <button type="button">追加</button>
            </form>
        `;
        this.$rootEl.find("#schedule-edit-container").append(formElStr);
    }

    showUpdateScheduleArea() {

        let formElStr = `
            <form id="form-schedule-update" method="" action="">
                <input type="text" name="content" value="${this.selectedScheduleList[0].title}"/>
                <button type="button">更新</button>
            </form>
        `;
        this.$rootEl.find("#schedule-edit-container").append(formElStr);
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
                let button = "<button>編集</button>";
                $div.append(checkbox);
                $div.append(label);
                $div.append(button);
                this.$rootEl.find("#schedule-list").append($div);
            }
        }
    }
}