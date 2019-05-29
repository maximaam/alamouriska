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
        $.get('/async/'+$this.data('type')+'-remove?uid=' + $this.data('uid'), function (response) {
            if (+response.status === 1) {
                $this.parents('li').fadeOut();
            }
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


    let $bubbles = $('.bubbles');
    if ($bubbles.length > 0) {
        (function($){

            // Define a blank array for the effect positions. This will be populated based on width of the title.
            let bArray = [];
            // Define a size array, this will be used to vary bubble sizes
            let sArray = [4,6,8,10,16];

            // Push the header width values to bArray
            for (let i = 0; i < $bubbles.width(); i++) {
                bArray.push(i);
            }

            // Function to select random array element
            // Used within the setInterval a few times
            function randomValue(arr) {
                return arr[Math.floor(Math.random() * arr.length)];
            }

            // setInterval function used to create new bubble every 350 milliseconds
            setInterval(function(){

                // Get a random size, defined as variable so it can be used for both width and height
                let size = randomValue(sArray);
                // New bubble appeneded to div with it's size and left position being set inline
                // Left value is set through getting a random value from bArray
                $bubbles.append('<div class="bubble" style="left: ' + randomValue(bArray) + 'px; width: ' + size + 'px; height:' + size + 'px;"></div>');

                // Animate each bubble to the top (bottom 100%) and reduce opacity as it moves
                // Callback function used to remove finsihed animations from the page
                $('.bubble').animate({
                        'bottom': '100%',
                        'opacity' : '-=0.7'
                    }, 3000, function(){
                        $(this).remove()
                    }
                );
            }, 350);

        })($);
    }

    $('input:file').on('change', function() {
        let $target = $(this);
        let $parent = $target.parents('fieldset');
        $parent.find('img').remove();

        let imgSrc = (window.URL || window.webkitURL).createObjectURL($target[0].files[0]);
        $parent.append($('<img src="' + imgSrc + '" alt="Image" class="mw-100 mt-2">'));
    });

    if ($('.almrsk-post').length) {
        $('#mot_inTamazight_help').append('<a href="https://www.lexilogos.com/clavier/tamazight.htm" target="_blank" class="ml-2">Clavier Tamazight <i class="fa fa-external-link"></i></a>');
        $('#mot_inArabic_help').append('<a href="https://www.lexilogos.com/clavier/araby.htm" target="_blank" class="ml-2">Clavier Arabe <i class="fa fa-external-link"></i></a>');

        $('#mot_question').parents('.form-group').addClass('almrsk-form-question');
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

    setTimeout(function () {
        $('#journal-public').fadeIn('slow');
    }, 5000);

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
});

$(document)
    .on('fos_comment_show_form', '.fos_comment_comment_reply_show_form', function (event, data) {
        alert('form');
    });