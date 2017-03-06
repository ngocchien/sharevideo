


// Main
(function (Main, $) {
    Main.topTips = [
        {
            url: "#",
            text: "Xin chào! Chúc bạn một ngày tràn đầy sức sống cùng với thatvidieu.com",
            attr: []
        },
        {
            url: FeelStatic.baseURL + 'user/authenticate',
            text: "Mẹo: Đăng nhập thatvidieu.com để có trải nghiệm tốt nhất",
            attr: [
                {
                    "key": "onclick",
                    "value": "Main.authenticate(this); return false;"
                }
            ]
        },
        {
            url: FeelStatic.baseURL + 'hot-today?utm_source=tips',
            text: "HOT NOW là bảng xếp hạng video được cộng đồng quan tâm, tương tác cao",
            attr: []
        },
        {
            url: FeelStatic.baseURL + 'ranking?utm_source=tips',
            text: "Bạn có muốn xem Bảng xếp hạng video tuần này?",
            attr: []
        },
        {
            url: "#",
            text: "Mẹo: Chỉ cần cuộn xuống dưới bạn có thể xem video gợi ý tiếp theo",
            attr: []
        },
        {
            url: FeelStatic.baseURL + 'personal?utm_source=tips',
            text: "Có 12 video hấp dẫn dành riêng cho bạn. CHECK NGAY VÀ LUÔN",
            attr: []
        }
    ];

    Main.speedNetwork 			= 800;
    Main.speedNetworkCalculated	= false;
    Main.aspectRatio            = '16:9';

    Main.isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (
                Main.isMobile.Android()
                || Main.isMobile.BlackBerry()
                || Main.isMobile.iOS()
                || Main.isMobile.Opera()
                || Main.isMobile.Windows()
            );
        },
        iOSVersion: function() {
            return parseFloat(('' + (/CPU.*OS ([0-9_]{1,5})|(CPU like).*AppleWebKit.*Mobile/i.exec(navigator.userAgent) || [0,''])[1]).replace('undefined', '3_2').replace('_', '.').replace('_', '')) || false;
        }
    };

    Main.playWithVideoJS = function() {
        return true;
        var checkVideoJs = FeelStatic.getCookie('feel_video_js');
        return checkVideoJs;
    };

    Main.isGoogleBot = function() {
        return /bot|googlebot|crawler|spider|robot|crawling/i.test(navigator.userAgent)
    };

    Main.isNativeApp = function () {
        return navigator.userAgent.indexOf('TVDNativeIOS') > 0 || navigator.userAgent.indexOf('TVDNativeAndroid') > 0;
    };

    Main.isEmail = function (email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    };

    Main.getParameterByName = function(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    };

    Main.wfcLabel = 'wifichua';
    Main.partnerList = {
        wifichua: {
            label: 'wifichua',
            channel: '3265917368',
            adsProbability: 0.4
        },
        KGM: {
            label: 'KGM',
            channel: '7696116969',
            adsProbability: 0.4
        },
        TBD: {
            label: 'TBD',
            channel: '1560354963',
            adsProbability: 0.4
        },
        H97: {
            label: 'H97',
            channel: '5701019763',
            adsProbability: 0.4
        },
        ATP: {
            label: 'ATP',
            channel: '1526342161',
            adsProbability: 0.4
        }
    };

    Main.getPartner = function() {
        var partner = FeelStatic.getCookie('feel_partner');
        if (partner) {
            return partner;
        }

        var utmSource = Main.getParameterByName('utm_source');
        if (utmSource && Main.partnerList[utmSource]) {
            FeelStatic.setCookie('feel_partner', utmSource);
            return utmSource;
        }

        return false;
    };

    Main.getPartnerChannelID = function() {
        var partner = Main.getPartner();
        if (partner) {
            return Main.partnerList[partner].channel;
        }
        return '1789184165'
    };

    Main.getAdsProbability = function() {
        var partner = Main.getPartner();
        if (partner) {
            return Main.partnerList[partner].adsProbability;
        }
        return 0.2; //20% default
    };

    Main.isFromWFC = function() {
        var partner = Main.getPartner();
        return partner && partner == Main.partnerList.wifichua.label;

        //if (FeelStatic.getCookie('feel_partner') == Main.wfcLabel) {
        //    return true;
        //}
        //var utmSource = Main.getParameterByName('utm_source');
        //if (utmSource == Main.wfcLabel) {
        //    FeelStatic.setCookie('feel_partner', Main.wfcLabel, 1000);
        //    return true;
        //}
        //return false;
    };

    Main.init = function () {
        var header      = $('#header');
        var btnState    = false;

        var lastScrollTop   = -100,
            temp            = 0;
        $(window).bind('scroll', function(e) {
            if (Math.abs($(window).scrollTop() - lastScrollTop) < 5) {
                return;
            }
            lastScrollTop = $(window).scrollTop();

            var currentScrollTop = $(this).scrollTop();

            if (currentScrollTop >= 35) {
                if (!header.hasClass('fixed')) {
                    header.addClass('fixed');
                }
            } else if (header.hasClass('fixed') && !Main.isFromWFC()) {
                header.removeClass('fixed');
            }

            /*else {
             if (btnState) {
             $('#btn-fixed').fadeOut(300);
             btnState = false;
             }
             }*/
        });

        var coverPage   = $('#cover-page'),
            page        = $('#page'),
            footer      = $('#footer'),
            sideMenu    = $('#side-menu');
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

        var btnTop = $('#btn-fixed .btn-top');
        btnTop.click(function (e) {
            e.preventDefault();
            $('html, body').animate({ scrollTop: 0 }, 500);
        });

        Main.checkLogin();
        for(var i = 0; i < 8; i++) {
            setTimeout(Main.calculateNetworkSpeed, 1000 * Math.pow(2, i));
        }

        Main.loadTip();


        if (Main.isFromWFC()) {
            header.find('.news').addClass('hide-forever');
            header.addClass('fixed');
            $('#page').css('padding-top', '74px');
            $('#btn-fixed').addClass('hide-forever');
            $('#beginner').addClass('hide-forever');
            $('#hot-video-2').addClass('post-wfc');
            $('#hot-video-3').addClass('post-wfc');
        } else {
            header.find('.news').removeClass('hide-forever');
            $('#btn-fixed').show();
            $('#page').css('padding-top', '144px');
        }

        //Provide special way to style the control depend on browser and os
        var body = $('body');
        for(var prop in Main.isMobile) {
            if (Main.isMobile[prop]()) {
                if (prop == 'any') {
                    body.addClass('mobile');
                }
                else if (prop == 'iOSVersion') {
                    body.addClass('ios' + Math.floor(Main.isMobile[prop]()));
                }
                else {
                    body.addClass(prop.toLowerCase());
                }
            }
        }
    };

    Main.tipIndex = 0;
    Main.loadTip = function(manual) {
        var topTip = $('#top-tips');
        if (manual) {
            Main.tipIndex++;
            if (Main.tipIndex == Main.topTips.length) {
                Main.tipIndex = 0;
            }
            topTip.fadeOut(300);
        } else {
            Main.tipIndex = Math.floor(Math.random() * Main.topTips.length);
        }

        var delay = manual ? 300 : 0;
        topTip.fadeIn(300);
        setTimeout(function() {
            //init random top tips
            topTip.html(Main.topTips[Main.tipIndex].text);
            topTip.attr('href', Main.topTips[Main.tipIndex].url);

            var tipAttr = Main.topTips[Main.tipIndex].attr;
            if (tipAttr.length > 0) {
                for (var i = 0; i < tipAttr.length; i++) {
                    topTip.attr(tipAttr[i].key, tipAttr[i].value);
                }
            }
        }, delay);
    };

    Main.isLoggedIn = function() {
        return FeelStatic.getCookie('feel_username') && FeelStatic.getCookie('feel_avatar')
    };

    Main.checkLogin = function() {
        var profile = Main.getProfile();
        if (profile !== false) {
            Main.authenticationDisplay(profile.username, profile.avatar);
            return
        }

        var request = $.ajax({
            url: FeelStatic.baseURL + 'user/get',
            method: 'GET',
            dataType: 'json'
        });

        request.done(function(data) {
            if (data.error_message) {
                Main.authenticationDisplay(false, false);
            } else {
                Main.authenticationDisplay(data.name, data.avatar);
            }
        });
    };

    Main.showCaptchaInput = function() {
        Main.notification('Vì sự an toàn của bạn và thatvidieu.com. Bạn cần nhập captcha để tiếp tục');
    };

    Main.getProfile = function() {
        var username = FeelStatic.getCookie('feel_username');
        var avatar = FeelStatic.getCookie('feel_avatar');
        if (username && avatar) {
            return {
                username: decodeURIComponent(username).replace(/\*\*\*/g, ' '),
                avatar: decodeURIComponent(avatar)
            }
        }
        return false
    };

    Main.authenticationDisplay = function(name, avatar) {
        var header = $('#header');
        var btnLogin = header.find('.btn-login');
        var userDiv = header.find('.user');
        //sidemenu
        var sidemenu = $('#side-menu');
        var unauthorized = sidemenu.find('.unauthorized');
        var loggedIn = sidemenu.find('.logged-in');

        if (name === false) {
            btnLogin.show();
            userDiv.hide();

            unauthorized.show();
            loggedIn.hide();
        } else {
            btnLogin.hide();

            userDiv.find('.avt').css('background-image', 'url(' + avatar + ')').attr('title', name);
            var username = userDiv.find('.username');
            username.attr('title', name);
            username.find('.name').html(name);

            userDiv.show();

            unauthorized.hide();

            loggedIn.find('.avt').css('background-image', 'url(' + avatar + ')').attr('title', name);
            loggedIn.find('.username').html(name).attr('title', name);
            loggedIn.show();
        }


    };

    Main.logout = function() {
        var request = $.ajax({
            url: FeelStatic.baseURL + 'user/logout',
            method: 'GET'
        });

        request.done(function() {
            location.reload();
        });
    };

    Main.calculateNetworkSpeed = function() {
        return;
        var start = 0;
        function updateProgress (oEvent) {
            if (start == 0) start = (new Date()).getTime();
            if (oEvent.lengthComputable) {
                var speed = oEvent.loaded / ((new Date()).getTime() - start) * 1000 / 1024;
                if (!isFinite(speed)) {
                    speed = 2000;
                }

                //Prevent compare with default init network speed
                if (Main.speedNetworkCalculated) {
                    Main.speedNetwork = Math.max(Main.speedNetwork, speed);
                }
                else {
                    Main.speedNetwork = speed;
                    Main.speedNetworkCalculated = true;
                }
                // ...
            } else {
                // Unable to compute progress information since the total size is unknown
            }
        }

        var oReq = new XMLHttpRequest();
        oReq.addEventListener("progress", updateProgress);
        oReq.open("GET", FeelStatic.baseURL + 'resources/logo-84x30.png?' + (new Date()).getTime(), true);
        oReq.send();
    };

    Main.checkNetwork = function () {
        var imageUrl = FeelStatic.baseURL + 'resources/img-speedtest-42x57.png',
            fileSize = 24910;

        var startTime, endTime;
        var img = new Image();

        startTime = (new Date()).getTime();
        img.src = imageUrl;

        img.onload = function () {
            endTime = (new Date()).getTime();
            showResults();
        };

        function showResults() {
            var duration = (endTime - startTime) / 1000;
            var bitsLoaded = fileSize * 8;
            var speedBps = (bitsLoaded / duration).toFixed(2);
            var speedKbps = (speedBps / 1024).toFixed(2);
            var speedMbps = (speedKbps / 1024).toFixed(2);

            Main.speedNetwork = speedKbps;
        }
    };

    Main.notification = function (text, width) {
        var notification = $('#notification'),
            container = notification.find('.container'),
            btnClose = container.find('.btn-close'),
            content = notification.find('.container > p');

        if (width) {
            container.width(width);
        }

        content.html(text);
        notification.fadeIn(300);

        btnClose.click(function (e) {
            e.preventDefault();
            notification.fadeOut(300);
            setTimeout(function() {
                container.removeAttr('style');
            }, 300);
        });
    };

    Main.checkShowReferalForm = function() {
        return;
        if (Main.isLoggedIn()) {
            var code = FeelStatic.getCookie('feel_code');
            if (code == "") {
                $('#referal-container').show();
            }
        }
    };

    Main.showReferalCode = function() {
        var code = FeelStatic.getCookie('feel_code');
        var html = '';
        if (code != '') {
            html = '<p>Mã khuyến mãi:</p><p class="referal-code">' + code +'</p>';
        } else {
            html = 'Chức năng đang được cập nhật :)';
        }
        Main.notification(html);
    };

    Main.updateReferalCode = function(form) {
        form = $(form);
        var code = form.find('.referal-code-value');
        if (code.val() == '') {
            Main.notification('Vui lòng nhập code khuyến mãi');
            return;
        }

        var request = $.ajax({
            url: FeelStatic.baseURL + '/user/updateCode?code=' + code.val(),
            method: 'GET'
        });

        request.done(function(data) {
            data = JSON.parse(data);
            if (data.error_message) {
                Main.notification(data.error_message);
                return;
            }
            FeelStatic.setCookie('feel_code', code.val(), 30 * 1000);
            form.parent().fadeOut(300);
            Main.notification('<p>Chúc mừng bạn đã trở thành <br/>VBA member</p>');
        });
    };

    Main.maintain = function (text) {
        var maintain = $('#maintain'),
            btnClose = $('#maintain .container .btn-close'),
            content = $('#maintain .container .content');

        content.html(text);
        maintain.fadeIn(300);

        btnClose.click(function (e) {
            e.preventDefault();
            maintain.fadeOut(300);
        });
    };

    Main.floatingSidebar = function (container, sidebar) {
        // var bottomLineSidebar 	= sidebar.offset().top + sidebar.height();
        // var footerHeight 		= $('#footer').height();
        // var state				= '';
        // var lastScrollTop		= 0;
        //
        // $(window).bind('scroll', function() {
        //     if (
        //         $(window).height() > sidebar.height()
        //         || $(document).width() < 750
        //         || Math.abs($(window).scrollTop() - lastScrollTop) < 5
        //     ) {
        //         return;
        //     }
        //     lastScrollTop = $(window).scrollTop();
        //
        //     if ($(window).scrollTop() + $(window).height() >= bottomLineSidebar + 20) {
        //         if ($(window).scrollTop() + $(window).height() >= $(document).height() - footerHeight) {
        //             if (state != 'absolute_auto') {
        //                 state = 'absolute_auto';
        //                 sidebar.css('position', 'absolute')
        //                     .css('top', $('#category-page .container').height() - sidebar.height() - 25 + 'px')
        //                     .css('right', 20);
        //             }
        //         }
        //         else {
        //             if (state != 'fixed') {
        //                 state = 'fixed';
        //                 sidebar.css('position', 'fixed')
        //                     .css('top', $(window).height() - sidebar.height() - 50)
        //                     .css('right', container.offset().left + 20)
        //                     .css('bottom', 'auto');
        //             }
        //         }
        //     } else if (state != 'absolute_zero') {
        //         state = 'absolute_zero';
        //         sidebar.css('position', 'absolute')
        //             .css('top', 0)
        //             .css('right', 20)
        //             .css('bottom', 'auto');
        //     }
        // });

        var hWindow             = $(window).height(),
            hSidebar            = sidebar.height()
            leftContainer       = container.offset().left,
            topContainer        = container.offset().top,
            checkHeight         = hWindow > hSidebar,
            lastScrollTop		= -100,
            startScroll         = false;

        $(window).bind('scroll', function() {
            if (
                $(document).width() < 750
                || Math.abs($(window).scrollTop() - lastScrollTop) < 5
            ) {
                return;
            }

            var wScrollTop  = $(window).scrollTop(),
                hContainer  = container.height();

            if (checkHeight) {
                var checkTop    = wScrollTop > topContainer - 100,
                    checkBottom = topContainer + hContainer <= wScrollTop + hSidebar + 95;

                if (!checkTop) {
                    sidebar.css('position', 'absolute')
                        .css('top', 0)
                        .css('right', 20)
                        .css('bottom', 'auto');
                } else if (!checkBottom) {
                    sidebar.css('position', 'fixed')
                        .css('top', 95)
                        .css('right', leftContainer + 20)
                        .css('bottom', 'auto');
                } else {
                    sidebar.css('position', 'absolute')
                        .css('top', 'auto')
                        .css('right', 20)
                        .css('bottom', 0);
                }
            } else {
                var topSidebar          = sidebar.offset().top,
                    bottomLineSidebar   = topSidebar + hSidebar,
                    checkScrollDown     = wScrollTop > lastScrollTop,
                    checkTopSidebar     = wScrollTop <= topSidebar - 95,
                    checkBottomSidebar  = wScrollTop + hWindow >= bottomLineSidebar + 40,
                    checkStart          = wScrollTop + 95 <= topContainer,
                    checkEnd            = wScrollTop + hWindow >= topContainer + hContainer;

                if (!startScroll) {
                    if (checkBottomSidebar) {
                        startScroll = true;
                    }
                } else {
                    if (checkStart) {
                        sidebar.css('position', 'absolute')
                            .css('top', 0)
                            .css('right', 20)
                            .css('bottom', 'auto');
                        return;
                    }

                    if (checkEnd) {
                        sidebar.css('position', 'absolute')
                            .css('top', hContainer - hSidebar)
                            .css('right', 20)
                            .css('bottom', 'auto');
                        return;
                    }

                    if (checkScrollDown) {
                        // Scroll Down
                        if (checkTopSidebar) {
                            sidebar.css('position', 'absolute')
                                .css('top', topSidebar - 175)
                                .css('right', 20)
                                .css('bottom', 'auto');
                        } else if (checkBottomSidebar) {
                            sidebar.css('position', 'fixed')
                                .css('top', hWindow - hSidebar - 50)
                                .css('right', leftContainer + 20)
                                .css('bottom', 'auto');
                        }
                    } else {
                        // Scroll Up
                        if (checkBottomSidebar) {
                            sidebar.css('position', 'absolute')
                                .css('top', topSidebar - 175)
                                .css('right', 20)
                                .css('bottom', 'auto');
                        } else if (checkTopSidebar) {
                            sidebar.css('position', 'fixed')
                                .css('top', 95)
                                .css('right', leftContainer + 20)
                                .css('bottom', 'auto');
                        }
                    }
                }
            }

            lastScrollTop = wScrollTop;
        });
    };

    Main.authenticate = function (btn) {
        if (Main.isLoggedIn()) {
            return;
        }
        btn = $(btn);
        var authentionURL = btn.attr('href') + '?redirect=' + window.location.href;

        var newwindow;
        var  screenX    = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft,
            screenY    = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop,
            outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth,
            outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22),
            width    = 500,
            height   = 270,
            left     = parseInt(screenX + ((outerWidth - width) / 2), 10),
            top      = parseInt(screenY + ((outerHeight - height) / 2.5), 10),
            features = (
                'width=' + width +
                ',height=' + height +
                ',left=' + left +
                ',top=' + top
            );
        newwindow=window.open(authentionURL,'Login_by_facebook',features);
        if (window.focus) {newwindow.focus()}
    };

    Main.sendAuthenticationToken = function(token) {
        var request = $.ajax({
            url: FeelStatic.baseURL + 'user/authenticate?app_request=true&access_token=' + token,
            method: 'GET',
            dataType: 'json'
        });

        request.done(function(data) {
            if (data.success === false) {
                alert('Không đăng nhập được hệ thống, vui lòng thử lại!');
            } else {
                Main.authenticationDisplay(data.username, data.avatar);
            }
        });
    };

    Main.saveLoadedVideos = function(ids) {
        var request = $.ajax({
            url: FeelStatic.baseURL + 'post/saveLoadedVideos',
            method: 'POST',
            data: {
                ids: ids
            },
            dataType: 'json'
        });

        request.done(function(data) {
            console.log(data);
        });
    };

    Main.checkAspectRatio = function () {
        var wWindow = $(window).width(),
            hWindow = $(window).height();

        if (wWindow > hWindow) {
            Main.aspectRatio = '16:9';
        } else {
            Main.aspectRatio = '4:3';
        }
    };

    Main.showVoucher = function() {
        var voucher = ViewPage.getVoucherNumber();
        if (voucher) {
            Main.renderVoucherAlert(voucher);
            return;
        }

        if (!Main.isLoggedIn()) {
            return;
        }

        Main.notification('Đang tải dữ liệu ...');
        var request = $.ajax({
            url: FeelStatic.baseURL + 'user/voucher',
            dataType: 'json'
        });

        request.done(function(data) {
            if (data.error_message) {
                Main.notification(data.error_message);
                return;
            }
            Main.renderVoucherAlert(data.voucher);
        });
    };

    Main.renderVoucherAlert = function(voucher) {
        var container = $('#voucher-alert');
        container.find('.voucher-number').html(voucher);
        container.find('.voucher-alert-general').show();
        container.find('.voucher-detail').hide();

        Main.notification(container.html(), 380);
    };

    Main.showVoucherdetail = function() {
        var modal = $('#notification');
        modal.find('.voucher-alert-general').hide();
        modal.find('.voucher-detail').show();
    };

    Main.showModalForm = function(title, content, btns) {
        var modal = $('#modal-form');
        modal.find('.modal-title').html(title);
        modal.find('.modal-content').html(content);

        var footer = modal.find('.modal-footer');
        footer.html('');

        if (btns) {
            for (var i = 0; i < btns.length; i++) {
                var btn = $('<button />');
                btn.html(btns[i].text);
                btn.addClass('btn').addClass(btns[i].cls);
                btn.bind('click', btns[i].handler);

                btn.appendTo(footer);
            }
        }

        var btnClose = $('<button />');
        btnClose.html('Đóng');
        btnClose.addClass('btn').addClass('btn-normal');
        btnClose.bind('click', Main.hideModalForm);
        btnClose.appendTo(footer);

        modal.fadeIn(300);
    };

    Main.hideModalForm = function() {
        $('#modal-form').fadeOut(300);
    };

    Main.showTransferVoucherForm = function() {
        var voucher = ViewPage.getVoucherNumber();
        if (voucher) {
            Main.showVoucher();
            return;
        }

        var transferVoucherContainer = $('#transfer-voucher-form');
        var btns = [
            {
                text: 'Đổi voucher',
                cls: 'transfer-voucher-btn',
                handler: Main.transferVoucher
            }
        ];

        Main.showModalForm('', transferVoucherContainer.html(), btns);
    };

    Main.transferVoucher = function() {
        var form = $('#modal-form');
        var transferVoucher = form.find('.txt-tranfer-voucher');
        if (transferVoucher.val() == '') {
            alert('Vui lòng nhập Code Vi Diệu');
            transferVoucher.focus();
            return;
        }

        Main.getTransferVoucherFromServer(transferVoucher.val());
    };

    Main.getTransferVoucherFromServer = function(code) {
        code = code.toUpperCase();
        var form = $('#modal-form');
        var btn = form.find('.transfer-voucher-btn');
        btn.prop('disabled', true).html('Đang tải...');

        var request = $.ajax({
            url: FeelStatic.baseURL + 'user/transferVoucher',
            method: 'POST',
            data: {
                transfer_code: code
            },
            dataType: 'json'
        });

        request.done(function(data) {
            if (data.error_message) {
                alert(data.error_message);
                btn.prop('disabled', false).html('Đổi voucher');
                return;
            }
            Main.hideModalForm();
            Main.renderVoucherAlert(data.voucher);
        });
    };
})(window.Main = window.Main || {}, jQuery);


