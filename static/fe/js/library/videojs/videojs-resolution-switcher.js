(function(){var n=null,n="undefined"===typeof window.videojs&&"function"===typeof require?require("video.js"):window.videojs;(function(n,e){var q={ui:!0},k=e.getComponent("MenuItem"),i=e.extend(k,{constructor:function(j,g){g.selectable=!0;k.call(this,j,g);this.src=g.src;j.on("resolutionchange",e.bind(this,this.update))}});i.prototype.handleClick=function(e){k.prototype.handleClick.call(this,e);this.player_.currentResolution(this.options_.label)};i.prototype.update=function(){this.selected(this.options_.label===
this.player_.currentResolution().label)};e.registerComponent("ResolutionMenuItem",i);var l=e.getComponent("MenuButton"),o=e.extend(l,{constructor:function(j,g){this.label=document.createElement("span");g.label="Quality";l.call(this,j,g);this.el().setAttribute("aria-label","Quality");this.controlText("Quality");if(g.dynamicLabel)e.addClass(this.label,"vjs-resolution-button-label"),this.el().appendChild(this.label);else{var h=document.createElement("span");e.addClass(h,"vjs-menu-icon");this.el().appendChild(h)}j.on("updateSources",
e.bind(this,this.update))}});o.prototype.createItems=function(){var e=[],g=this.sources&&this.sources.label||{},h;for(h in g)g.hasOwnProperty(h)&&e.push(new i(this.player_,{label:h,src:g[h],selected:h===(this.currentSelection?this.currentSelection.label:!1)}));return e};o.prototype.update=function(){this.sources=this.player_.getGroupedSrc();this.currentSelection=this.player_.currentResolution();this.label.innerHTML=this.currentSelection?this.currentSelection.label:"";return l.prototype.update.call(this)};
o.prototype.buildCSSClass=function(){return l.prototype.buildCSSClass.call(this)+" vjs-resolution-button"};e.registerComponent("ResolutionMenuButton",o);e.plugin("videoJsResolutionSwitcher",function(j){function g(a,c){return!a.res||!c.res?0:+c.res-+a.res}function h(a){var c={label:{},res:{},type:{}};a.map(function(a){i(c,"label",a);i(c,"res",a);i(c,"type",a);c.label[a.label].push(a);c.res[a.res].push(a);c.type[a.type].push(a)});return c}function i(a,c,b){null==a[c][b[c]]&&(a[c][b[c]]=[])}function l(a,
c){var b=m["default"],f="";"high"===b?(b=c[0].res,f=c[0].label):"low"===b||null==b||!a.res[b]?(b=c[c.length-1].res,f=c[c.length-1].label):a.res[b]&&(f=a.res[b][0].label);return{res:b,label:f,sources:a.res[b]}}function n(a){var b={highres:{res:1080,label:"1080",yt:"highres"},hd1080:{res:1080,label:"1080",yt:"hd1080"},hd720:{res:720,label:"720",yt:"hd720"},large:{res:480,label:"480",yt:"large"},medium:{res:360,label:"360",yt:"medium"},small:{res:240,label:"240",yt:"small"},tiny:{res:144,label:"144",
yt:"tiny"},auto:{res:0,label:"auto",yt:"auto"}},d=function(b,c){a.tech_.ytPlayer.setPlaybackQuality(c[0]._yt);a.trigger("updateSources");return a};m.customSourcePicker=d;a.tech_.ytPlayer.setPlaybackQuality("auto");a.tech_.ytPlayer.addEventListener("onPlaybackQualityChange",function(f){for(var p in b)if(p.yt===f.data){a.currentResolution(p.label,d);break}});a.one("play",function(){var f=[];a.tech_.ytPlayer.getAvailableQualityLevels().map(function(d){f.push({src:a.src().src,type:a.src().type,label:b[d].label,
res:b[d].res,_yt:b[d].yt})});a.groupedSrc=h(f);var p=a.groupedSrc.label.auto;this.currentResolutionState={label:"auto",sources:p};a.trigger("updateSources");a.setSourcesSanitized(p,"auto",d)})}function k(a){a.tech_.hls&&(a.on("mediachange",function(){a.trigger("resolutionchange")}),a.on("loadedmetadata",function(){var b=this.tech_.hls,d=b.representations(),f=[{src:"auto",type:"application/x-mpegURL",label:"auto",res:0}];console.log(d);d.map(function(a){f.push({src:a,type:"application/x-mpegURL",label:'<span class="representation-height">'+
a.height+'p</span> <span class="representation-bit">'+Math.round(a.bandwidth/1024)+"kbps</span>",res:a.height})});a.groupedSrc=h(f);d=function(b,c,d){var f=b.tech_.hls.representations();if("auto"===d)return f.map(function(a){a.enabled(!0)}),a.trigger("updateSources"),b;var e=c[0].src.id;f.map(function(a){a.id!==e?a.enabled(!1):a.enabled(!0)});a.trigger("updateSources");return b};m.customSourcePicker=d;b=b.playlists.media();this.currentResolutionState={label:"auto",sources:b};a.trigger("updateSources");
a.setSourcesSanitized(b,"auto",d)}))}var m=e.mergeOptions(q,j),b=this;b.updateSrc=function(a,c){if(!a)return b.src();if(c&&c.hls)return b.src(a),k(b);a=a.filter(function(a){try{return""!==b.canPlayType(a.type)}catch(c){return!0}});this.currentSources=a.sort(g);this.groupedSrc=h(this.currentSources);var d=l(this.groupedSrc,this.currentSources);this.currentResolutionState={label:d.label,sources:d.sources};b.trigger("updateSources");b.setSourcesSanitized(d.sources,d.label);b.trigger("resolutionchange");
return b};b.currentResolution=function(a,c){if(null==a)return this.currentResolutionState;if(this.groupedSrc&&this.groupedSrc.label&&this.groupedSrc.label[a]){var d=this.groupedSrc.label[a],f=b.currentTime(),e=b.paused();!e&&this.player_.options_.bigPlayButton&&this.player_.bigPlayButton.hide();var g="loadeddata";"Youtube"!==this.player_.techName_&&("none"===this.player_.preload()&&"Flash"!==this.player_.techName_)&&(g="timeupdate");b.setSourcesSanitized(d,a,c||m.customSourcePicker).one(g,function(){b.currentTime(f);
b.handleTechSeeked_();e||b.play().handleTechSeeked_();b.trigger("resolutionchange")});return b}};b.getGroupedSrc=function(){return this.groupedSrc};b.setSourcesSanitized=function(a,c,d){this.currentResolutionState={label:c,sources:a};if("function"===typeof d)return d(b,a,c);b.src(a.map(function(a){return{src:a.src,type:a.type,res:a.res}}));return b};b.ready(function(){if(m.ui){var a=new o(b,m);b.controlBar.resolutionSwitcher=b.controlBar.el_.insertBefore(a.el_,b.controlBar.getChild("fullscreenToggle").el_);
b.controlBar.resolutionSwitcher.dispose=function(){this.parentNode.removeChild(this)}}1<b.options_.sources.length&&b.updateSrc(b.options_.sources);b.tech_.hls&&b.options_.sources.length&&k(b);"Youtube"===b.techName_&&n(b)})})})(window,n)})();
