$(document).ready(function () {

    let date_selected;

    // カレンダー内の要素押下時
    $(document).on("click", "#calendar-area td", function(){
        console.log("click calendar element.");

        date_selected = {
            date: $(this).attr("data-date"),
            day: $(this).attr("data-day")
        }
        // フォーム表示(本来はモーダルウィンドウなど)
        $("#form-area").show();

    });

    $('#form-schedule').submit(function (e) {
        
        let title = $("#text-title").val();
        let date = date_selected.date;
        let day = date_selected.day;

        e.preventDefault();
        $.ajax({
            url: "./createSchedule.php",
            data: {
                user_id: 1,
                title: title,
                date: date,
                day: day
            },
            type: "POST",
        })
            .then(
                // 成功時
                function (response) {
                    let $obj = $("<div>").text(title).attr("data-id", response);
                    $("td[data-date='" + date + "']").append($obj);
                },
                // 失敗時
                function (response) {

                }
        );

    })

    $("#form-area").hide();
});