// Home Page
(function (HomePage, $) {
    HomePage.isLoading = false;
    HomePage.isContinued = true;
    HomePage.currentPage = 0;

    HomePage.init = function () {
        // var btnBeginner = $('#beginner .container .btn-start'),
        //     beginner = $('#beginner');
        //
        // if (FeelStatic.getCookie('beginner') != '') {
        //     beginner.hide();
        // } else {
        //     beginner.fadeIn(300);
        //     btnBeginner.click(function (e) {
        //         e.preventDefault();
        //         FeelStatic.setCookie('beginner', 'beginner', 365);
        //         beginner.fadeOut(300);
        //     });
        // }

        var btnViewMore = $('#home-page .sub-content .view-more .btn-view');
        btnViewMore.click(function (e) {
            e.preventDefault();
            btnViewMore.parent().hide();
            HomePage.addVideo();
            HomePage.moreVideo();

            var container = $('#home-page .sub-content .content'),
                sidebar = $('#home-page .sub-content .content .sidebar');
            Main.floatingSidebar(container, sidebar);
        });

        if ($(window).width() <= 414) {
            HomePage.slickSlide();
        }
    };

    HomePage.slickSlide = function () {
        var videos = $('#home-page .top-post .videos'),
            navigation = $('#home-page .top-post .navigation');

        videos.slick({
            slidesToShow: 1,
            dots: true,
            appendDots: navigation,
            arrows: false,
            pauseOnHover: true,
            autoplay: true,
            autoplaySpeed: 5000
        });
    };

    HomePage.moreVideo = function () {
        $(window).bind('scroll', function() {
            if (HomePage.isLoading || HomePage.isContinued == false) {
                return;
            }

            var positionMobileApps = $('#home-page .mobile-apps').offset().top,
                flagScroll = $(this).scrollTop() + $(this).height() - ($(this).height() / 6) > positionMobileApps;

            if (flagScroll)  {
                HomePage.isLoading = true;
                HomePage.addVideo();
            }
        });
    };

    HomePage.addVideo = function () {
        var loading = $('#loading-container .loading');
        loading.fadeIn(500);

        var request = $.ajax({
            url: FeelStatic.baseURL + 'post/loadmore',
            method: 'GET',
            dataType: 'json',
            data: {
                page: HomePage.currentPage++
            },
            statusCode: {
                403: Main.showCaptchaInput
            }
        });
        request.done(function(data) {
            HomePage.isContinued = data.continue;

            var postVideo = $('#home-page .sub-content .content .more-videos .videos .post').first(),
                moreVideos = $('#home-page .sub-content .content .more-videos .videos');
            for(var i = 0; i < data.list.length; i++) {
                var post = postVideo.clone();

                var poster = post.find('.poster');
                poster.css('background-image', 'url(' + FeelStatic.baseURL + data.list[i].normal_picture + ')')
                    .find('.thumbnail')
                    .attr('src', FeelStatic.baseURL + data.list[i].normal_picture)
                    .attr('title', data.list[i].title)
                    .attr('alt', data.list[i].title);

                poster.find('.duration').text(data.list[i].format_duration);

                if (data.list[i].score > 0) {
                    poster.find('.hot .score').text(data.list[i].score);
                    poster.find('.hot.hide').removeClass('hide');
                } else {
                    poster.find('.hot').addClass('hide');
                }

                poster.find('.view .number').text(data.list[i].format_views);
                poster.find('.link')
                    .attr('href', FeelStatic.baseURL + 'post/' + data.list[i].seo_id)
                    .attr('title', data.list[i].title);

                var content = post.find('.content');
                content.find('.title > a')
                    .text(data.list[i].title)
                    .attr('href', FeelStatic.baseURL + 'post/' + data.list[i].seo_id)
                    .attr('title', data.list[i].title);
                content.find('.description').text(data.list[i].description);
                content.find('.author .username')
                    .text(data.list[i].moderator.name)
                    .attr('title', data.list[i].moderator.name)
                    .prop('href', data.list[i].moderator.profile_url);

                post.appendTo(moreVideos);
            }

            HomePage.isLoading = false;
            loading.hide();
        });
    };
})(window.HomePage = window.HomePage || {}, jQuery);

