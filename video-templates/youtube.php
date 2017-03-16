<?php
$additional_wrapper_classes = [];
$additional_wrapper_classes = apply_filters( 'cf_video_overlays_wrapper_classes', $additional_wrapper_classes );

$output = '<div id="cf_youtube_overlay_wrapper" class="cf_video_overlays_embed-responsive cf_video_overlays_embed-responsive-16by9 cf_video_overlays_form_' . $form['ID'] . ' ' . implode( ' ', $additional_wrapper_classes ) . '">';
	$output .= '<div class="cf_video_overlays_youtube_form">' . $form_html . '</div>';
	$output .= '<div class="cf_youtube_overlay_wrapper"><div id="cf_youtube_overlay_container" data-id="' . $atts['video_id'] . '"></div></div>';
$output .= '</div>';
return $output;