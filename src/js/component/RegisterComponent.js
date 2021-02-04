class RegisterComponent extends Component {

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

        // 登録ボタンの有効/無効フラグ
        this.submitButtonDisabled = this.checkPasswordFormIsEmpty();

        // メッセージ欄の有効/無効フラグ
        this.messageAreaDisabled = false;

        this.render();
    }

    initEventHandler() {

        let _this = this;

        /**
         * パスワード入力欄に変更があった場合
         * 内容が空の場合は登録ボタンを無効化する
         */
        this.$rootEl.on("input", "input#password", (evt) => {

            _this.submitButtonDisabled = _this.checkPasswordFormIsEmpty();
            _this.render();
        });

        /**
         * パスワード確認入力欄に変更があった場合
         * 内容が空の場合は登録ボタンを無効化する
         */
        this.$rootEl.on("input", "input#password-confirm", (evt) => {

            _this.submitButtonDisabled = _this.checkPasswordFormIsEmpty();
            _this.render();
        });

        /**
         * 登録ボタンが押された場合
         * 二重送信対策として登録ボタンを無効化する
         */
        this.$rootEl.on("submit", "#form-register", (evt) => {

            _this.submitButtonDisabled = true;
            _this.messageAreaDisabled = true;
            _this.render();
        });
    }

    render() {
        // ボタンの有効化/無効化
        super.findElement("#register-button").prop("disabled", this.submitButtonDisabled);

        // メッセージエリアの削除
        if (this.messageAreaDisabled === true) {
            super.findElement("#message-area").empty();
        }
    }

    checkPasswordFormIsEmpty() {
        let password = super.findElement("input#password").val();
        let passwordConfirm = super.findElement("input#password-confirm").val();

        return (password === "" || passwordConfirm === "");
    }
}