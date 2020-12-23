class MainContainer extends Component {

    //$rootEl: このコンポーネントが担当するルート要素を表すjQueryオブジェクト
    constructor($rootEl) {

        super();

        this.$rootEl = $rootEl;
        this.subComponents = {};
        this.observers = [];

        this.initSubComponent();
        this.initComponent();
    }

    // 子コンポーネントを定義
    initSubComponent() {

        // カレンダー表示部分
        let calendarComponent = new CalendarComponent(super.findElement("#calendar-component"));
        super.registerSubComponent("calendarComponent", calendarComponent);

        calendarComponent.registerObserver("dateChanged", this, this.dateChangeEventHandler);
        calendarComponent.registerObserver("calendarChanged", this, this.calendarChangeEventHandler);

        // スケジュール一覧表示部分
        let scheduleComponent = new ScheduleComponent(this.findElement("#schedule-list-area"));
        super.registerSubComponent("scheduleComponent", scheduleComponent);

        scheduleComponent.registerObserver("scheduleChangeed", this, this.scheduleChangeEventHandler);
        scheduleComponent.registerObserver("operationDisabledChange", this, this.operationDisabledEventHandler);
    }

    // コンポーネントの初期化
    initComponent() {

        // 登録されているスケジュール一覧を表示する日付(デフォルトはアクセス時の年月日)
        this.selectedDateObj = new Date();

        // 表示されているカレンダーが持つスケジュール一覧
        this.schedules = [];
    }

    /**
     * カレンダーコンポーネントで日付が変更された場合
     */
    dateChangeEventHandler(notification) {

        // 新しいDateオブジェクトを生成し、そのデータをセットするよう通知
        this.selectedDateObj = notification.newDateObj;
        let schedulesOfSelectedDate = this.getSchedulesOfSelectedDate();

        this.subComponents.calendarComponent.setSelectedDateObj(this.selectedDateObj);
        this.subComponents.calendarComponent.render();

        this.subComponents.scheduleComponent.setSelectedDateObj(this.selectedDateObj);
        this.subComponents.scheduleComponent.setSchedulesOfSelectedDate(schedulesOfSelectedDate);
        this.subComponents.scheduleComponent.render();
    }

    /**
     * カレンダーが更新された場合は選択状態の日付とスケジュールを通知
     */
    calendarChangeEventHandler(notification) {
        this.selectedDateObj = notification.newDateObj;
        this.schedules = notification.schedules;

        let schedulesOfSelectedDate = this.getSchedulesOfSelectedDate();

        this.subComponents.calendarComponent.setSelectedDateObj(this.selectedDateObj);
        this.subComponents.calendarComponent.setSchedules(this.schedules);
        this.subComponents.calendarComponent.render();

        this.subComponents.scheduleComponent.setSelectedDateObj(this.selectedDateObj);
        this.subComponents.scheduleComponent.setSchedulesOfSelectedDate(schedulesOfSelectedDate);
        this.subComponents.scheduleComponent.render();
    }

    /**
     * スケジュールコンポーネントでスケジュールが更新された場合
     */
    scheduleChangeEventHandler(notification) {

        let dateStr = this.selectedDateObj.getFullYear()+"-"+this.selectedDateObj.getMonth()+"-"+this.selectedDateObj.getDate();
        this.schedules[dateStr] = notification.schedules;

        this.subComponents.calendarComponent.setSchedules(this.schedules);
        this.subComponents.calendarComponent.render();

        this.subComponents.scheduleComponent.setSchedulesOfSelectedDate(this.getSchedulesOfSelectedDate());
        this.subComponents.scheduleComponent.render();
    }

    /**
     * 画面操作にロックを掛けたい依頼が来た場合
     */
    operationDisabledEventHandler(notification) {
        this.subComponents.calendarComponent.setOperationDisabled(notification.disable);
        this.subComponents.calendarComponent.render();

        this.subComponents.scheduleComponent.setOperationDisabled(notification.disable);
        this.subComponents.scheduleComponent.render();
    }

    getSchedulesOfSelectedDate() {
        let schedulesOfSelectedDate = [];
        let formatedDateString = this.selectedDateObj.getFullYear() + "-" + this.selectedDateObj.getMonth() + "-" + this.selectedDateObj.getDate();

        if (Object.keys(this.schedules).includes(formatedDateString)) {
            schedulesOfSelectedDate = this.schedules[formatedDateString];
        }
        return schedulesOfSelectedDate;
    }
}