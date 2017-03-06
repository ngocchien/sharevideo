
/**
 * Created by ChienNguyen on 2/19/2017.
 */
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