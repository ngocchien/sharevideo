
/*!
 * videojs-playlists - Playlists done right for Videojs
 * v0.1.1
 *
 * copyright Antonio Laguna 2013
 * MIT License
 */
//videojs-playlists.js
function playList(options,arg){
    var player = this;
    player.pl = player.pl || {};
    var index = parseInt(options,10);

    player.pl._guessVideoType = function(video){
        var videoTypes = {
            'webm' : 'video/webm',
            'mp4' : 'video/mp4',
            'ogv' : 'video/ogg',
            'm3u8': 'application/x-mpegURL'
        };
        var extension = video.split('.').pop();

        return videoTypes[extension] || '';
    };

    player.pl.init = function(videos, options) {
        options = options || {};
        player.pl.videos = [];
        player.pl.current = 0;
        player.on('ended', player.pl._videoEnd);

        if (options.getVideoSource) {
            player.pl.getVideoSource = options.getVideoSource;
        }

        player.pl._addVideos(videos);
    };

    player.pl._updatePoster = function(posterURL) {
        player.poster(posterURL);
        player.removeChild(player.posterImage);
        player.posterImage = player.addChild("posterImage");
    };

    player.pl._addVideos = function(videos){
        for (var i = 0, length = videos.length; i < length; i++){
            var aux = [];
            for (var j = 0, len = videos[i].src.length; j < len; j++){
                aux.push({
                    type : player.pl._guessVideoType(videos[i].src[j]),
                    src : videos[i].src[j]
                });
            }
            videos[i].src = aux;
            player.pl.videos.push(videos[i]);
        }
    };

    player.pl._nextPrev = function(func){
        var comparison, addendum;

        if (func === 'next'){
            comparison = player.pl.videos.length -1;
            addendum = 1;
        }
        else {
            comparison = 0;
            addendum = -1;
        }

        if (player.pl.current !== comparison){
            var newIndex = player.pl.current + addendum;
            player.pl._setVideo(newIndex);
            player.trigger(func, [player.pl.videos[newIndex]]);
        }
    };

    player.pl._setVideo = function(index){
        if (index < player.pl.videos.length){
            player.pl.current = index;
            player.pl.currentVideo = player.pl.videos[index];
            player.pl._setVideoAttr({
                controls: player.pl.videos[index].controls
            });

            if (!player.paused()){
                player.pl._resumeVideo();
            }

            if (player.pl.getVideoSource) {
                player.pl.getVideoSource(player.pl.videos[index], function(src, poster) {
                    player.pl._setVideoSource(src, poster);
                });
            } else {
                player.pl._setVideoSource(player.pl.videos[index].src, player.pl.videos[index].poster);
            }
        }
    };

    player.pl._setVideoSource = function(src, poster) {
        //player.src(src);
        //player.updateSrc([{src: src[0].src]}], {hls: true});
        player.updateSrc(src, {hls: true});
        player.pl._updatePoster(poster);
    };

    player.pl._setVideoAttr = function(options) {
        player.controls(options.controls);
    };

    player.pl._resumeVideo = function(){
        player.one('loadstart',function(){
            player.play();
        });
    };

    player.pl._videoEnd = function(){
        if (player.pl.current === player.pl.videos.length -1){
            player.trigger('lastVideoEnded');
        }
        else {
            player.pl._resumeVideo();
            this.pl._nextPrev('next');
        }
    };

    player.pl.stop = function() {
        var lastStopSrc = player.src();
        var lastStopPos	= player.currentTime();

        player.src('null.mp4');
        player.one('play', function() {
            player.pl._setVideo(player.pl.current);
            if (player.src() == lastStopSrc) {
                player.currentTime(lastStopPos);
            }
        })
    }

    if (options instanceof Array){
        player.pl.init(options, arg);
        player.pl._setVideo(0);
        return player;
    }
    else if (index === index){ // NaN
        player.pl._setVideo(index);
        return player;
    }
    else if (typeof options === 'string' && typeof player.pl[options] !== 'undefined'){
        player.pl[options].apply(player);
        return player;
    }
}

//videojs.Player.prototype.next = function(){
//    this.pl._nextPrev('next');
//    return this;
//};
//videojs.Player.prototype.prev = function(){
//    this.pl._nextPrev('prev');
//    return this;
//};

videojs.plugin('playList', playList);