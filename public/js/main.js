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
                $bubbles.append('<div class="individual-bubble" style="left: ' + randomValue(bArray) + 'px; width: ' + size + 'px; height:' + size + 'px;"></div>');

                // Animate each bubble to the top (bottom 100%) and reduce opacity as it moves
                // Callback function used to remove finsihed animations from the page
                $('.individual-bubble').animate({
                        'bottom': '100%',
                        'opacity' : '-=0.7'
                    }, 3000, function(){
                        $(this).remove()
                    }
                );
            }, 350);

        })($);
    }


});

$(document)
    .on('fos_comment_show_form', '.fos_comment_comment_reply_show_form', function (event, data) {
        alert('form');
    });