// View Page
(function (ViewPage, $) {
    ViewPage.isLoading 			= false;
    ViewPage.maxRenderedVidIdx	= 0;
    ViewPage.videoList 			= [];
    ViewPage.currentId 			= false;
    ViewPage.isContinue 		= true;
    ViewPage.playerList 		= [];
    ViewPage.currentIndex 		= 0;
    ViewPage.activeVideo 		= null; //Store the last active video object
    ViewPage.timeTracking 		= 10;
    ViewPage.videoQuality 		= 0;
    ViewPage.eventScroll 		= 'scroll';//Main.isMobile.any() ? 'touchstart' : 'scroll';
    ViewPage.volumeDefault 		= 90;
    ViewPage.hasVideoIntro 		= false;
    ViewPage.firstVideo 		= true;
    ViewPage.lastScrollTop 		= -100; //Last scrollTop that onscroll has been checked in auto play video event
    ViewPage.minRenderedVidIdx	= 0;

    ViewPage.init = function () {
        $('html, body').animate({ scrollTop: 80 }, 1000);

        if (Main.isMobile.any()) {
            $('#footer').hide();
        }

        var container = $('#view-page .container'),
            sidebar = $('#view-page .container .sidebar');
        Main.floatingSidebar(container, sidebar);

        ViewPage.addVideo();
        /*ViewPage.getVideo(function() {
         //Trigger scroll event to force render video at starting up
         $(window).scroll();
         });*/

        if (Main.playWithVideoJS()) {
            VideoManager.autoPlayVideo();
        } else {
            ViewPage.autoPlayVideo();
        }

        var notification = $('#view-page .container .main-content .post .show-notification');
        if (ViewPage.firstVideo && !FeelStatic.getCookie('feel_hide_firstVideo')) {
            notification.show();
            ViewPage.firstVideo = false;
        }

        notification.find('.btn-close').click(function (e) {
            e.preventDefault();
            FeelStatic.setCookie('feel_hide_firstVideo', 'true', 365);
            notification.hide();
        });

        $(window).resize(function() {
            var playerContainer = $('.video-js');
            var container = playerContainer.parent();
            playerContainer.width(container.width()).height(container.height());
        });

            //Init the boolean value ViewPage.isScrolling which is used to check that user is scrolling or not
        var t;
        $(window).bind('scroll', function() {
            ViewPage.isScrolling = true;

            if (t) {
                clearTimeout(t);
            }

            t = setTimeout(function() {
                ViewPage.isScrolling = false;
            }, 300);
        });

        $(window).unload(function() {
            $(window).scrollTop(0);
        });
    };

    ViewPage.reportVideo = function(id, reason) {
        var request = $.ajax({
            url: FeelStatic.baseURL + 'notify/sendReport',
            method: 'POST',
            dataType: 'json',
            data: {
                id: id,
                reason: reason
            },
            statusCode: {
                403: Main.showCaptchaInput
            }
        });

        request.done(function(data) {
            if (data.error_message) {
                Main.notification(data.error_message);
                return;
            }
            Main.notification('Thật vi diệu chân thành cám ơn đóng góp của bạn nhé!');
        });
    };

    ViewPage.addVideo = function () {
        var lastScrollTop = -100;

        $(window).bind('scroll', function() {
            if (
                ViewPage.isLoading
                || (
                    Math.abs($(window).scrollTop() - lastScrollTop) < 10
                    && Math.max(ViewPage.currentIndex, 0) < ViewPage.maxRenderedVidIdx - 1 //Need at least 2 video prepared below viewing video, if not, force loadding new video
                )
            ) {
                return;
            }
            lastScrollTop = $(window).scrollTop();

            var footerPos 			= $(document).height() - $('#footer').height(),
                isRenderNeeding 	= $(this).scrollTop() + $(this).height() + 200 > footerPos //User is staying at footer
                    || Math.max(ViewPage.currentIndex, 0) >= ViewPage.maxRenderedVidIdx - 1
                    || Math.max(ViewPage.currentIndex, 0) <= ViewPage.minRenderedVidIdx; //Need at least 2 video prepared below viewing video

            if (isRenderNeeding)  {
                if (ViewPage.currentIndex >= ViewPage.videoList.length - 2 && ViewPage.isContinue == true) {
                    ViewPage.getVideo(function() {
                        ViewPage.renderPosts(ViewPage.videoList);
                    });
                }

                ViewPage.renderPosts(ViewPage.videoList);
            }
        });
    };

    ViewPage.renderPostDetails = function(post, vid, idx) {
        var postId = vid.id,
            idContainer = 'video-container-' + postId + '-' + idx,
            sourceList = null;//listVideo[idx].source_list

        post.find('.header').removeClass('require-warning-confirm');
        post.find('#video-container').append($(document.createElement('div')).attr('id', idContainer));

        var shareQuick = post.find('.header .line-style.line-end .social');
        shareQuick.find('.short-link, .facebook, .google, .twitter').attr('data-title', vid.title);
        shareQuick.find('.short-link, .facebook, .google, .twitter')
            .attr('data-seo_id', vid.seo_id)
            .attr('data-short-link', vid.short_link);

        post.find('.header .line-style.line-end .btn-report').attr('data-text', postId);

        shareQuick.removeClass('active');

        post.find('.show-notification').hide();

        post.find('.content > .title').text(vid.title);

        var author = post.find('.content .info .author');
        author.find('.avt').css('background-image', 'url(' + vid.moderator.avatar + ')');
        author.find('.user .username').text(vid.moderator.name);
        author.find('.user .created .time').text(vid.fantastic_time);
        author.find('a').prop('href', vid.moderator.profile_url);

        post.find('.content .info .view .number').text(vid.format_views);

        var like = post.find('.content .score-container .like');
        like.attr('data-text', vid.id).attr('id', 'like-' + vid.id);

        post.find('.cmt-form').attr('data-text', vid.id);


        var likeBtn = like.find('.btn-like');
        var dislikeBtn = like.find('.btn-dislike');

        likeBtn.find('.number').text(vid.format_likes);
        dislikeBtn.find('.number').text(vid.format_dislikes);

        if (vid.is_like) {
            likeBtn.addClass('active');
        } else {
            likeBtn.removeClass('active');
            dislikeBtn.removeClass('active');
        }

        var content = post.find('.content'),
            hot = content.find('.score-container .hot');
        if (vid.score > 0) {
            hot.find('img.red').removeClass('hide');
            hot.find('img.gray').addClass('hide');
            hot.find('p').removeClass('hide');
            hot.find('p .score').text(vid.score);
        } else {
            hot.find('img.red').addClass('hide');
            hot.find('img.gray').removeClass('hide');
            hot.find('p').addClass('hide');
            hot.find('p .score').text(0);
        }

        content.find('.readMore-container .description-container .description').text(vid.description);

        var share = content.find('.readMore-container .share .social');
        share.find('.facebook, .google, .twitter').attr('data-title', vid.title);
        share.find('.facebook, .google, .twitter')
            .attr('data-seo_id', vid.seo_id)
            .attr('data-short-link', vid.short_link);

        var elementTags = content.find('.tags ul'),
            listTags = vid.categories,
            defaultTag = $('<li><a></a></li>');
        elementTags.text('');

        for (var i = 0; i < listTags.length; i++) {
            var tag = defaultTag.clone();
            tag.find('a').attr('href', FeelStatic.baseURL + 'tag/' + listTags[i].tag);
            tag.find('a').text(listTags[i].name);
            tag.appendTo(elementTags);
        }

        content.find('.readMore-container').addClass('hide')
            .find('.cmt-container').html('');
        content.find('.gradient').show()
            .find('.btn-readMore').attr('data-post_id', postId)
            .attr('data-load-comment', 'true');

        return idContainer;
    };

    ViewPage.renderPosts = function (listVideo) {
        var fn;
        var	minPrevPost	= 1,
            minNextPost	= 2;

        //Iphone 6 and over
        if (window.screen.height >= 667) {
            //minPrevPost = 1;
            //minNextPost = 4;
        }

        if (!Main.isMobile.any()) {
            minPrevPost = 5;
            minNextPost = 10;
        }

        var numElems	= minPrevPost + minNextPost + 1;

        if (ViewPage.renderingPost) {
            return;
        }
        ViewPage.renderingPost = true;

        (fn = function() {
            if (ViewPage.isScrolling) {
                setTimeout(fn, 100);
                return;
            }

            var postElements		= $('#view-page .container .main-content .post'),
                mainContent			= $('#view-page .container .main-content');

            //Keep at least 1 post above current post
            while (ViewPage.minRenderedVidIdx > ViewPage.currentIndex - minPrevPost && ViewPage.minRenderedVidIdx > 0) {
                var idx = --ViewPage.minRenderedVidIdx;

                var lastPost        = $('#view-page .container .main-content .post').last();
                var post 			= postElements.length < numElems ? $('#post-sample').clone().css('display', 'block').addClass('post').attr('id', '') : lastPost;
                post.attr('video-index', idx);

                var idContainer = ViewPage.renderPostDetails(post, listVideo[idx], idx),
                    urlThumbnail = '',
                //urlLogo = FeelStatic.baseURL + 'resources/logo-84x30.png',
                    warningType = listVideo[idx].warning_type;

                if (warningType) {
                    urlThumbnail = FeelStatic.templateUrl + 'Public/images/img-warning-740x416.jpg';
                } else {
                    urlThumbnail = FeelStatic.baseURL + listVideo[idx].picture;
                }

                post.prependTo(mainContent);

                if (post[0] == postElements.last()[0]) {
                    //console.log('Move last post to first to be ' + listVideo[idx].id);
                    ViewPage.maxRenderedVidIdx--;
                    $(window).scrollTop($(window).scrollTop() + post.outerHeight(true));
                }

                if (Main.playWithVideoJS()) {
                    post[0].player.dispose();
                    post[0].player = VideoManager.initPlayer({
                        container   : idContainer,
                        url         : listVideo[idx].hls,
                        picture     : urlThumbnail,
                        warningType : listVideo[idx].warning_type,
                        seo_id      : listVideo[idx].seo_id
                    });

                } else {
                    ViewPage.playerVideo(postId, idContainer, sourceList, urlThumbnail, urlLogo, false, warningType);
                }
            }

            //Keep at least 2 post below current post
            while (ViewPage.maxRenderedVidIdx < ViewPage.videoList.length && ViewPage.maxRenderedVidIdx <= ViewPage.currentIndex + minNextPost) {
                var idx = ViewPage.maxRenderedVidIdx++;

                var firstPost       = $('#view-page .container .main-content .post').first();
                var post 			= postElements.length < numElems ? $('#post-sample').clone().css('display', 'block').addClass('post').attr('id', '') : firstPost;
                post.attr('video-index', idx);

                if (post[0] == firstPost[0]) {
                    //console.log('Move first post to last to be ' + listVideo[idx].id);
                    ViewPage.minRenderedVidIdx++;
                    //var dummyHeight = parseInt(mainContent.css('padding-top'));
                    //mainContent.css('padding-top', dummyHeight + post.outerHeight(true) + 'px');
                    $(window).scrollTop($(window).scrollTop() - post.outerHeight(true));
                }

                var idContainer = ViewPage.renderPostDetails(post, listVideo[idx], idx),
                    urlThumbnail = '';
                //urlLogo = FeelStatic.baseURL + 'resources/logo-84x30.png',
                if (listVideo[idx].warning_type != 0) {
                    urlThumbnail = FeelStatic.templateUrl + 'Public/images/img-warning-740x416.jpg';
                } else {
                    urlThumbnail = FeelStatic.baseURL + listVideo[idx].picture;
                }

                post.appendTo(mainContent);

                //var ads = moreVideos.find(".ads.ads-for-viewVideo");
                //if (ViewPage.videoIndex == 2) {
                //    ads.show();
                //    ads.appendTo(moreVideos);
                //}

                ViewPage.showVoucher();

                if (Main.playWithVideoJS()) {
                    if (post[0].player) {
                        post[0].player.dispose();
                        post[0].player = VideoManager.initPlayer({
                            container   : idContainer,
                            url         : listVideo[idx].hls,
                            picture     : urlThumbnail,
                            warningType : listVideo[idx].warning_type,
                            seo_id: listVideo[idx].seo_id
                        });
                    }
                    else {
                        post[0].player = VideoManager.initPlayer({
                            container: idContainer,
                            url: listVideo[idx].hls,
                            picture: urlThumbnail,
                            warningType : listVideo[idx].warning_type,
                            seo_id: listVideo[idx].seo_id
                        });
                    }
                } else {
                    ViewPage.playerVideo(postId, idContainer, sourceList, urlThumbnail, urlLogo, false, warningType);
                }

                //Trigger scroll event to update some position of some element because the size of window has been changed
                $(window).scroll();
            }

            ViewPage.renderingPost = false;
        })();
    };

    ViewPage.showVoucher = function() {
        return; //stop voucher event
        if (ViewPage.invalidToShowVoucher()) {
            return;
        }

        var mainContent	= $('#view-page .container .main-content');
        $('#referal-container').appendTo(mainContent).show();
        Main.hasShownVoucher = true;
    };

    ViewPage.invalidToShowVoucher = function() {
        return !Main.isLoggedIn() || Main.hasShownVoucher || ViewPage.getVoucherNumber() || ViewPage.getNumberOfViewedVideos() < 2 || ViewPage.hasIgnoredVoucher();
    };

    ViewPage.getVoucherNumber = function() {
        var voucherNo = FeelStatic.getCookie('feel_voucher');
        if (voucherNo) {
            return voucherNo;
        }

        return false;
    };

    ViewPage.increaseViewedNumber = function() {
        var num = FeelStatic.getCookie('feel_viewed_videos');
        if (!num) {
            num = 0;
        }
        num = parseInt(num) + 1;
        FeelStatic.setCookie('feel_viewed_videos', num, 1);
    };

    ViewPage.getNumberOfViewedVideos = function() {
        var num = FeelStatic.getCookie('feel_viewed_videos');
        if (!num) {
            return 0;
        }
        return parseInt(num);
    };

    ViewPage.requestVoucher = function(button) {
        if (ViewPage.isGettingVoucher || ViewPage.getVoucherNumber()) {
            return;
        }
        ViewPage.isGettingVoucher = true;
        button = $(button);
        button.prop('disabled', true);
        button.html('Đang tải...');

        var request = $.ajax({
            url: FeelStatic.baseURL + 'post/getVoucher',
            dataType: 'json'
        });

        request.done(function(data) {
            if (data.error_message) {
                Main.notification(data.error_message);
            } else {
                Main.notification('<p>Chúc mừng bạn đã nhận được 1 voucher</p><h1 style="font-size: 2em;">' + data.voucher + '</h1>', 400);
            }
            ViewPage.isGettingVoucher = false;
            button.prop('disabled', false);
            button.html('Nhận voucher');
            $('#referal-container').fadeOut(300);
        });
    };

    ViewPage.ignoreVoucher = function(button) {
        if (ViewPage.isGettingVoucher) {
            return;
        }

        $('#referal-container').fadeOut(300);
        FeelStatic.setCookie('feel_voucher_ignore', true, 7);
    };

    ViewPage.hasIgnoredVoucher = function() {
        return FeelStatic.getCookie('feel_voucher_ignore');
    };

    ViewPage.viewVoucherDetail = function() {
        var html = '';
        html += '<p>Tặng 1 ly Heineken miễn phí trị giá 75000đ<p>';
        html += '<p>Tại <b>Saigon Acoustic - 104 Hai Bà Trưng, Q1, HCM</b><p>';
        html += '<p>Áp dụng trước ngày <b>31/12/2016</b><p>';
        html += '<p>Thời gian mở cửa <b>6pm - 11:30pm từ Thứ 4 - Chủ nhật</b><p>';

        Main.notification(html, 500);
    };

    ViewPage.getVideo = function (callback) {
        var loading = $('#loading-container .loading');

        ViewPage.isLoading = true;
        loading.fadeIn(500);

        var request = $.ajax({
            url: FeelStatic.baseURL + 'post/next/',
            method: 'POST',
            dataType: 'json',
            data: {
                id: ViewPage.currentId
            },
            statusCode: {
                403: Main.showCaptchaInput
            }
        });

        request.done(function(data) {
            data = data.newPosts;

            if (data.length == 0) {
                ViewPage.isContinue = false;
            }

            for (var i = 0; i < data.length; i++) {
                ViewPage.videoList.push(data[i]);
            }
            ViewPage.isLoading = false;
            //loading.hide();

            //Call the callback function
            if (callback) {
                callback();
            }
        });
    };

    ViewPage.checkQualityVideo = function (player) {
        if (jwp.sources.length == 1) {
            return;
        }

        var hContainer = jwp.getHeight();
        //Main.checkNetwork();

        if (jwp.sources.length == 2) {
            if (Main.speedNetwork >= 750 && hContainer > 240) {
                ViewPage.videoQuality = 1;
            } else {
                ViewPage.videoQuality = 2;
            }
            return;
        }

        if (jwp.sources.length == 3) {
            if (Main.speedNetwork >= 1500 && hContainer > 480) {
                ViewPage.videoQuality = 0;
            } else if (Main.speedNetwork >= 750 && hContainer > 240) {
                ViewPage.videoQuality = 1;
            } else {
                ViewPage.videoQuality = 2;
            }
        }
    };

    ViewPage.sloganScroll = function (slogan) {
        slogan.addClass('active');
    };

    ViewPage.pushState = function(obj) {
        var fn, //Must define fn before declaring because it is recursive function
            fn = function() {
                if (ViewPage.isScrolling) {
                    ViewPage.PushStateTimer = setTimeout(fn, 1000);
                    return;
                }

                FeelStatic.changeUrlWithoutReload(obj, obj.title, FeelStatic.baseURL + 'post/' + obj.seo_id);
                document.title = obj.title + ' | Thật vi diệu';
                ga('send', {
                    hitType: 'pageview',
                    page: location.pathname
                });
            };
        if (ViewPage.PushStateTimer) {
            clearTimeout(ViewPage.PushStateTimer);
        }
        //Execute it
        fn();
    };

    ViewPage.inVisibleArea = function (item) {
        var h = $(item).children().first(),
            positionStart = h.offset().top + h.height() / 3,
            positionEnd = h.offset().top + h.height() * 2 / 3,
            checkPlay1 = positionStart >= $(window).scrollTop(),
            checkPlay2 = positionEnd <= $(window).scrollTop() + $(window).height();

        return checkPlay1 && checkPlay2;
    };

    ViewPage.nextVideo = function () {
        if (ViewPage.currentIndex >= ViewPage.videoList.length) {
            return
        }
        var next = $(ViewPage.activeVideo).next();
        $('html, body').animate({scrollTop: next.offset().top - 50}, 300);
    };

    ViewPage.selectReportVideoItem = function(btn, other) {
        btn = $(btn);
        var txt = $('#modal-form').find('.report-reason');

        if (other == 'other') {
            txt.val('').show();
        } else {
            txt.hide();
            txt.val(btn.find('span').html());
        }
    };

    ViewPage.reportVideoId = false;
    ViewPage.showReportContainer = function(btn) {
        btn = $(btn);
        ViewPage.reportVideoId = btn.attr('data-text');
        var reportDiv = $('#view-page-report-video');
        var btns = [
            {
                text: 'Gửi report',
                handler: ViewPage.reportVideoByClick
            }
        ];

        Main.showModalForm('', reportDiv.html(), btns);
    };

    ViewPage.reportVideoByClick = function() {
        var txt = $('#modal-form').find('.report-reason');
        if (txt.val() == '') {
            alert('Vui lòng cho thatvidieu.com biết lý do nhé');
            return;
        }

        ViewPage.reportVideo(ViewPage.reportVideoId, txt.val());
        Main.hideModalForm();
        ViewPage.reportVideoId = false;
    };

    ViewPage.btnShare = function (btn) {
        btn = $(btn);
        btn.parent().find('.social').toggleClass('active');

    };

    ViewPage.shareSocial = function(btn, type) {
        var prefix = '';

        if (type == 'facebook') {
            prefix = 'https://www.facebook.com/sharer.php?u=';
        } else if (type == 'google') {
            prefix = 'https://plus.google.com/share?url=';
        } else {
            prefix = 'https://twitter.com/share?url=';
        }

        var url =  prefix + window.location.protocol + FeelStatic.baseURL + $(btn).attr('data-short-link'),
            title = $(btn).attr('data-title');

        window.open(url, title, 'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
    };

    ViewPage.copyToClipboard = function(btn) {
        var shorLink = window.location.protocol + FeelStatic.baseURL + $(btn).attr('data-short-link'),
            $temp = $("<input>");
        $("body").append($temp);
        $temp.val(shorLink).select();
        document.execCommand("copy");
        $temp.remove();

        Main.notification("Đã sao chép liên kết rút gọn:<br><b>"+ shorLink + "</b>");
    };

    ViewPage.viewMoreInfo = function (btn) {
        btn = $(btn);
        var gradient = btn.parent(),
            postContent = gradient.parent().find('.readMore-container');
        gradient.hide();
        postContent.removeClass('hide');

        if (btn.attr('data-load-comment') == 'true') {
            var postId = btn.attr('data-post_id');
            var request = $.ajax({
                url: FeelStatic.baseURL + 'post/getComment',
                method: 'GET',
                dataType: 'json',
                data: {
                    post: postId,
                    page: 0
                }
            });

            var readMore = btn.parent().parent().find('.readMore-container'),
                container = readMore.find('.cmt-container'),
                noCmt = readMore.find('.cmt-default .no-cmt'),
                cmtDefault = readMore.find('.cmt-default .cmt');
            request.done(function(data) {
                if (data.length == 0) {
                    noCmt.appendTo(container);
                } else {
                    for (var i = 0; i < data.length; i++) {
                        var cmt = cmtDefault.clone();

                        var supCmt = cmt.find('.sup-cmt');
                        supCmt.find('.avt').css('background-image', 'url(' + data[i].user.avatar + ')');

                        var supContent = supCmt.find('.cmt-content');
                        supContent.find('.username').text(data[i].user.name);
                        supContent.find('span').html(data[i].content);

                        var supLike = supCmt.find('.like');
                        supLike.find('.time').text(data[i].fantastic_time);

                        cmt.appendTo(container);
                    }
                }
            });
        }
    };

    ViewPage.focusCmt = function (item, flag) {
        if (!Main.isLoggedIn()) {
            Main.notification("Bạn cần đăng nhập để thực hiện thao tác này")
            return
        }
        item = $(item);
        var btnSubmit = item.parent().find('.btn-submit');
        if (flag) {
            btnSubmit.show();
        }
        else {
            setTimeout(function() {
                btnSubmit.hide();
            }, 100);
        }
    };

    ViewPage.addComment = function(button) {
        if (!Main.isLoggedIn()) {
            Main.notification("Bạn cần đăng nhập để thực hiện thao tác này");
            return
        }
        button = $(button);
        var txt = button.parent().find('.txt-cmt');
        if (txt.val().length < 5) {
            Main.notification("Comment ít nhất 5 ký tự nhé");
            return
        }

        var form = button.parent();
        var defaultItem = form.find('.cmt');
        var item = defaultItem.clone();
        var container = form.parent().find('.cmt-container');
        container.find('.no-cmt').hide();
        var profile = Main.getProfile();

        item.find('.username').html(profile.username);
        item.find('.avt').css('background-image', 'url(' + profile.avatar + ')')
        item.find('.cmt-content > span').html(txt.val());
        item.find('.time').html('Đang gửi');
        item.prependTo(container).show();

        var request = $.ajax({
            url: FeelStatic.baseURL + 'post/comment',
            method: 'POST',
            dataType: 'json',
            data: {
                post: form.attr('data-text'),
                content: txt.val()
            },
            statusCode: {
                403: Main.showCaptchaInput
            }
        });
        button.prop('disabled', true);

        request.done(function(data) {
            if (data.error_message) {
                item.find('.time').html('Có lỗi xảy ra vui lòng thử lại sau');
            } else {
                item.find('.time').html('Vừa xong');
            }
            item.find('.cmt-content > span').html(data.content);
            button.prop('disabled', false);
            txt.val('');
        });
    };

    ViewPage.likeVideo = function(btn, isLike) {
        if (Main.isLoggedIn() === false) {
            Main.notification('Bạn cần đăng nhập để thực hiện thao tác này.');
            return;
        }
        btn = $(btn);
        var parent = btn.parent();
        var postId = parent.attr('data-text');

        var btnLike = parent.find('a.btn-like');
        var btnDislike = parent.find('a.btn-dislike');
        if (btn.hasClass('btn-like')) {
            btnDislike.removeClass('active');
        } else {
            btnLike.removeClass('active');
        }
        btn.toggleClass('active');

        var request = $.ajax({
            url: FeelStatic.baseURL + 'post/like',
            method: "POST",
            dataType: 'json',
            data: {
                post_id: postId,
                like: isLike
            },
            statusCode: {
                403: Main.showCaptchaInput
            }
        });

        request.done(function(data) {
            if (data.error_message) {
                Main.notification('Bạn cần đăng nhập <br> để thực hiện thao tác này.');
                btn.toggleClass('active');
            } else {
                btnDislike.find('.number').text(data.format_dislikes);
                btnLike.find('.number').text(data.format_likes);
            }
        });
    };

    ViewPage.checkLike = function(postId) {
        var request = $.ajax({
            url: FeelStatic.baseURL + 'post/checkLike?id=' + postId,
            method: 'GET',
            dataType: 'json'
        });

        request.done(function(data) {
            if (data.like === true) {
                $('#like-' + postId).find('.btn-like').addClass('active');
            } else if (data.dislike == true) {
                $('#like-' + postId).find('.btn-dislike').addClass('active');
            }
        });
    }

    ViewPage.playerVideo = function (postId, idContainer, sourceList, linkPoster, linkLogo, autoPlay, warningType) {
        $('#' + idContainer).html('');

        var widthContainer = $('#' + idContainer).width(),
            screen         = '';
        if (Main.aspectRatio == '16:9') {
            screen = 9 / 16;
        } else {
            screen = 3 / 4;
        }
        $('#' + idContainer).height(widthContainer * screen);

        var slogan = $('#view-page .container .slogan');

        var lastScrollTop = -100;
        $(window).bind('scroll', function() {
            if (slogan.hasClass('active') && Math.abs($(window).scrollTop() - lastScrollTop) > 10) {
                lastScrollTop = $(window).scrollTop();
                var positionFooter = $(document).height() - $('#footer').height(),
                    flagScroll = $(this).scrollTop() + $(this).height() + 300 > positionFooter;

                if (flagScroll) {
                    slogan.removeClass('active');
                }
            }
        });

        var sources = [];
        for (var i = 0; i < sourceList.length; i++) {
            var item = {};
            item.file = FeelStatic.baseURL + sourceList[i].url;
            item.label = sourceList[i].size + 'p';
            sources.push(item);
        }

        // *** Don't delete this code
        // var sources = [{
        //     file: FeelStatic.baseURL + sourceList[0].url,
        //     label: '720p'
        //     }, {
        //     file: FeelStatic.baseURL + sourceList[0].url,
        //     label: '480p'
        //     }, {
        //     file: FeelStatic.baseURL + sourceList[0].url,
        //     label: '240p'
        //     }
        // ];

        var sourcesIntro = [{
            file: FeelStatic.baseURL + 'resources/video-intro-HD.mp4',
            label: '720p'
        }, {
            file: FeelStatic.baseURL + 'resources/video-intro-SD.mp4',
            label: '480p'
        }
        ];

        var playlist = [],
            checkRefferer = document.referrer.indexOf(FeelStatic.baseURL) < 0,
            hasViewedIntroToday = FeelStatic.getCookie('feel_viewed_intro') != "",
            ableToPlayIntro = (checkRefferer || Main.getPartner())  && !ViewPage.hasVideoIntro && !hasViewedIntroToday;
        if (ableToPlayIntro) {
            playlist = [{image: linkPoster, sources: sourcesIntro}, {image: linkPoster, sources: sources}];
            ViewPage.hasVideoIntro = true;
            FeelStatic.setCookie('feel_viewed_intro', 'true', Main.getPartner() ? 0.15 : 1);
        } else {
            playlist = [{image: linkPoster, sources: sources}];
        }

        var jwp = jwplayer(idContainer).setup({
            playlist: playlist,
            width: '100%',
            aspectratio: Main.aspectRatio,
            autostart: autoPlay,
            skin: {
                name: 'glow',
                active: '#009dff'
            },
            logo: {
                file: linkLogo,
                margin: '15'
            }
        });

        jwp.postId = postId;
        jwp.trackId = false;
        jwp.isTracking = false;
        jwp.lastTrackedPosition = 0;
        jwp.checkedQuality = false;
        jwp.warningType = warningType;

        jwp.on('ready', function() {
            var video = $(this.getContainer()).find('video');
            video.bind('contextmenu', function(e) {e.stopPropagation(); e.preventDefault();});
            video.attr('playsinline', '');

            var containerVideo = $(this.getContainer());
            containerVideo.mouseover(function () {
                containerVideo.removeClass('jw-flag-user-inactive');
            });
            containerVideo.mouseout(function () {
                containerVideo.addClass('jw-flag-user-inactive');
            });
            containerVideo.focus(function () {
                containerVideo.css('outline', 'none');
            });

            var controls = $(this.getContainer()).find('.jw-controls');
            controls.find('.jw-display-icon-container.jw-background-color').css({
                'background-color': 'rgba(0, 0, 0, 0.6)',
                'border-radius': '50%'
            });

            var controlBar = $(this.getContainer()).find('.jw-controlbar');
            controlBar.css({
                'background': '-webkit-linear-gradient(transparent, rgba(0, 0, 0, 1))',
                'background': 'linear-gradient(transparent, rgba(0, 0, 0, 1))'
            });
            controlBar.find('.jw-icon-prev, .jw-icon-playlist, .jw-icon-next').css('display', 'none');

            ViewPage.playerList.push(jwp);
        });

        jwp.sources = sources;
        jwp.hasAcceptedWarning = false;

        jwp.on('play', function () {
            if (FeelStatic.getCookie('quality')) {
                //console.log('Set', parseInt(FeelStatic.getCookie('quality')) - (3 - sources.length));
                jwp.setCurrentQuality(parseInt(FeelStatic.getCookie('quality')) - (3 - sources.length));
            }
            else if (!jwp.checkedQuality) {
                ViewPage.checkQualityVideo(jwp);
                jwp.setCurrentQuality(ViewPage.videoQuality - (3 - sources.length));
                jwp.checkedQuality = true;
            }

            slogan.removeClass('active');

            var iconHD = $(this.getContainer()).find('.jw-icon-hd');
            switch (jwp.getCurrentQuality() + (3 - jwp.sources.length)) {
                case 0:
                    iconHD.css('color', '#e80800');
                    break;
                case 1:
                    iconHD.css('color', '#ff8f8b');
                    break;
                default:
                    iconHD.css('color', '#ffffff');
                    break;
            }

            if (FeelStatic.getCookie('volume')) {
                if (FeelStatic.getCookie('volume') > 0) {
                    jwp.setMute(false);
                    jwp.setVolume(FeelStatic.getCookie('volume'));
                } else {
                    jwp.setMute(true);
                }
            } else {
                jwp.setVolume(ViewPage.volumeDefault);
            }

            if (!jwp.hasAcceptedWarning) {
                ViewPage.checkWarning(jwp);
            }
        });

        jwp.on('pause', function () {
            ViewPage.trackingVideo(this);
            if (jwp.hasAcceptedWarning && ViewPage.hasVideoIntro) {
                ViewPage.sloganScroll(slogan);
            }
        });

        jwp.on('time', function () {
            if (jwp.getDuration() - jwp.getPosition() <= 10 && ViewPage.hasVideoIntro) {
                ViewPage.sloganScroll(slogan);
            }
            if (jwp.getPosition() - jwp.position > ViewPage.timeTracking && jwp.trackId === false && jwp.isTracking === false) {
                ViewPage.trackingVideo(this);
            }
        });

        jwp.on('seek', function (obj) {
            jwp.position = obj.offset;
        });

        jwp.on('complete', function () {
            ViewPage.trackingVideo(this);
        });

        jwp.on('fullscreen', function () {
            // jwp.position = jwp.getPosition();
            // ViewPage.checkQualityVideo(jwp);
            // jwp.setCurrentQuality(ViewPage.videoQuality);
            // jwp.load();
            // jwp.seek(jwp.position);
        });

        jwp.on('volume', function () {
            jwp.setMute(false);
            FeelStatic.setCookie('volume', jwp.getVolume(), 1);
        });

        jwp.on('mute', function () {
            FeelStatic.setCookie('volume', 0, 1);
        });

        jwp.on('levelsChanged', function(e) {
            FeelStatic.setCookie('quality', e.currentQuality + (3 - jwp.sources.length), 0);
            if (e.currentQuality + (3 - jwp.sources.length) > 1 && /(android)/i.test(navigator.userAgent)) {//240p
                $(this.getContainer()).addClass('hide-buffering-spinner');
            }
        });

        jwp.position = 0;
    };

    ViewPage.autoPlayVideo = function () {
        var t; //Timer use in changing page title, url and track ga
        $(window).bind('scroll', function () {
            if (ViewPage.currentIndex == -1) {
                var posts = $('#view-page .container .main-content .post');
                posts.each(function (index, item) {
                    if (index < ViewPage.playerList.length && ViewPage.checkPlayVideo(item)) {
                        if (!Main.isMobile.iOS() || Main.isNativeApp()) {
                            ViewPage.playerList[index].play();
                        }
                        ViewPage.currentIndex 	= index;
                        ViewPage.activeVideo 	= item;
                        return false;
                    }
                });
            }
            else {
                var curScrollTop 	= $(window).scrollTop(),
                    item			= ViewPage.activeVideo,
                    index			= ViewPage.currentIndex;

                if (Math.abs(curScrollTop - ViewPage.lastScrollTop) < 20) {
                    return;
                }

                while(item && index < ViewPage.playerList.length) {
                    if (ViewPage.checkPlayVideo(item)) {
                        if (ViewPage.currentIndex != index
                            && !ViewPage.playerList[ViewPage.currentIndex].getFullscreen()
                            && !ViewPage.checkPlayVideo(ViewPage.activeVideo)
                        ) {
                            ViewPage.trackingVideo(ViewPage.playerList[ViewPage.currentIndex]);
                            ViewPage.playerList[ViewPage.currentIndex].position = ViewPage.playerList[ViewPage.currentIndex].getPosition();
                            ViewPage.playerList[ViewPage.currentIndex].checkedQuality = false;
                            ViewPage.playerList[ViewPage.currentIndex].stop();

                            if (!Main.isMobile.iOS() || Main.isNativeApp()) {
                                if (ViewPage.playerList[index].position > 0) {
                                    ViewPage.playerList[index].seek(ViewPage.playerList[index].position);
                                } else {
                                    ViewPage.playerList[index].play();
                                }
                            }

                            var obj = ViewPage.videoList[index];
                            ViewPage.pushState(obj);

                            ViewPage.currentIndex 	= index;
                            ViewPage.activeVideo	= item;
                        }

                        return;
                    }

                    if (curScrollTop < ViewPage.lastScrollTop) {
                        do {
                            //Find the previous video
                            //Put it in loop because between 2 <div> tags has a space
                            item = item.previousSibling;
                        }
                        while(item && (item.tagName != 'DIV' || (' ' + item.className + ' ').indexOf(' post ') < 0))
                        index--;
                    }
                    else {
                        do {
                            //Find the next video
                            //Put it in loop because between 2 <div> tags has a space
                            item = item.nextSibling;
                        }
                        while(item && (item.tagName != 'DIV' || (' ' + item.className + ' ').indexOf(' post ') < 0));
                        index++;
                    }
                }

                ViewPage.lastScrollTop = curScrollTop;
            }
        });

        //Make mobile video become autoplay-able
        var autoplayableLastIndex = 0;
        $(window).bind('touchstart', function() {
            //The init script in touchstart event can help the first video start automatically on first touch
            if (ViewPage.currentIndex == -1) {
                var posts = $('#view-page .container .main-content .post');
                posts.each(function (index, item) {
                    if (index < ViewPage.playerList.length && ViewPage.checkPlayVideo(item)) {
                        if (!Main.isMobile.iOS() || Main.isNativeApp()) {
                            ViewPage.playerList[index].play();
                        }
                        ViewPage.currentIndex 	= index;
                        ViewPage.activeVideo 	= item;
                        return false;
                    }
                });
            }

            var count = ViewPage.playerList.length;
            for(var i = autoplayableLastIndex; i < count; i++) {
                if (ViewPage.playerList[i].getState() == 'idle') {
                    if (!Main.isMobile.iOS() || Main.isNativeApp()) {
                        ViewPage.playerList[i].play();
                        if (i != ViewPage.currentIndex) {
                            ViewPage.playerList[i].stop();
                        }
                    }
                }
            }

            autoplayableLastIndex = count;
        })
    };

    ViewPage.trackingVideo = function (jwp) {
        //if (jwp.isTracked == true) {
        //    return;
        //}
        var currentPosition = jwp.getPosition();

        var duration = currentPosition - jwp.position,
            isCompleted = (jwp.getState() == 'complete') ? 1 : 0;

        //prevent spam tracking
        if (currentPosition - jwp.lastTrackedPosition < 5 && !isCompleted) {
            return;
        }
        jwp.isTracking = true;
        jwp.lastTrackedPosition = currentPosition;

        if (duration > ViewPage.timeTracking) {
            var request = $.ajax({
                url: FeelStatic.baseURL + 'post/track',
                method: 'POST',
                data: {
                    post_id: jwp.postId,
                    duration: duration,
                    is_completed: isCompleted,
                    begin: jwp.position,
                    end: jwp.getPosition(),
                    trackId: jwp.trackId
                },
                dataType: 'json'
            });

            request.done(function(data) {
                jwp.isTracking = false;
                if (data.id != 0) {
                    jwp.trackId = data.id;
                }
            });
        } else {
            jwp.isTracking = false;
        }
    };

    ViewPage.checkWarning = function (jwp) {
        if (jwp.warningType == 0) {
            jwp.hasAcceptedWarning = true;
            return;
        }

        jwp.pause();
        jwp.controls(false);
        var warning = $(jwp.getContainer()).parent().find('.warning-container'),
            video = $(jwp.getContainer()).find('video');
        warning.show();
        video.css('-webkit-filter', 'blur(10px)');
        video.css('filter', 'blur(10px)');

        if (jwp.warningType == 1) {
            warning.find('.warning').text('Video có chứa nội dung nhạy cảm');
        }

        if (jwp.warningType == 2) {
            warning.find('.warning').text('Video có chứa nội dung gây sợ hãi');
        }

        var btnContinue = warning.find('.btn-continue');
        btnContinue.click(function (e) {
            e.preventDefault();
            jwp.play();
            jwp.controls(true);
            warning.hide();
            video.css('-webkit-filter', 'blur(0)');
            video.css('filter', 'blur(0)');
            jwp.hasAcceptedWarning = true;
        });
    };
})(window.ViewPage = window.ViewPage || {}, jQuery);

(function(VideoManager, $) {
    window.onerror = function(msg, url, linenumber) {
        console.log('Error message: '+msg+'\nURL: '+url+'\nLine Number: '+linenumber);
        return true;
    };

    VideoManager.ads = 'http://googleads.g.doubleclick.net/pagead/ads?ad_type=video_text_image_flash&client=ca-video-pub-4724742554474460&description_url=http%3A%2F%2Fthatvidieu.com&videoad_start_delay=0&hl=vi&max_ad_duration=30000';
    VideoManager.ads = VideoManager.ads + '&channel=' + Main.getPartnerChannelID();

    //VideoManager.ads = 'https://pubads.g.doubleclick.net/gampad/ads?sz=640x480&iu=/124319096/external/single_ad_samples&ciu_szs=300x250&impl=s&gdfp_req=1&env=vp&output=vast&unviewed_position_start=1&cust_params=deployment%3Ddevsite%26sample_ct%3Dskippablelinear&correlator=';
    VideoManager.adsProbability = Main.getAdsProbability();

    VideoManager.getVideoType = function(video){
        var videoTypes = {
            'webm' : 'video/webm',
            'mp4' : 'video/mp4',
            'ogv' : 'video/ogg',
            'm3u8': 'application/x-mpegURL'
        };
        var extension = video.split('.').pop();

        return videoTypes[extension] || '';
    };

    VideoManager.initPlaylist = function(url, thumbnail, disabledIntro) {
        var playlist = [],
            checkRefferer = document.referrer.indexOf(FeelStatic.baseURL) < 0,
            hasViewedIntroToday = FeelStatic.getCookie('feel_viewed_intro') != "",
            ableToPlayIntro = (checkRefferer || Main.getPartner()) && !ViewPage.hasVideoIntro && !hasViewedIntroToday;
        if (ableToPlayIntro && !disabledIntro) {
            playlist = [
                {
                    src: [FeelStatic.baseURL + 'resources/intro.m3u8'],
                    poster: thumbnail,
                    title: '',
                    controls: false,
                    track: false
                },
                {
                    src: [FeelStatic.baseURL + url],
                    poster: thumbnail,
                    title: '',
                    controls: true,
                    track: true
                }
            ];

            ViewPage.hasVideoIntro = true;
            FeelStatic.setCookie('feel_viewed_intro', 'true', Main.isFromWFC() ? 0.15 : 1);
        } else {
            playlist = [{
                src: [FeelStatic.baseURL + url],
                poster: thumbnail,
                title: '',
                controls: true,
                track: true
            }];
        }

        return playlist;
    };

    VideoManager.initPlayer = function(options) {
        var container = $('#' + options.container);
        container.html('');

        var containerWidth = container.width(),
            screen = '';

        if (Main.aspectRatio == '16:9') {
            screen = 9 / 16;
        } else {
            screen = 3 / 4;
        }

        container.height(containerWidth * screen);

        var slogan = $('#view-page .container .slogan');

        var lastScrollTop = -100;
        $(window).bind('scroll', function() {
            if (slogan.hasClass('active') && Math.abs($(window).scrollTop() - lastScrollTop) > 10) {
                lastScrollTop = $(window).scrollTop();
                var positionFooter = $(document).height() - $('#footer').height(),
                    flagScroll = $(this).scrollTop() + $(this).height() + 300 > positionFooter;

                if (flagScroll) {
                    slogan.removeClass('active');
                }
            }
        });

        //Init play list
        var playlist = VideoManager.initPlaylist(options.url, options.picture);

        var playerOptions = {
            id: options.container,
            width: container.width(),
            height: container.height(),
            ads: VideoManager.ads,
            videos: playlist,
            warningType: options.warningType,
            seo_id: options.seo_id
        };

        var player = VideoManager.initVideoJS(playerOptions);

        player.on('ready', function() {
            VideoManager.checkWarning(player);
        });

        player.on('next', function(e, src) {
            //this.updateSrc(src[0].src[0], {hls: true});
        });

        player.on('play', function () {
            slogan.removeClass('active');

            VideoManager.checkWarning(player);

            //Pause other player
            var posts = $('#view-page .container .main-content .post').each(function() {
                if (this.player != player) {
                    if (this.player.paused() === false) {
                        this.player.pause();
                    }
                }
            });
        });

        player.on('pause', function () {
            if (player.pl.currentVideo.track && player.currentTime() > 1) {
                if (player.hasAcceptedWarning) {
                    ViewPage.sloganScroll(slogan);
                }
            }
            VideoManager.trackVideo(this, true);
        });

        player.on('timeupdate', function () {
            if (player.currentTime() > 1 && player.remainingTime() <= 10 && player.pl.currentVideo.track) {
                ViewPage.sloganScroll(slogan);
            }
            VideoManager.trackVideo(this);
        });

        player.on('seek', function (obj) {
            player.position = obj.offset;
        });

        player.on('lastVideoEnded', function () {
            VideoManager.trackVideo(this, true);
        });

        player.on('fullscreenchange', function() {
            $(window).scroll(); //don't remove, it's trigger for restore sidebar when toggling fullscreen
        });

        player.position = 0;

        return player;
    };

    VideoManager.initVideoJS = function(options) {
        var placeholder = document.getElementById(options.id);
        var dumbPlayer = document.createElement('video');
        dumbPlayer.id = options.id;
        dumbPlayer.className = 'video-js vjs-default-skin vjs-big-play-centered';
        dumbPlayer.setAttribute('preload', 'none');
        dumbPlayer.setAttribute('controls', 'true');
        dumbPlayer.setAttribute('webkit-playsinline', 'true');
        dumbPlayer.setAttribute('playsinline', 'true');

        //var contentSrc = document.createElement('source');
        //contentSrc.setAttribute('src', options.videos[options.videos.length - 1].src[0]);
        //contentSrc.setAttribute('type', VideoManager.getVideoType(options.videos[options.videos.length - 1].src[0]));
        //dumbPlayer.appendChild(contentSrc);

        placeholder.parentNode.appendChild(dumbPlayer);
        placeholder.parentNode.removeChild(placeholder);

        var playerOptions = {
            width: options.width,
            height: options.height,
            plugins: {
                videoJsResolutionSwitcher: {
                    default: 480,
                    dynamicLabel: true
                }
            }
        };
        var player = videojs(options.id, playerOptions);

        player.hasAcceptedWarning = options.warningType == 0;
        player.warningType = options.warningType;

        //Calculate ads probabylity
        player.ima.adsDisabled = true; //Math.random() > VideoManager.adsProbability;

        //Init google IMA
        if (player.ima && player.ads && !player.ima.adsDisabled) {
            player.ima({
                id: options.id,
                adTagUrl: this.ads,
                nativeControlsForTouch: true
            }, function() {
                player.ima.addEventListener(google.ima.AdEvent.Type.STARTED, function(e) {
                    $(player.ima.adContainerDiv).addClass('started');
                    if (!player.hasAcceptedWarning) {
                        player.ima.pauseAd();
                    }
                });
                player.ima.addEventListener(google.ima.AdEvent.Type.RESUMED, function(e) {
                    if (!player.hasAcceptedWarning) {
                        player.ima.pauseAd();
                    }
                });
                player.ima.addEventListener(google.ima.AdEvent.Type.LOADED, function(e) {
                    //Does not load ads twice per video
                    player.ima.adsDisabled = true;
                });

                player.ima.startFromReadyCallback();
            });
        } else {
            player.ima.adsDisabled = true;
        }


        //Init google IMA container, this must be call in a user action due to restriction of mobile devices
        if (!player.ima.adsDisabled) {
            var fnInitAdsContainer = null;
            if (Main.isMobile.any()) {
                $(window).bind('touchstart', fnInitAdsContainer = function() {
                    if (!player.ima.adDisplayContainerInitialized) {
                        player.ima.initializeAdDisplayContainer();
                        $(window).unbind('touchstart', fnInitAdsContainer);
                    }
                });
            }
            else {
                player.ima.initializeAdDisplayContainer();
            }
        }

        player.logobrand({
            image: FeelStatic.baseURL + "resources/events/video_player_logo_for_tet.png",
            //image: FeelStatic.baseURL + "resources/video_player_logo.png",
            destination: FeelStatic.baseURL + 'post/' + options.seo_id //destination when clicked
        });

        player.playList(options.videos);

        return player;
    };

    VideoManager.autoPlayVideo = function () {
        var t; //Timer use in changing page title, url and track ga
        var lastScrollTop       = -50;
        var lastVisibleItem     = null;
        $(window).bind('scroll', function () {
            if (ViewPage.currentPlayer && ViewPage.currentPlayer.isFullscreen()) {
                return;
            }
            if (Math.abs($(this).scrollTop() - lastScrollTop) > 30) {
                lastScrollTop = $(this).scrollTop();

                var posts = $('#view-page .container .main-content .post');
                posts.each(function (idx, item) {
                    var index = parseInt($(item).attr('video-index'));
                    if (ViewPage.inVisibleArea(item)) {
                        //if (item != lastVisibleItem) {
                            lastVisibleItem = item;

                            var player = item.player;

                            if (!player.ima.adsDisabled && !player.ima.adsActive && player.ima.adDisplayContainerInitialized) {
                                player.ima.requestAds();
                            }
                            if (!Main.isMobile.iOS() || Main.isNativeApp() || Main.isMobile.iOSVersion() >= 10) {
                                if (player.hasAcceptedWarning) {
                                    if (player.ima.adsActive) {
                                        player.ima.resumeAd();
                                    }
                                    if (!player.ima.adsActive || !player.ima.currentAd.isLinear()) {
                                        player.play();
                                    }
                                }
                            }

                            var obj = ViewPage.videoList[index];
                            ViewPage.pushState(obj);

                            ViewPage.currentIndex = index;
                            ViewPage.activeVideo = item;
                            ViewPage.currentPlayer = player;
                        //}
                    }
                    else {
                        var player = item.player;

                        if (player.ima.adsActive && player.ima.adPlaying) {
                            player.ima.pauseAd();
                        }
                        if (player.paused() === false) {
                            //player.pause();
                            player.pl.stop();
                        }
                    }
                });
            }
        });

        //Make mobile video become autoplay-able
        var autoplayableLastIndex = 0;
        $(window).bind('touchstart', function() {
            if (VideoManager.isFullscreen) {
                return;
            }
            //The init script in touchstart event can help the first video start automatically on first touch
            var posts = $('#view-page .container .main-content .post');
            posts.each(function (idx, item) {
                var index = parseInt($(item).attr('video-index'));
                var player = item.player;

                if (ViewPage.inVisibleArea(item)) {
                    if (!player.ima.adsActive && !player.ima.adsDisabled) {
                        if (!player.ima.adDisplayContainerInitialized) {
                            player.ima.initializeAdDisplayContainer();
                        }
                        player.ima.requestAds();
                    }
                    if (!Main.isMobile.iOS() || Main.isNativeApp() || Main.isMobile.iOSVersion() >= 10) {
                        if (!player.ima.adsActive) {
                            if (player.hasAcceptedWarning) {
                                player.play();
                            }
                        }
                    }
                    ViewPage.currentIndex 	= index;
                    ViewPage.activeVideo 	= item;
                }
                else if (!player.autoplayable) {
                    player.play();
                    player.pl.stop();
                    if (!player.ima.adDisplayContainerInitialized) {
                        player.ima.initializeAdDisplayContainer();
                    }
                }

                player.autoplayable = true;
            });
        })
    };

    VideoManager.trackVideo = function (player, force) {
        //Prevent tracking intro video
        if (!player.pl.currentVideo.track) {
            return;
        }
        var currentVideo = ViewPage.videoList[ViewPage.currentIndex];
        if (currentVideo.isTracking) {
            return;
        }

        var currentPosition = player.currentTime();

        var duration = currentPosition - player.position,
            isCompleted = (player.ended()) ? 1 : 0;

        //prevent spam tracking
        if (currentPosition - currentVideo.lastTrackedPosition < 10 && !force) {
            return;
        }
        currentVideo.isTracking = true;
        currentVideo.lastTrackedPosition = currentPosition;

        if (duration > ViewPage.timeTracking || isCompleted) {
            if (!currentVideo.tracked && Main.isLoggedIn()) {
                ViewPage.increaseViewedNumber();
            }

            var request = $.ajax({
                url: FeelStatic.baseURL + 'post/track',
                method: 'POST',
                data: {
                    post_id: currentVideo.id,
                    duration: duration,
                    is_completed: isCompleted,
                    begin: player.position,
                    end: player.currentTime(),
                    trackId: player.trackId
                },
                dataType: 'json',
                timeout: 10000
            });

            request.done(function(data) {
                currentVideo.tracked = true;
                currentVideo.isTracking = false;
                if (data.id != 0) {
                    player.trackId = data.id;
                }
            });

            request.error(function() {
                currentVideo.isTracking = false;
            });
        } else {
            currentVideo.isTracking = false;
        }

        ViewPage.videoList[ViewPage.currentIndex] = currentVideo;
    };

    VideoManager.checkWarning = function (player) {
        //var currentVideo = ViewPage.videoList[ViewPage.currentIndex];
        if (player.hasAcceptedWarning){
            return;
        }

        player.pause();
        if (player.ima && player.ima.pauseAd) {
            player.ima.pauseAd();
        }
        player.controls(false);

        var postContainer   = $(player.el()).parent().parent(),
            warning         = postContainer.find('.warning-container'),
            video           = postContainer.find('video');

        postContainer.addClass('require-warning-confirm');

        if (player.warningType == 1 || player.warningType == '1') {
            warning.find('.warning').html('Video có chứa nội dung nhạy cảm');
        }

        if (player.warningType == 2 || player.warningType == '2') {
            warning.find('.warning').html('Video có chứa nội dung gây sợ hãi');
        }

        var btnContinue = warning.find('.btn-continue').unbind();
        btnContinue.click(function (e) {
            e.preventDefault();
            player.hasAcceptedWarning = true;

            if (!player.ima.adsActive || !player.ima.currentAd.isLinear()) {
                player.play();
            }
            if (player.ima && player.ima.resumeAd) {
                player.ima.resumeAd();
            }
            player.controls(true);
            postContainer.removeClass('require-warning-confirm');
            warning.hide();
            video.css('-webkit-filter', 'blur(0)');
            video.css('filter', 'blur(0)');
        });
    };
})(window.VideoManager = window.VideoManager || {}, jQuery);

// Category Page
(function (CategoryPage, $) {
    CategoryPage.isLoading = false;
    CategoryPage.isContinued = true;
    CategoryPage.currentPage = 1;
    CategoryPage.tagId = '';

    CategoryPage.init = function () {
        var container = $('#category-page .container'),
            sidebar = $('#category-page .container .sidebar');
        Main.floatingSidebar(container, sidebar);

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

        var btnViewMore = $('#category-page .view-more .btn-view');
        btnViewMore.parent().hide();
        CategoryPage.moreVideo();

        //btnViewMore.click(function (e) {
        //    e.preventDefault();
        //    btnViewMore.parent().hide();
        //    CategoryPage.addVideo();
        //    CategoryPage.moreVideo();
        //});
    };

    CategoryPage.moreVideo = function () {
        $(window).bind('scroll', function() {
            if (CategoryPage.isLoading || CategoryPage.isContinued == false) {
                return;
            }

            var positionMobileApps = $('#footer').offset().top,
                flagScroll = $(this).scrollTop() + $(this).height() - ($(this).height() / 6) > positionMobileApps;

            if (flagScroll)  {
                CategoryPage.isLoading = true;
                CategoryPage.addVideo();
            }
        });
    };

    CategoryPage.addVideo = function () {
        var loading = $('#loading-container .loading');
        loading.fadeIn(500);

        var request = $.ajax({
            url: FeelStatic.baseURL + 'tag/loadmore',
            method: 'GET',
            dataType: 'json',
            data: {
                page: CategoryPage.currentPage++,
                tag: CategoryPage.tagId
            },
            statusCode: {
                403: Main.showCaptchaInput
            }
        });
        request.done(function(data) {
            CategoryPage.isContinued = data.continue;

            var postVideo = $('#category-page .container .main-content .inside-container .inside .videos .post').first(),
                moreVideos = $('#category-page .container .main-content .inside-container .inside .videos');

            for(var i = 0; i < data.list.length; i++) {
                var post = postVideo.clone();

                var poster = post.find('.poster');
                poster.css('background-image', 'url(' + FeelStatic.baseURL + data.list[i].normal_picture + ')')
                    .find('.thumbnail')
                    .attr('src', FeelStatic.baseURL + data.list[i].normal_picture)
                    .attr('title', data.list[i].title)
                    .attr('alt', data.list[i].title);

                poster.find('.duration').text(data.list[i].format_duration);

                if (data.list[i].score > 0) {
                    poster.find('.hot .score').text(data.list[i].score);
                    poster.find('.hot.hide').removeClass('hide');
                } else {
                    poster.find('.hot').addClass('hide');
                }

                poster.find('.view .number').text(data.list[i].format_views);
                poster.find('.link')
                    .attr('href', FeelStatic.baseURL + 'post/' + data.list[i].seo_id)
                    .attr('title', data.list[i].title);

                var content = post.find('.content');
                content.find('.title > a')
                    .text(data.list[i].title)
                    .attr('href', FeelStatic.baseURL + 'post/' + data.list[i].seo_id)
                    .attr('title', data.list[i].title);
                content.find('.description').text(data.list[i].description);
                content.find('.created .time').text(data.list[i].fantastic_time);
                content.find('.author .username')
                    .text(data.list[i].moderator.name)
                    .attr('title', data.list[i].moderator.name)
                    .prop('href', data.list[i].moderator.profile_url);

                post.appendTo(moreVideos);
            }

            CategoryPage.isLoading = false;
            loading.hide();
        });
    };
})(window.CategoryPage = window.CategoryPage || {}, jQuery);

// Hot Page
(function (HotPage, $) {
    HotPage.isLoading = false;
    HotPage.isContinued = true;
    HotPage.currentPage = 0;

    HotPage.init = function () {
        var btnViewMore = $('#hot-page .view-more .btn-view');

        btnViewMore.parent().hide();
        HotPage.moreVideo();

        //btnViewMore.click(function (e) {
        //    e.preventDefault();
        //    btnViewMore.parent().hide();
        //    HotPage.addVideo();
        //    HotPage.moreVideo();
        //});
    };

    HotPage.moreVideo = function () {
        $(window).bind('scroll', function() {
            if (HotPage.isLoading || HotPage.isContinued == false) {
                return;
            }

            var positionMobileApps = $('#footer').offset().top,
                flagScroll = $(this).scrollTop() + $(this).height() - ($(this).height() / 6) > positionMobileApps;

            if (flagScroll)  {
                HotPage.isLoading = true;
                HotPage.addVideo();
            }
        });
    };

    HotPage.addVideo = function () {
        var loading = $('#loading-container .loading');
        loading.fadeIn(500);

        var request = $.ajax({
            url: FeelStatic.baseURL + 'post/loadMoreHotToday',
            method: 'GET',
            dataType: 'json',
            data: {
                page: HotPage.currentPage++
            },
            statusCode: {
                403: Main.showCaptchaInput
            }
        });
        request.done(function(data) {
            HotPage.isContinued = data.continue;

            var postVideo = $('#hot-page .videos .post').first(),
                moreVideos = $('#hot-page .videos');
            for(var i = 0; i < data.list.length; i++) {
                var post = postVideo.clone();

                var poster = post.find('.poster');

                poster.css('background-image', 'url(' + FeelStatic.baseURL + data.list[i].normal_picture + ')');
                poster.find('.thumbnail').attr('src', FeelStatic.baseURL + data.list[i].normal_picture)
                    .attr('title', data.list[i].title)
                    .attr('alt', data.list[i].title);
                poster.find('.number').text(++HotPage.numVideos);
                poster.find('.link').attr('href', FeelStatic.baseURL + 'post/' + data.list[i].seo_id)
                    .attr('title', data.list[i].title);

                var content = post.find('.content');
                content.find('.title > a').text(data.list[i].title)
                    .attr('href', FeelStatic.baseURL + 'post/' + data.list[i].seo_id)
                    .attr('title', data.list[i].title);
                content.find('.hot .score').text(data.list[i].score);
                content.find('.view .number').text(data.list[i].format_views);

                post.appendTo(moreVideos);
            }

            HotPage.isLoading = false;
            loading.hide();
        });
    };
})(window.HotPage = window.HotPage || {}, jQuery);

(function (AboutPage, $) {
    AboutPage.init = function () {
        $('html, body').animate({ scrollTop: 72 }, 1000);

        var menu = $('#about-page .sidebar .content .menu'),
            btnMenu =  menu.find('li a');
        btnMenu.click(function (e) {
            e.preventDefault();

            var hash = $(this).attr('href');
            FeelStatic.changeUrlWithoutReload(null, '', FeelStatic.baseURL + 'about/' + hash);
            AboutPage.showContent(hash, menu);

            if (hash == '#contact') {
                AboutPage.showMap();
            }
        });

        AboutPage.showContent(window.location.hash, menu);
    };

    AboutPage.showContent = function (hash, menu) {
        if (hash == '') {
            hash = '#info';
        }

        menu.find('li').removeClass('active');
        menu.find('li.'+ hash.replace('#', '')).addClass('active');

        var content = $('#about-page .content-container .content');
        content.hide();
        $(hash).show();
    };

    AboutPage.showMap = function () {
        var myLatLng = {lat: 10.806977, lng: 106.712407};

        var mapDiv = document.getElementById('map');
        var map = new google.maps.Map(mapDiv, {
            center: myLatLng,
            zoom: 17
        });

        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            title: 'Thật Vi Diệu'
        });
    };

    AboutPage.sendContact = function (btn, username) {
        var form = $(btn).parent().parent(),
            subject = form.find('.contact-subject').val(),
            email = form.find('.contact-email').val(),
            content = form.find('.contact-content').val();

        if (subject == '' || email == '' || content == '') {
            Main.notification('Bạn cần nhập đầy đủ thông tin!');
            return;
        }

        if (!Main.isEmail(email)) {
            Main.notification('Email không đúng định dạng!');
            return;
        }

        var request = $.ajax({
            url: FeelStatic.baseURL + 'notify/sendContact',
            method: 'POST',
            data: {
                username: username,
                subject: subject,
                email: email,
                content: content
            },
            statusCode: {
                403: Main.showCaptchaInput
            }
        });
        request.done(function(data) {
            if (username == 'Contact') {
                Main.notification('Cảm ơn bạn! <br> Đã liên hệ với chúng tôi!');
            } else {
                Main.notification('Cảm ơn bạn! <br> Đã góp ý cho chúng tôi!');
            }
        });
    };

    AboutPage.sendFeedBack = function () {

    };
})(window.AboutPage = window.AboutPage || {}, jQuery);

