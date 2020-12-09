class MainContainerComponent {

    /**
     * 画面全体を管理するコンポーネント
     * 
     */
    constructor($rootEl) {

        this.$rootEl = $rootEl;

        // コンポーネント間の協調を実現するためのイベント発行用オブジェクト
        this.containerEventEmitter = new EventEmitter();
        this.calendarEventEmitter = new EventEmitter();
        this.scheduleListEventEmitter = new EventEmitter();

        this.attachEvents();

        this.containerEventEmitter.emit(EVENT.START);
    }

    attachEvents() {

        let _this = this;

        // カレンダーコンポーネントの初期化
        this.containerEventEmitter.on(EVENT.START, () => {

            let option = {
                "eventEmitter": _this.calendarEventEmitter,
            }
            _this.calendarComponent = new CalendarComponent($("#calendar-area"), option);
        });

        // カレンダーコンポーネントの初期化が完了した後、スケジュールリストコンポーネントを初期化
        this.calendarEventEmitter.on(EVENT.DONE_INIT, (dateObj, scheduleList) => {
            let option = {
                "eventEmitter": _this.scheduleListEventEmitter,
                "data": {
                    "selectedDateObj": dateObj,
                    "scheduleList": scheduleList,
                },
            }
            _this.scheduleListComponent = new ScheduleListComponent($("#schedule-list-area"), option);
        });

        // カレンダーコンポーネント側で日付が変更された場合はスケジュールリストコンポーネント側に通知
        this.calendarEventEmitter.on(EVENT.DATE_CHANGE, (dateObj, scheduleList) => {
            _this.scheduleListEventEmitter.emit(EVENT.DATE_CHANGE, dateObj, scheduleList);
        });

        // スケジュールリストコンポーネント側でスケジュールが変更された場合はカレンダーコンポーネント側に通知
        this.scheduleListEventEmitter.on(EVENT.SCHEDULE_CHANGE, (scheduleList) => {

            _this.calendarEventEmitter.emit(EVENT.SCHEDULE_CHANGE, scheduleList);
        });
    }
}