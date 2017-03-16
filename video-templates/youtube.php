<?php
$output = '<div id="cf_youtube_overlay_wrapper" class="embed-responsive embed-responsive-16by9">';
	$output .= '<div class="cf_youtube_overlay_form">' . $form_html . '</div>';
	$output .= '<div class="cf_youtube_overlay_wrapper"><div id="cf_youtube_overlay_container" data-id="' . $atts['video_id'] . '"></div></div>';
$output .= '</div>';