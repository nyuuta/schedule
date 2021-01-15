function escapeHTML(val) {
    return $("<div />").text(val).html();
}