// Tag Page
(function (TagPage, $) {
    TagPage.isLoading = false;
    TagPage.isContinued = true;
    TagPage.currentPage = 1;
    TagPage.tagId = '';

    TagPage.init = function () {
        var container = $('#tag-page .container'),
            sidebar = $('#tag-page .container .sidebar');
        Main.floatingSidebar(container, sidebar);

        var videos = $('#tag-page .container .main-content .header .videos'),
            navigation = $('#tag-page .container .main-content .header .navigation');
        videos.slick({
            slidesToShow: 1,
            dots: true,
            appendDots: navigation,
            arrows: false,
            pauseOnHover: true,
            autoplay: true,
            autoplaySpeed: 5000
        });

        var btnViewMore = $('#tag-page .view-more .btn-view');
        btnViewMore.parent().hide();
        TagPage.moreVideo();

        //btnViewMore.click(function (e) {
        //    e.preventDefault();
        //    btnViewMore.parent().hide();
        //    TagPage.addVideo();
        //    TagPage.moreVideo();
        //});
    };

    TagPage.moreVideo = function () {
        $(window).bind('scroll', function() {
            if (TagPage.isLoading || TagPage.isContinued == false) {
                return;
            }

            var positionMobileApps = $('#footer').offset().top,
                flagScroll = $(this).scrollTop() + $(this).height() - ($(this).height() / 6) > positionMobileApps;

            if (flagScroll)  {
                TagPage.isLoading = true;
                TagPage.addVideo();
            }
        });
    };

    TagPage.addVideo = function () {
        var loading = $('#loading-container .loading');
        loading.fadeIn(500);

        var request = $.ajax({
            url: FeelStatic.baseURL + 'tag/loadmore',
            method: 'GET',
            dataType: 'json',
            data: {
                page: TagPage.currentPage++,
                tag: TagPage.tagId
            },
            statusCode: {
                403: Main.showCaptchaInput
            }
        });
        request.done(function(data) {
            TagPage.isContinued = data.continue;

            var postVideo = $('#tag-page .container .main-content .inside-container .inside .videos .post').first(),
                moreVideos = $('#tag-page .container .main-content .inside-container .inside .videos');

            for(var i = 0; i < data.list.length; i++) {
                var post = postVideo.clone();

                var poster = post.find('.poster');
                poster.css('background-image', 'url(' + FeelStatic.baseURL + data.list[i].normal_picture + ')')
                    .find('.thumbnail')
                    .attr('src', FeelStatic.baseURL + data.list[i].normal_picture)
                    .attr('title', data.list[i].title)
                    .attr('alt', data.list[i].title);

                poster.find('.duration').text(data.list[i].format_duration);

                if (data.list[i].score > 0) {
                    poster.find('.hot .score').text(data.list[i].score);
                    poster.find('.hot.hide').removeClass('hide');
                } else {
                    poster.find('.hot').addClass('hide');
                }

                poster.find('.view .number').text(data.list[i].format_views);
                poster.find('.link')
                    .attr('href', FeelStatic.baseURL + 'post/' + data.list[i].seo_id)
                    .attr('title', data.list[i].title);

                var content = post.find('.content');
                content.find('.title > a')
                    .text(data.list[i].title)
                    .attr('href', FeelStatic.baseURL + 'post/' + data.list[i].seo_id)
                    .attr('title', data.list[i].title);
                content.find('.description').text(data.list[i].description);
                content.find('.created .time').text(data.list[i].fantastic_time);
                content.find('.author .username')
                    .text(data.list[i].moderator.name)
                    .attr('title', data.list[i].moderator.name);

                post.appendTo(moreVideos);
            }

            TagPage.isLoading = false;
            loading.hide();
        });
    };
})(window.TagPage = window.TagPage || {}, jQuery);

