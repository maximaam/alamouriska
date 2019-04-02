$(document).ready(function() {

    //Thumbs up
    $('.js_almrsk-liking').on('click', function(e) {
        e.preventDefault();

        let $this = $(this);

        $.get('/async/liking?owner=' + $this.data('owner') + '&ownerId=' + $this.data('id'), function (data) {
            if (+data.status === 1) {
                let $sumLikings = $('.likings-sum'),
                    $labelLikings = $('.likings-label'),
                    sumLikings = +$sumLikings.text();

                $sumLikings.text(+data.action === 1 ? (sumLikings + 1) : (sumLikings - 1));
                $labelLikings.text(data.actionLabel);
            }
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