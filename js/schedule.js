$(document).ready(function () {

    let date_selected;
    let id_selected;

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

    // カレンダー内の予定要素押下時
    $(document).on("click", "[data-id]", function(e){
        console.log("click schedule element.");

        // 親要素(日付要素)へのイベント伝播を防ぐ
        e.stopPropagation();
        id_selected = $(this).attr("data-id");

        // フォーム表示(本来はモーダルウィンドウなど)
        $("#delete-area").show();
        $("#update-area").show();
    });

    $('#form-delete').submit(function (e) {

        e.preventDefault();

        $.ajax({
            url: "./deleteSchedule.php",
            data: {
                id: id_selected
            },
            type: "POST",
        })
            .then(
                // 成功時
                function (response) {
                    $("#delete-area").hide();
                    $(`[data-id='${id_selected}']`).remove();
                },
                // 失敗時
                function (response) {

                }
        );
    });

    $('#form-update').submit(function (e) {

        e.preventDefault();

        let content = $("#text-update").val();

        $.ajax({
            url: "./updateSchedule.php",
            data: {
                title: content,
                id: id_selected,
            },
            type: "POST",
        })
            .then(
                // 成功時
                function (response) {
                    $("#update-area").hide();
                    $(`[data-id='${id_selected}']`).text(content);
                },
                // 失敗時
                function (response) {

                }
        );
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
    $("#delete-area").hide();
    $("#update-area").hide();
});