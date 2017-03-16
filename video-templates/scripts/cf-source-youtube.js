var $ = jQuery,
    video_container = $('#cf_youtube_overlay_container'),
    form_container = $('.cf_youtube_overlay_form'),
    yt_id = video_container.data('id'),
    player;


$('body').on('submit', '.cf_youtube_overlay_form form', function() {

    document.cookie = "_cf_yt_video_" + yt_id + "=" + yt_id + ";path=/";
    form_container.fadeOut(function(){
        if( player ) {
            player.playVideo();
        }
    });

});

function onYouTubeIframeAPIReady() {
    player = new YT.Player('cf_youtube_overlay_container', {
        height: '390',
        width: '640',
        videoId: yt_id,
        playerVars: { rel: 0, },
        events: {
            'onStateChange': onPlayerStateChange
        }
    });
}

function onPlayerStateChange( event ) {
    if( 1 == event.data ) {

        if( !check_video_cookie( yt_id ) ) {
            player.pauseVideo();
            form_container.fadeIn();
        }
    }
}

function check_video_cookie( id ) {

    var name = '_cf_yt_video_' + id + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            var video_id = c.substring(name.length, c.length);
            if( video_id !== id ) {
                return false;
            } else {
                return true;
            }
        }
    }
    return false;


};