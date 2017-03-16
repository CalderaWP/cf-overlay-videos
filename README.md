# Caldera Forms Video Overlay Plugin #
This plugin allows you to extend Caldera Forms to have a video embedded, on click a user will get a form which must be completed before video can continue playing.  
Uses cookies to track so same visitor does not need to fill out the form twice.

## Shortcode Usage ##
__Example__  
`[caldera_forms id="form_id" video_id="video_id" video_source="youtube"]`
* __video_id__ - `ID` of the video
* __video_source__  - `Source` of the video (i.e `youtube` )
  
## Currently Supported Sources ##
* Youtube
  
## Filters ##
__cf_video_overlays_wrapper_classes__ - adds classes to the main wrapper  
```
add_filter( 'cf_video_overlays_wrapper_classes', function( $classes ) {
	$classes[] = 'hi-shawn';
	return $classes;
}, 10 );
```

