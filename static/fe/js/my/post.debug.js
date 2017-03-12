/**
 * Created by ChienNguyen on 2/19/2017.
 */
// View Page
(function (ViewPage, $) {
    ViewPage.isLoading = false;
    ViewPage.maxRenderedVidIdx = 0;
    ViewPage.videoList = [];
    ViewPage.currentId = false;
    ViewPage.isContinue = true;
    ViewPage.playerList = [];
    ViewPage.currentIndex = 0;
    ViewPage.activeVideo = null; //Store the last active video object
    ViewPage.timeTracking = 10;
    ViewPage.videoQuality = 0;
    ViewPage.eventScroll = 'scroll';//Main.isMobile.any() ? 'touchstart' : 'scroll';
    ViewPage.volumeDefault = 90;
    ViewPage.hasVideoIntro = false;
    ViewPage.firstVideo = true;
    ViewPage.lastScrollTop = -100; //Last scrollTop that onscroll has been checked in auto play video event
    ViewPage.minRenderedVidIdx = 0;

    ViewPage.init = function () {
        $('html, body').animate({scrollTop: 80}, 1000);

        if (Main.isMobile.any()) {
            $('#footer').hide();
        }

        var container = $('#view-page .container'),
            sidebar = $('#view-page .container .sidebar');
        Main.floatingSidebar(container, sidebar);

        $(window).resize(function () {
            var playerContainer = $('.video-js');
            var container = playerContainer.parent();
            playerContainer.width(container.width()).height(container.height());
        });

        //Init the boolean value ViewPage.isScrolling which is used to check that user is scrolling or not
        var t;
        $(window).bind('scroll', function () {
            ViewPage.isScrolling = true;

            if (t) {
                clearTimeout(t);
            }

            t = setTimeout(function () {
                ViewPage.isScrolling = false;
            }, 300);
        });

        $(window).unload(function () {
            $(window).scrollTop(0);
        });
    };
    ViewPage.nextVideo = function () {
        if (ViewPage.currentIndex >= ViewPage.videoList.length) {
            return
        }
        var next = $(ViewPage.activeVideo).next();
        $('html, body').animate({scrollTop: next.offset().top - 50}, 300);
    };

    ViewPage.btnShare = function (btn) {
        btn = $(btn);
        btn.parent().find('.social').toggleClass('active');

    };

    ViewPage.shareSocial = function (btn, type) {
        var prefix = '';

        if (type == 'facebook') {
            prefix = 'https://www.facebook.com/sharer.php?u=';
        } else if (type == 'google') {
            prefix = 'https://plus.google.com/share?url=';
        } else {
            prefix = 'https://twitter.com/share?url=';
        }

        var url = prefix + $(btn).data('url'),
            title = $(btn).attr('data-title');

        window.open(url, title, 'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
    };

    ViewPage.playerVideo = function (postId, idContainer, sourceList, linkPoster, linkLogo, autoPlay, warningType) {
        $('#' + idContainer).html('');

        var widthContainer = $('#' + idContainer).width(),
            screen = '';
        if (Main.aspectRatio == '16:9') {
            screen = 9 / 16;
        } else {
            screen = 3 / 4;
        }
        $('#' + idContainer).height(widthContainer * screen);

        var slogan = $('#view-page .container .slogan');

        var lastScrollTop = -100;
        $(window).bind('scroll', function () {
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
            ableToPlayIntro = (checkRefferer || Main.getPartner()) && !ViewPage.hasVideoIntro && !hasViewedIntroToday;
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

        jwp.on('ready', function () {
            var video = $(this.getContainer()).find('video');
            video.bind('contextmenu', function (e) {
                e.stopPropagation();
                e.preventDefault();
            });
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

        jwp.on('levelsChanged', function (e) {
            FeelStatic.setCookie('quality', e.currentQuality + (3 - jwp.sources.length), 0);
            if (e.currentQuality + (3 - jwp.sources.length) > 1 && /(android)/i.test(navigator.userAgent)) {//240p
                $(this.getContainer()).addClass('hide-buffering-spinner');
            }
        });

        jwp.position = 0;
    };

})(window.ViewPage = window.ViewPage || {}, jQuery);