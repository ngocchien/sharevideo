$('.view-more .btn-view').on('click', function () {
    $(this).hide();
    $('#loading-container .loading').show();
    var page = +$(this).data('page') + 1,
        that = $(this);
    $.ajax({
        url: '/index',
        dataType: 'json',
        data: {
            page: page,
            is_ajax: true
        },
        success: function (response) {
            $('.more-videos .videos').append(response.html);
            if (response.status == true) {
                that.show();
                that.data('page', page);
            }
            $('#loading-container .loading').hide();
        }
    });
});