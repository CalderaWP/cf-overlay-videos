<?php
/**
 Plugin Name: Caldera Forms Video Overlays
 */

add_action( 'caldera_forms_includes_complete', 'cf_video_overlays_init' );

/**
 * Make plugin go
 *
 * @since 0.0.1
 */
function cf_video_overlays_init(){
	//need to add CF 1.5.0.7 or later check here
	new CF_Video_Overlays();
}

/**
 * Is this an supported source type?
 *
 * @since 0.0.1
 *
 * @param string $source_type
 *
 * @return bool
 */
function cf_video_overlays_allowed_source( $source_type){
	$allowed = apply_filters( 'cf_video_overlays_allowed_sources', array(
		'youtube'
	) );

	return in_array( $source_type, $allowed );
}





class CF_Video_Overlays {

	const ID_ATT = 'video_id';

	const SOURCE_ATT = 'video_source';


	public function __construct(){
		add_filter( 'shortcode_atts_caldera_form', array( $this, 'shortcode_atts' ), 10, 4 );
		add_filter( 'shortcode_atts_caldera_forms_modal', array( $this, 'shortcode_atts' ), 10, 4 );
		add_filter( 'caldera_forms_pre_render_form', array( $this, 'maybe_load' ), 10, 4 );
	}

	/**
	 * Allow extra shortcode atts
	 *
	 * @since 0.0.1
	 *
	 * @param $allowed
	 * @param $pairs
	 * @param $atts
	 * @param $shortcode
	 *
	 * @return mixed
	 */
	public function shortcode_atts(  $allowed, $pairs, $atts, $shortcode ){
		if( isset( $atts[ self::ID_ATT ] ) ){
			$allowed[ self::SOURCE_ATT ] = $atts[ self::SOURCE_ATT ];
			if( isset( $allowed[ self::SOURCE_ATT ] ) && cf_video_overlays_allowed_source( $allowed[ self::SOURCE_ATT ] )  ){
				$allowed[ self::SOURCE_ATT ] = $atts[ self::SOURCE_ATT ];
			}else{
				$allowed[ self::SOURCE_ATT ] = 'youtube';
			}
		}

		return $allowed;
	}

	/**
	 * If is video overlay form, replace with the video player markup
	 *
	 * @since 0.0.1
	 *
	 * @param $output
	 * @param $entry_id
	 * @param $form
	 * @param $atts
	 *
	 * @return string
	 */
	public function maybe_load( $output, $entry_id, $form, $atts ){
		if( isset( $atts[ self::ID_ATT ] ) ){

			remove_filter( 'caldera_forms_pre_render_form', array( $this, 'maybe_load' ), 10 );

			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

			$form_html = Caldera_Forms::render_form( $form );

			$output = 'Hi Roy'; //include the partial with the video player/overlay
		}

		return $output;
	}

	/**
	 * Load needed JavaScript for the player
	 *
	 * @since 0.0.1
	 */
	public function load_scripts(){
		//going to need to do some enqueuing here
	}

}