
/*
 * ChienNguyen
 */

//DEFINE
var baseURL = window.location.host,
    header = $('#header'),
    btnState = false,
    lastScrollTop = -100,
    temp = 0;
$(window).bind('scroll', function (e) {
    if (Math.abs($(window).scrollTop() - lastScrollTop) < 5) {
        return;
    }
    lastScrollTop = $(window).scrollTop();

    var currentScrollTop = $(this).scrollTop();

    if (currentScrollTop >= 35) {
        if (!header.hasClass('fixed')) {
            header.addClass('fixed');
        }
    } else if (header.hasClass('fixed')) {
        header.removeClass('fixed');
    }

    /*else {
     if (btnState) {
     $('#btn-fixed').fadeOut(300);
     btnState = false;
     }
     }*/
});

var coverPage = $('#cover-page'),
    page = $('#page'),
    footer = $('#footer'),
    sideMenu = $('#side-menu'),
    header = $('#header');
$('#header .menu .btn-menu, #cover-page').click(function (e) {
    e.preventDefault();
    $('body').toggleClass('lock-scroll');
    coverPage.toggleClass('active');
    header.toggleClass('active');
    page.toggleClass('active');
    footer.toggleClass('active');
    sideMenu.toggleClass('active');
});

var btnSearch = $('#header .menu .btn-search'),
    searchForm = $('#search'),
    inputQuestion = $('#search .over .container .search-form .question'),
    btnCloseSearch = $('#search .over .btn-close');
btnSearch.click(function (e) {
    e.preventDefault();
    searchForm.fadeIn(300);
    inputQuestion.focus();
});
btnCloseSearch.click(function (e) {
    e.preventDefault();
    searchForm.fadeOut(300);
});



