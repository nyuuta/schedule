$(document).ready(function () {

    const getSchedules = function () {

        let date_begin = $("table td[data-date]:first").attr("data-date");
        let date_end = $("table td[data-date]:last").attr("data-date");

        $.ajax({
            url: "./readSchedule.php",
            data: {
                date_begin: date_begin,
                date_end: date_end
            },
            type: "GET",
            dataType: "json"
        })
            .then(
                // 成功時
                function (response) {

                    for (let schedule of response) {
                        let $obj = $("<div>").text(schedule.title).attr("data-id", schedule.id);
                        $("td[data-date='" + schedule.date + "']").append($obj);
                    }
                },
                // 失敗時
                function (response) {

                }
        );
    }

    // アクセス時の日付データ取得
    let date_current = new Date();

    // カレンダーの作成と表示
    let calendar_object = new Calendar();
    calendar_object.init(date_current.getFullYear(), date_current.getMonth());
    calendar_object.show();

    // 予定情報を追加
    getSchedules();

    $("#calendar-change-next").on("click", function () {
        
        console.log("click next button.");

        calendar_object.next();

        let calendar_year = calendar_object.getCurrentYear();
        let calendar_month = calendar_object.getCurrentMonth();

        if ((calendar_year === date_current.getFullYear()+100) && (calendar_month === 11)) {
            $(this).addClass("fa-disabled");
            $(this).prop("disabled", true);
        } else {
            $("#calendar-change-prev").removeClass("fa-disabled");
            $("#calendar-change-prev").prop("disabled", false);

        }
    });


    $("#calendar-change-prev").on("click", function () {
        
        console.log("click prev button.");

        calendar_object.prev();

        let calendar_year = calendar_object.getCurrentYear();
        let calendar_month = calendar_object.getCurrentMonth();

        if ((calendar_year === date_current.getFullYear()-100) && (calendar_month === 0)) {
            $(this).addClass("fa-disabled");
            $(this).prop("disabled", true);
        } else {
            $("#calendar-change-next").removeClass("fa-disabled");
            $("#calendar-change-next").prop("disabled", false);
        }
    });
});