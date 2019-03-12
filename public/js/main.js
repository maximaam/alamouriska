$(document).ready(function() {

    //Thumbs up
    $('.js_almrsk-thumbs-up').on('click', function(e) {
        e.preventDefault();

        let $this = $(this);

        $.get('/async/thumbs-up?owner=' + $this.data('owner') + '&ownerId=' + $this.data('id'), function (data) {
            alert(data.status);
        });
    });

    $('.js_ask-log-in').on('click', function(e) {
        e.preventDefault();

        $.get('/async/ask-log-in', function (data) {
            $(this).append(data);
        });
    });



});

$(document)
    .on('fos_comment_show_form', '.fos_comment_comment_reply_show_form', function (event, data) {
        alert('form');
    });