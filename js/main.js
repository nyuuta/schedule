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

    // カレンダーの作成と表示
    let component = new CalendarComponent($("#calendar-area"));

    // 予定情報を追加
    // getSchedules();
});