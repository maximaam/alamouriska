$(document).ready(function() {

    $('.js_almrsk-like').on('click', function(e) {
        e.preventDefault();

        let $this = $(this);

        $.get('/async/like?owner=' + $this.data('owner') + '&ownerId=' + $this.data('id'), function (data) {
            alert(data);
        });


    })
});