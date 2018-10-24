jQuery(document).ready(function ($) {
    $(document).on('submit', '#jintegrity-check', function (e) {
        e.preventDefault();
        $('.jintegrity .info-page').html('');
        $('.jintegrity .ajax-loader').show();
        $('.jintegrity #jintegrity-submit').hide();
        $.ajax({
            url: jintegrity_url,
            data: $(this).serialize(),
            type: 'POST',
            success: function (response) {
                $('.jintegrity').html($(response).find('.jintegrity').html());
                $('.jintegrity .ajax-loader').hide();
                $('.jintegrity #jintegrity-submit').show();
            }
        });
    });
});