// Search Page
(function (SearchPage, $) {
    SearchPage.isLoading = false;
    SearchPage.isContinued = true;
    SearchPage.currentPage = 1;
    SearchPage.keyword = '';

    SearchPage.init = function () {
        var container = $('#search-page .container'),
            sidebar = $('#search-page .container .sidebar');
        Main.floatingSidebar(container, sidebar);

        var videos = $('#search-page .container .main-content .header .videos'),
            navigation = $('#search-page .container .main-content .header .navigation');
        videos.slick({
            slidesToShow: 1,
            dots: true,
            appendDots: navigation,
            arrows: false,
            pauseOnHover: true,
            autoplay: true,
            autoplaySpeed: 5000
        });

        var btnViewMore = $('#search-page .view-more .btn-view');
        btnViewMore.click(function (e) {
            e.preventDefault();
            btnViewMore.parent().hide();
            SearchPage.addVideo();
            SearchPage.moreVideo();
        });
    };

    SearchPage.moreVideo = function () {
        $(window).bind('scroll', function() {
            if (SearchPage.isLoading || SearchPage.isContinued == false) {
                return;
            }

            var positionMobileApps = $('#footer').offset().top,
                flagScroll = $(this).scrollTop() + $(this).height() - ($(this).height() / 6) > positionMobileApps;

            if (flagScroll)  {
                SearchPage.isLoading = true;
                SearchPage.addVideo();
            }
        });
    };

    SearchPage.addVideo = function () {
        var loading = $('#loading-container .loading');
        loading.fadeIn(500);

        var request = $.ajax({
            url: FeelStatic.baseURL + 'search/json',
            method: 'GET',
            dataType: 'json',
            data: {
                page: SearchPage.currentPage++,
                keyword: SearchPage.keyword
            },
            statusCode: {
                403: Main.showCaptchaInput
            }
        });
        request.done(function(data) {
            SearchPage.isContinued = data.continue;

            var postVideo = $('#search-page .container .main-content .inside-container .inside .videos .post').first(),
                moreVideos = $('#search-page .container .main-content .inside-container .inside .videos');

            for(var i = 0; i < data.list.length; i++) {
                var post = postVideo.clone();

                var poster = post.find('.poster');
                poster.css('background-image', 'url(' + FeelStatic.baseURL + data.list[i].normal_picture + ')')
                    .find('.thumbnail')
                    .attr('src', FeelStatic.baseURL + data.list[i].normal_picture)
                    .attr('title', data.list[i].title)
                    .attr('alt', data.list[i].title);

                poster.find('.duration').text(data.list[i].format_duration);

                if (data.list[i].score > 0) {
                    poster.find('.hot .score').text(data.list[i].score);
                    poster.find('.hot.hide').removeClass('hide');
                } else {
                    poster.find('.hot').addClass('hide');
                }

                poster.find('.view .number').text(data.list[i].format_views);
                poster.find('.link')
                    .attr('href', FeelStatic.baseURL + 'post/' + data.list[i].seo_id)
                    .attr('title', data.list[i].title);

                var content = post.find('.content');
                content.find('.title > a')
                    .text(data.list[i].title)
                    .attr('href', FeelStatic.baseURL + 'post/' + data.list[i].seo_id)
                    .attr('title', data.list[i].title);
                content.find('.description').text(data.list[i].description);
                content.find('.created .time').text(data.list[i].fantastic_time);
                content.find('.author .username')
                    .text(data.list[i].moderator.name)
                    .attr('title', data.list[i].moderator.name);

                post.appendTo(moreVideos);
            }

            SearchPage.isLoading = false;
            loading.hide();
        });
    };
})(window.SearchPage = window.SearchPage || {}, jQuery);

