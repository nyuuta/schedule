class PreRegisterComponent extends Component {

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

        // 仮登録ボタンの有効/無効フラグ
        this.submitButtonDisabled = this.checkMailFormIsEmpty();

        // メッセージ欄の有効/無効フラグ
        this.messageAreaDisabled = false;

        this.render();
    }

    initEventHandler() {

        let _this = this;

        /**
         * メールアドレス入力欄に変更があった場合
         * 内容が空の場合は仮登録ボタンを無効化する
         */
        this.$rootEl.on("input", "input#mail", (evt) => {

            _this.submitButtonDisabled = _this.checkMailFormIsEmpty();
            _this.render();
        });

        /**
         * 仮登録ボタンが推された場合
         * 二重送信対策として仮登録ボタンを無効化する
         * 読み込み状態のアニメーション表示
         */
        this.$rootEl.on("submit", "#form-pre-register", (evt) => {

            _this.submitButtonDisabled = true;
            _this.messageAreaDisabled = true;
            _this.render();
        });
    }

    render() {
        // ボタンの有効化/無効化
        super.findElement("#pre-register-button").prop("disabled", this.submitButtonDisabled);

        // メッセージエリアの削除
        if (this.messageAreaDisabled === true) {
            super.findElement("#message-area").empty();
        }
    }

    checkMailFormIsEmpty() {
        let mail = super.findElement("input#mail").val();
        return (mail === "");
    }
}