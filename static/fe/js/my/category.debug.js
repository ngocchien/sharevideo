/**
 * Created by ChienNguyen on 2/18/2017.
 */
var Category = {
    init: function () {
        $(document).ready(function () {
            $(document).ready(function () {
                var videos = $('#category-page .container .main-content .header .videos'),
                    navigation = $('#category-page .container .main-content .header .navigation');
                videos.slick({
                    slidesToShow: 1,
                    dots: true,
                    appendDots: navigation,
                    arrows: false,
                    pauseOnHover: true,
                    autoplay: true,
                    autoplaySpeed: 5000
                });

                /*var btnViewMore = $('#category-page .view-more .btn-view');
                btnViewMore.parent().hide();
                Category.moreVideo();*/
            })
        });

    },
    moreVideo: function () {
        /*$(window).bind('scroll', function () {
            if (CategoryPage.isLoading || CategoryPage.isContinued == false) {
                return;
            }

            var positionMobileApps = $('#footer').offset().top,
                flagScroll = $(this).scrollTop() + $(this).height() - ($(this).height() / 6) > positionMobileApps;

            if (flagScroll) {
                CategoryPage.isLoading = true;
                CategoryPage.addVideo();
            }
        });*/
    }
};
Category.init();


