$(document).ready(function () {

    let date_selected;
    let id_selected;

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

    $("#form-area").hide();
    $("#delete-area").hide();
    $("#update-area").hide();
});