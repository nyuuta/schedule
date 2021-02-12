class ScheduleComponent extends Component {

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

    // 当コンポで管理する状態・データを初期化
    initComponent() {

        // スケジュールを表示する日付を表すDateオブジェクト
        this.selectedDateObj = new Date();

        this.schedulesOfSelectedDate = [];

        this.deleteButtonDisabled = true;

        // チェックボックスの選択や追加・更新・削除の操作を可能にするかどうか
        this.operationDisabled = false;

        // 追加・変更・削除の種類
        this.editType = EDITTYPE.ADD;

        this.componentDisabled = true;

        this.checkedScheduleIDs = [];

        // 変更や削除対象のスケジュール情報
        this.selectedSchedule = [];

        // 進捗度を表す値
        this.progress = [0, 30, 60, 100];

        this.render();
    }

    initEventHandler() {

        let _this = this;

        /**
         * 進捗度切り替えボタンが押された場合
         * 進捗度を変更し描画
         */
        this.$rootEl.on("click", "[data-switch-button]", (evt) => {
    
            evt.stopPropagation();
            evt.preventDefault();

            let targetScheduleID = $(evt.currentTarget).parent().attr("data-schedule-id");
            let newProgress = 0;
            let targetIndex = 0;
            $.each(_this.schedulesOfSelectedDate, function (index, schedule) {
                if (schedule.id == targetScheduleID) {
                    let currentProg = _this.schedulesOfSelectedDate[index].progress;
                    switch (currentProg) {
                        case 0:
                            newProgress = 30;
                            break;
                        case 30:
                            newProgress = 60;
                            break;
                        case 60:
                            newProgress = 100;
                            break;
                        case 100:
                            newProgress = 0;
                            break;
                    }

                    targetIndex = index;
                }
            });

            $.ajax({
                url: "/ajax/updateScheduleProgress",
                data: {
                    id: targetScheduleID,
                    progress: newProgress,
                },
                type: "POST",
                dataType: "json",
            }).done((response) => {
    
                if (response.status === "ng") {
                    alert(reponse.message);
                    return;
                }

                _this.schedulesOfSelectedDate[targetIndex].progress = newProgress;
                _this.render();
            }).fail(function (response) {
                // 通信失敗時のコールバック処理
                window.location.href = "/main";
            }).always(function (response) {
                // 常に実行する処理
            });
        });

        /**
         * チェックボックスに変更があった場合
         * チェックの入ったスケジュール情報とボタンの有効無効を更新し再描画
         */
        this.$rootEl.on("change", "input[type='checkbox']", (evt) => {

            _this.checkedScheduleIDs = [];
            super.findElement("input[name='schedule']:checkbox:checked").each((index, el) => {
                let id = parseInt($(el).val());
                _this.checkedScheduleIDs.push(id);
            });

            _this.deleteButtonDisabled = (_this.checkedScheduleIDs.length === 0);
            _this.renderDeleteButton();
        });

        /**
         * 追加・編集・削除が押された場合（共通処理）
         * 操作不可に設定＆通知し、確認画面を有効にする
         */
        this.$rootEl.on("click", "[data-button-type]", (evt) => {
            evt.stopPropagation();
            evt.preventDefault();

            _this.componentDisabled = false;
            _this.operationDisabled = true;
            super.notify("operationDisabledChange", {"disable": true});
        });

        /**
         * 新規追加ボタンが押された場合
         * 画面表示とチェックボックス解除を通知
         */
        this.$rootEl.on("click", "[data-button-type='add']", (evt) => {
    
            evt.stopPropagation();
            evt.preventDefault();

            _this.editType = EDITTYPE.ADD;
            _this.render();
        });

        /**
         * 編集ボタンが押された場合
         * 画面表示とチェックボックス解除を通知
         */
        this.$rootEl.on("click", "[data-button-type='edit']", (evt) => {

            evt.stopPropagation();
            evt.preventDefault();

            let id = $(evt.currentTarget).prev().val();

            _this.selectedSchedule = _this.schedulesOfSelectedDate.filter((schedule) => {
                return (schedule.id == id);
            })[0];

            _this.editType = EDITTYPE.UPDATE;
            _this.render();
        });

        /**
         * 削除ボタンが押された場合
         * 画面表示とチェックボックス解除を通知
         */
        this.$rootEl.on("click", "[data-button-type='delete']", (evt) => {
    
            evt.stopPropagation();
            evt.preventDefault();

            _this.editType = EDITTYPE.DELETE;
            _this.render();
        });

        /**
         * 操作キャンセルボタンが押された場合
         */
        this.$rootEl.on("click", "#button-schedule-cancel", (evt) => {
    
            evt.stopPropagation();
            evt.preventDefault();

            _this.editType = EDITTYPE.ADD;
            _this.componentDisabled = true;
            _this.operationDisabled = false;

            super.notify("operationDisabledChange", {"disable": false});
            _this.render();
        });

        /**
         * 追加確定ボタンが押された場合
         */
        this.$rootEl.on("click", "#button-schedule-add-confirm", (evt) => {
    
            evt.stopPropagation();
            evt.preventDefault();

            let newName = $("input[name='content']").val();
            let dateStr = _this.selectedDateObj.getFullYear() + "-" + (_this.selectedDateObj.getMonth()+1) + "-" + _this.selectedDateObj.getDate();
            let day = _this.selectedDateObj.getDay();

            $.ajax({
                url: "/ajax/createSchedule",
                data: {
                    title: newName,
                    date: dateStr,
                    day: day
                },
                type: "POST",
                dataType: "json",
            }).done((response) => {
    
                if (response.status === "ng") {
                    alert(reponse.message);
                    return;
                }

                _this.schedulesOfSelectedDate.push({
                    id: response.data.id,
                    title: newName,
                    date: dateStr,
                    day: day,
                    progress: 0
                });

                _this.editType = EDITTYPE.ADD;
                _this.componentDisabled = true;
                _this.operationDisabled = false;
    
                super.notify("operationDisabledChange", {"disable": false});
                super.notify("scheduleChanged", { schedules: _this.schedulesOfSelectedDate });
    
            }).fail(function (response) {
                // 通信失敗時のコールバック処理
                window.location.href = "/server-error";
            }).always(function (response) {
                // 常に実行する処理
            });
        });

        // スケジュール編集確定ボタンが押下されたとき
        this.$rootEl.on("click", "#button-schedule-update-confirm", (evt) => {

            evt.stopPropagation();
            evt.preventDefault();

            let newName = $("input[name='content']").val();
            let id = _this.selectedSchedule.id;
            let dateStr = _this.selectedDateObj.getFullYear() + "-" + (_this.selectedDateObj.getMonth()+1) + "-" + _this.selectedDateObj.getDate();
            let day = _this.selectedDateObj.getDay();

            $.ajax({
                url: "/ajax/updateSchedule",
                data: {
                    id: id,
                    title: newName,
                },
                type: "POST",
                dataType: "json",
            }).done((response) => {
    
                if (response.status === "ng") {
                    alert(reponse.message);
                    return;
                }

                _this.schedulesOfSelectedDate = _this.schedulesOfSelectedDate.filter((schedule) => {
                    return (schedule.id != id);
                });

                _this.schedulesOfSelectedDate.push({
                    id: id,
                    title: newName,
                    date: dateStr,
                    day: day
                });

                _this.editType = EDITTYPE.ADD;
                _this.componentDisabled = true;
                _this.operationDisabled = false;
    
                super.notify("operationDisabledChange", {"disable": false});
                super.notify("scheduleChanged", { schedules: _this.schedulesOfSelectedDate });
    
            }).fail(function (response) {
                // 通信失敗時のコールバック処理
                window.location.href = "/server-error";
            }).always(function (response) {
                // 常に実行する処理
            });
        });

        // スケジュール削除確定ボタンが押下されたとき
        this.$rootEl.on("click", "#button-schedule-delete-confirm", (evt) => {

            evt.stopPropagation();
            evt.preventDefault();

            let ids = _this.checkedScheduleIDs;

            $.ajax({
                url: "/ajax/deleteSchedule",
                data: {
                    ids: ids,
                },
                type: "POST",
                dataType: "json",
            }).done((response) => {
    
                if (response.status === "ng") {
                    alert(reponse.message);
                    return;
                }

                _this.schedulesOfSelectedDate = _this.schedulesOfSelectedDate.filter((schedule) => {
                    return (!ids.includes(schedule.id));
                });

                _this.editType = EDITTYPE.ADD;
                _this.componentDisabled = true;
                _this.operationDisabled = false;
    
                super.notify("operationDisabledChange", {"disable": false});
                super.notify("scheduleChanged", { schedules: _this.schedulesOfSelectedDate });
    
            }).fail(function (response) {
                // 通信失敗時のコールバック処理
                window.location.href = "/server-error";
            }).always(function (response) {
                // 常に実行する処理
            });
        });
    }

    render() {
        this.renderDeleteButton();
        this.renderOperationProhibition();
        this.renderScheduleList();
        this.renderScheduleOperation();
        this.renderSelectedDate();
    }

    renderSelectedDate() {
        let dateStr = `${this.selectedDateObj.getFullYear()}年${(this.selectedDateObj.getMonth() + 1)}月${this.selectedDateObj.getDate()}日 (${DAYMAP[this.selectedDateObj.getDay()]}`;
        super.findElement("#schedule-list-fulldate").text(`${dateStr})`);
    }

    renderDeleteButton() {
        super.findElement("[data-button-type='delete']").prop("disabled", this.deleteButtonDisabled);
    }

    renderOperationProhibition() {

        if (this.operationDisabled) {
            let maskElmStr = `<div class="operation-disabled"></div>`;
            super.findElement("#schedule-list-component").append(maskElmStr);
            super.findElement("#schedule-button-container").append(maskElmStr);
        } else {
            super.findElement(".operation-disabled").remove();
        }

        super.findElement("#button-schedule-delete").prop("disabled", this.operationDisabled);
        super.findElement("#button-schedule-add").prop("disabled", this.operationDisabled);
        super.findElement("#schedule-update-button button").prop("disabled", this.operationDisabled);
        super.findElement("input[type='checkbox']").prop("disabled", this.operationDisabled);
    }

    renderScheduleList() {

        let elmStr = "";

        super.findElement("#schedule-list").empty();

        // 選択状態にある日付を取得し、対応するスケジュール一覧を表示
        if (this.schedulesOfSelectedDate.length === 0) {
            elmStr = "<p>予定が存在しません。</p>";
            super.findElement("#schedule-list").append(elmStr);
            return;
        }

        for (let schedule of this.schedulesOfSelectedDate) {
            elmStr += `
                <div class="schedule-item" data-schedule-id="${schedule.id}">
                    <input type="checkbox" name="schedule" value="${schedule.id}">${escapeHTML(schedule.title)}
                    <button data-button-type="edit">編集</button>
                    <div class="schedule-progress-${schedule.progress}">進捗${schedule.progress}%</div>
                    <button data-switch-button="">進捗切り替え</button>
                </div>
            `;
        }
        super.findElement("#schedule-list").append(elmStr);
    }


    renderScheduleOperation() {
        super.findElement("#schedule-edit-container").empty();
        if (this.componentDisabled) {
            super.findElement("#schedule-edit-container").hide();
            return;
        } else {
            super.findElement("#schedule-edit-container").show();
        }

        switch (this.editType) {
            case EDITTYPE.ADD:
                this.renderAddScheduleArea();
                break;
            case EDITTYPE.UPDATE:
                this.renderUpdateScheduleArea();
                break;
            case EDITTYPE.DELETE:
                this.renderDeleteScheduleArea();
                break;
        }
    }

    renderAddScheduleArea() {

        let formElStr = `
            <form id="form-schedule-add" method="" action="">
                <input type="text" name="content"/>
                <button id="button-schedule-add-confirm" type="button">追加</button>
                <button id="button-schedule-cancel" type="button">やめる</button>
            </form>
        `;
        super.findElement("#schedule-edit-container").append(formElStr);
    }

    renderUpdateScheduleArea() {

        let formElStr = `
            <form id="form-schedule-update" method="" action="">
                <input type="text" name="content" value="${this.selectedSchedule.title}"/>
                <button id="button-schedule-cancel" type="button">やめる</button>
                <button id="button-schedule-update-confirm" type="button">更新</button>
            </form>
        `;
        super.findElement("#schedule-edit-container").append(formElStr);
    }

    renderDeleteScheduleArea() {

        let formElStr = `
                <p>選択された${this.checkedScheduleIDs.length}個のスケジュールを本当に削除しますか？</p>
                <button id="button-schedule-delete-confirm" type="button">削除確定</button>
                <button id="button-schedule-cancel" type="button">やめる</button>
        `;
        super.findElement("#schedule-edit-container").append(formElStr);
    }

    setSelectedDateObj(dateObj) {
        this.selectedDateObj = dateObj;
    }

    setSchedulesOfSelectedDate(schedules) {
        this.schedulesOfSelectedDate = schedules;
    }

    setOperationDisabled(disabledFlag) {
        this.operationDisabled = disabledFlag;
    }
}