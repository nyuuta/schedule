$(document).ready(function () {

    let date_selected;

    // カレンダー内の要素押下時
    $(document).on("click", "#calendar-area td", function(){
        console.log("click calendar element.");

        date_selected = {
            year: $(this).attr("data-year"),
            month: $(this).attr("data-month"),
            date: $(this).text(),
            day: $(this).attr("data-day")
        }
        // フォーム表示(本来はモーダルウィンドウなど)
        $("#form-area").show();

    });

    $('#form-schedule').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "./createSchedule.php",
            data: {
                user_id: 1,
                title: $("#text-title").val(),
                year: date_selected.year,
                month: date_selected.month,
                date: date_selected.date,
                day: date_selected.day,
            },
            type: "POST",
        })
            .then(
                // 成功時
                function (response) {
                    console.log(response);
                },
                // 失敗時
                function (response) {
                }
        );

    })

    $("#form-area").hide();
});