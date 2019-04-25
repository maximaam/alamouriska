$(document).ready(function() {

    //Thumbs up
    $('.js_almrsk-liking').on('click', function(e) {
        e.preventDefault();

        let $this = $(this);

        $.get('/async/liking?owner=' + $this.data('owner') + '&ownerId=' + $this.data('id'), function (data) {
            if (+data.status === 1) {
                let $sumLikings = $this.find('.likings-sum'),
                    $labelLikings = $this.find('.likings-label'),
                    $containerLikings = $this.find('.likings-container'),
                    sumLikings = +$sumLikings.text();

                $sumLikings.text(+data.action === 1 ? (sumLikings + 1) : (sumLikings - 1));
                $containerLikings.toggleClass('text-danger', +data.action === 1);
                $labelLikings.attr('title', data.actionLabel);
            }
        });
    });

    $('.js_ask-log-in').on('click', function(e) {
        e.preventDefault();

        $.get('/async/ask-log-in', function (data) {
            $(this).append(data);
        });
    });

    $('#member-contact').submit(function(event){
        event.preventDefault(); //prevent default action
        let $form = $(this),
            url = '/async/member-contact',
            formData = $form.serialize(); //Encode form elements for submission

        $form.find('button').text('Envoie en cours...').attr('disabled', true);

        $.post(url, formData, function(response) {
            $form.html( response );
        });
    });
});

$(document)
    .on('fos_comment_show_form', '.fos_comment_comment_reply_show_form', function (event, data) {
        alert('form');
    });