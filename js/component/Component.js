class Component {

    /**
     * $rootEl: コンポーネントが管理するviewのルート要素を表す jQueryオブジェクト
     * subComponents: 子コンポーネントクラスのインスタンス配列
     * observers: コンポーネントを監視しているコンポーネント配列
     */
    constructor($rootEl) {
        this.$rootEl = $rootEl;
        this.subComponents = {};
        this.observers = [];
    }

    findElement(selector) {
        return this.$rootEl.find(selector);
    }

    registerSubComponent(name, component) {
        this.subComponents[name] = component;
    }

    registerObserver(name, receiver, callbackFunction) {
        this.observers.push({name: name, receiver: receiver, callback: callbackFunction});
    }

    notify(name, notificationObject) {

        for (let observer of this.observers) {
            if (observer.name == name) {
                observer.callback.call(observer.receiver, notificationObject);
            }
        }
    }

    // 子コンポーネントの初期化
    initSubComponent() {

    }

    // コンポーネントの初期化
    initComponent() {

    }

    // コンポーネント内で発生するイベント用ハンドラの初期化
    initEventHandler() {

    }
}