(function(PersonalPage, $) {
    PersonalPage.maxSuggestVideos = 12;
    PersonalPage.filename = '';

    PersonalPage.loadVideos = function() {
        var request = $.ajax({
            url: FeelStatic.baseURL + 'personal/get/',
            method: 'GET',
            dataType: 'json',
            statusCode: {
                403: Main.showCaptchaInput
            }
        });

        request.done(function(data) {
            $('#hot-sidebar').removeClass('temporary-hide');
            $('#loading-container').hide();
            $('#suggested-container').show();
            var merged = PersonalPage.mergeHotList(data.suggest);
            var loadedIds = PersonalPage.renderList('#suggested-list', merged, PersonalPage.maxSuggestVideos);
            Main.saveLoadedVideos(loadedIds);

            PersonalPage.renderList('#recent-list', data.recent, PersonalPage.maxSuggestVideos);
        });
    };

    PersonalPage.mergeHotList = function(data) {
        if (PersonalPage.hotVideos.length > 3) {
            PersonalPage.hotVideos.sort(function() { return 0.5 - Math.random() });
            var hot = PersonalPage.hotVideos.slice(0, 3);
            for (var i = 0; i < hot.length; i++) {
                data.push(hot[i]);
            }
        }
        return data;
    };

    PersonalPage.renderList = function(container, data, max) {
        data.sort(function() { return 0.5 - Math.random() });
        container = $(container);
        var defaultItem = container.find('.container').first();

        var ids = [];
        for (var i = 0; i < max; i++) {
            if (!data[i]) {
                continue;
            }
            ids.push(data[i].id);
            var item = defaultItem.clone();
            item.find('.poster').css('background-image', 'url(' + FeelStatic.baseURL + data[i].small_picture + ')');
            item.find('.thumbnail').prop('src', data[i].small_picture);
            item.find('.duration').html(data[i].format_duration);
            item.find('.view > .number').html(data[i].format_views);
            item.find('.link').prop('href', FeelStatic.baseURL +'/post/' + data[i].seo_id);
            item.find('.title > a').html(data[i].title).prop('href', FeelStatic.baseURL + '/post/' + data[i].seo_id);
            item.find('.author .username').html(data[i].moderator.name);

            if (data[i].score > 0) {
                item.find('.score').html(data[i].score);
            } else {
                item.find('.hot').hide();
            }

            item.appendTo(container).show();
        }
        defaultItem.remove();
        return ids;
    };

    PersonalPage.http_arr = [];
    PersonalPage.allowedType = [
        "video/mp4"
    ];

    PersonalPage.maxFileSize = 200; //mb

    PersonalPage.validateMedia = function(file) {
        //Validate type
        if (PersonalPage.allowedType.indexOf(file.type) == -1) {
            return false;
        }

        if (PersonalPage.maxFileSize * 1024 * 1024 < file.size) {
            return false;
        }

        return true;
    };

    PersonalPage.doUpload = function() {
        var files = document.getElementById('media-file').files;
        var notAllowedFiles = [];
        for (var i=0;i<files.length;i++) {
            if (PersonalPage.validateMedia(files[i])) {
                var uploadPage      = $('#upload-page'),
                    btnContainer    = uploadPage.find('.btn-container'),
                    formUpload      = uploadPage.find('.form-upload');

                btnContainer.hide(0);
                formUpload.css('display', 'inline-block');
                PersonalPage.uploadFile(files[i], i);
            } else {
                notAllowedFiles.push(files[i].name);
            }
        }

        if (notAllowedFiles.length > 0) {
            Main.notification('Thatvidieu.com chỉ hỗ trợ file .mp4 và kích thước không quá 200Mb <br/>Vui lòng chọn file khác!');
        }
        return false;
    };

    PersonalPage.uploadFile = function(file, index) {
        var http = new XMLHttpRequest();
        PersonalPage.http_arr.push(http);
        var uploadPage          = $('#upload-page'),
            btnContainer    = uploadPage.find('.btn-container'),
            formUpload          = uploadPage.find('.form-upload');

        formUpload.find('.btn.cancel').click(function(e) {
            e.preventDefault();
            http.removeEventListener('progress');
            http.abort();
            btnContainer.show(0);
            formUpload.hide(0);
        });

        //For calculate speed
        var oldLoaded = 0;
        var oldTime = 0;
        //Handler
        http.upload.addEventListener('progress', function(event) {
            if (oldTime == 0) { //default time
                oldTime = event.timeStamp;
            }
            //Init
            var fileName = file.name; //Filename
            var fileLoaded = event.loaded; //Loaded
            var fileTotal = event.total; //Total
            var fileProgress = parseInt((fileLoaded/fileTotal)*100) || 0; //Process
            var speed = PersonalPage.speedRate(oldTime, event.timeStamp, oldLoaded, event.loaded);
            //update progress bar here
            //========================
            //
            formUpload.find('.process-bar .percent').width(fileProgress + '%');
            formUpload.find('.process-bar .percent .number').html(fileProgress + '%');
            //
            //========================

            var fileRemain = fileTotal - fileLoaded;
            var timeRemain = fileRemain / speed;

            oldTime = event.timeStamp;
            oldLoaded = event.loaded;

            if (fileProgress >= 100) {
                formUpload.find('.process-bar .percent .number').html('Hoàn thành!');
                formUpload.find('.btn.save').removeClass('disabled');
            }
        }, false);


        //Start upload
        var data = new FormData();
        data.append('filename', file.name);
        data.append('uploadfile', file);
        http.open('POST', FeelStatic.baseURL + 'personal/doUpload', true);
        http.send(data);


        http.onreadystatechange = function(event) {
            if (http.readyState == 4 && http.status == 200) {
                //Upload done
                var data = http.responseText;
                data = JSON.parse(data);
                if (data.error_message) {
                    return;
                }

                //save filename in DOM which will be sent to PersonalPage.saveVideo later
                //========================
                PersonalPage.filename = data.filename;
                //========================
            }
            http.removeEventListener('progress', function() {});
        }
    };

    PersonalPage.cancelUpload = function(index) {
        if (index) {
            PersonalPage.http_arr[index].removeEventListener('progress');
            PersonalPage.http_arr[index].abort();
        } else {
            for (var i = 0; i < PersonalPage.http_arr.length; i++) {
                PersonalPage.http_arr[i].removeEventListener('progress');
                PersonalPage.http_arr[i].abort();
            }
        }
    };

    PersonalPage.speedRate = function(oldTime, newTime, oldLoaded, newLoaded) {
        var timeProcess = newTime - oldTime; //Lagging time
        if (timeProcess != 0) {
            var currentLoadedPerMilisecond = (newLoaded - oldLoaded)/timeProcess; // byte/ms
            return parseInt((currentLoadedPerMilisecond * 1000)/1024); //Speed KB/s
        } else {
            return parseInt(newLoaded/1024); //Speed KB/s
        }
    };

    PersonalPage.saveVideo = function(btn) {
        //validate and get form value here
        var uploadPage      = $('#upload-page'),
            btnContainer    = uploadPage.find('.btn-container'),
            formUpload      = uploadPage.find('.form-upload');

        var title = uploadPage.find('.input.title').val();
        var description = uploadPage.find('.input.description').val();
        var keyword = uploadPage.find('.input.keywords').val();
        var media = PersonalPage.filename;
        if (title == '') {
            Main.notification('Vui lòng nhập tiêu đề');
            return;
        }
        if (description == '') {
            Main.notification('Vui lòng nhập mô tả');
            return;
        }

        if (PersonalPage.savingVideoInfo) {
            return;
        }

        PersonalPage.savingVideoInfo = true;
        btn = $(btn);
        btn.prop('disabled', true).html('Đang lưu');
        //==============================================================

        var request = $.ajax({
            url: FeelStatic.baseURL + 'personal/save',
            method: 'POST',
            dataType: 'json',
            data: {
                title: title,
                description: description,
                keyword: keyword,
                media: media
            },
            statusCode: {
                403: Main.showCaptchaInput
            }
        });

        request.done(function(data) {
            if (data.error_message) {
                Main.notification('Bạn cần nhập đầy đủ thông tin!');
            } else {
                Main.notification('<b>Tải video thành công!</b> <br> Video của bạn đang được Thatvidieu kiểm duyệt. <br> Bạn có thể kiểm tra <strong><a href="' + FeelStatic.baseURL + 'personal/list">tại đây</a></strong>');
                btnContainer.show(0);
                formUpload.hide(0);
                btn.prop('disabled', false).html('Lưu lại');
            }
        });
    };

})(window.PersonalPage = window.PersonalPage || {}, jQuery);