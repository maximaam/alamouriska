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

    //Remove a comment: post comments or journal
    $(document).on('click', '.js_comment-remove', function(e) {
        e.preventDefault();

        if (!confirm('Tu confirmes la suppression?')) {
            return false;
        }

        let $this = $(this);

        $this
            .removeClass('js_comment-remove')
            .html('<i class="fa fa-spinner fa-pulse"></i>');

        $.get('/async/'+$this.data('type')+'-remove?uid=' + $this.data('uid'), function (response) {
            if (+response.status === 1) {
                $this.parents('li').fadeOut();
            }
        });
    });

    //Edit a comment
    $(document).on('click', '.js_comment-form-edit', function(e) {
        e.preventDefault();

        let $this = $(this),
            uid = +$this.data('uid');

        let $target = $('.form-comment-edit-' + uid);

        if ($target.children().length === 0) {
            let comment = $('.comment-item-' + uid).text();
            let $form = $('<form>', {method: 'post', action: '/async/comment-edit/'+uid, class: 'comment-form-edit'})
                .append($('<textarea/>', {name: 'comment_edit', id: 'comment-edit-'+uid, class: 'w-100', required: 'required', minLength: 25, maxLength: 2000, rows: 5}).val(comment))
                .append($('<button/>', {class: 'btn btn-happy d-block', type: 'submit'}).html('OK'))
            ;

            $target.append($form);
        } else {
            $target.html('');
        }
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

    $('input:file').on('change', function() {
        let $target = $(this);
        let $parent = $target.parents('fieldset');
        $parent.find('img').remove();

        let imgSrc = (window.URL || window.webkitURL).createObjectURL($target[0].files[0]);
        $parent.append($('<img src="' + imgSrc + '" alt="Image" class="mw-100 mt-2">'));
    });

    if ($('.almrsk-post').length) {
        $('#word_inTamazight_help').append('<a href="https://www.lexilogos.com/clavier/tamazight.htm" target="_blank" class="ml-2">Clavier Tamazight <i class="fa fa-external-link"></i></a>');
        $('#word_inArabic_help').append('<a href="https://www.lexilogos.com/clavier/araby.htm" target="_blank" class="ml-2">Clavier Arabe <i class="fa fa-external-link"></i></a>');

        $('#expression_post_help').append('<a href="https://www.lexilogos.com/clavier/tamazight.htm" target="_blank" class="ml-2" title="Clavier Tamazight">Tamazight</a> | <a href="https://www.lexilogos.com/clavier/araby.htm" target="_blank" title="Clavier Arabe">Arabe <i class="fa fa-external-link"></i></a>');

        $('#word_question').parents('.form-group').addClass('almrsk-form-question');
    }

    /* 1. Visualizing things on Hover - See next part for action on click */
    $('#stars li').on('mouseover', function(){
        let onStar = parseInt($(this).data('value'), 10); // The star currently mouse on

        // Now highlight all the stars that's not after the current hovered star
        $(this).parent().children('li.star').each(function(e){
            if (e < onStar) {
                $(this).addClass('hover');
            }
            else {
                $(this).removeClass('hover');
            }
        });

    }).on('mouseout', function(){
        $(this).parent().children('li.star').each(function(e){
            $(this).removeClass('hover');
        });
    });


    /* 2. Action to perform on click */
    $('#stars li').on('click', function(){
        let $thisStar = $(this),
            thisStarVal = parseInt($thisStar.data('value'), 10),
            $stars = $thisStar.parent().children('li.star');

        if (!$thisStar.parent().hasClass('mb-done')) {

            for (let i = 0; i < $stars.length; i++) {
                $($stars[i]).removeClass('selected');
            }

            for (let i = 0; i < thisStarVal; i++) {
                $($stars[i]).addClass('selected');
            }

            $.get('/async/rating?rating=' + thisStarVal, function (data) {
                $('#stars li').off('click');
                if (+data.status === 1) {
                    $('.rating-feedback').html($thisStar.attr('title'));
                }
            });
        }

        // JUST RESPONSE (Not needed)

        /*
        var ratingValue = parseInt($('#stars li.selected').last().data('value'), 10);
        var msg = "";
        if (ratingValue > 1) {
            msg = "Thanks! You rated this " + ratingValue + " stars.";
        }
        else {
            msg = "We will improve ourselves. You rated this " + ratingValue + " stars.";
        }

        $('.message').fadeIn(200);
        $('.message').html("<span>" + msg + "</span>");
        */


    });

    /*
    let $jumbotron = $('.jumbotron');
    if ($jumbotron.length > 0) {
        setTimeout(function () {
            $('.jumbotron').slideUp(3000, function () {
                $jumbotron.remove();
            });
        }, 3000);
    }
     */

    $(document).on('submit', '.comment-form', function(e){
        e.preventDefault();

        let $form = $(e.target),
            $type = $('#comment_type'), //Not for journal comment
            $btn = $form.find(':submit');

        $type.val($form.data('type'));
        $btn.data('label', $btn.html());
        $btn.html('<i class="fa fa-spinner fa-pulse"></i>');
        $btn.prop('disabled', true);

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function(response) {
                response = response.replace('border-bottom', 'border-bottom bg-sky'); //TMP add success class
                $('ul.list-comments').prepend(response);
                $btn.html($btn.data('label'));
                $btn.prop('disabled', false);
                $form.find('textarea').val('');
            },
            error: function(jqXHR, status, error) {
                alert('Erreur. Essaie encore.');
                $btn.prop('disabled', false);
            }
        });
    });
    $(document).on('submit', '.comment-form-edit', function(e){
        e.preventDefault();

        let $form = $(e.target),
            $btn = $form.find(':submit');

        $btn.html('<i class="fa fa-spinner fa-pulse"></i>');
        $btn.prop('disabled', true);

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function(response) {
                $btn.html('OK');
                $btn.prop('disabled', false);
                $form.parent().slideUp(function() {
                    $(this).empty().show();
                });
                $('.comment-item-'+response.uid).html($('#comment-edit-'+response.uid).val());
            },
            error: function(jqXHR, status, error) {
                alert('Erreur. Essaie encore.');
                $btn.prop('disabled', false);
            }
        });
    });
});

$(document)
    .on('fos_comment_show_form', '.fos_comment_comment_reply_show_form', function (event, data) {
        alert('form');
    });