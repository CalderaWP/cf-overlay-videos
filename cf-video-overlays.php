<?php
/**
 Plugin Name: Caldera Forms Video Overlays
 */

add_action( 'caldera_forms_includes_complete', 'cf_video_overlays_init' );

/**
 * Render a video overlay form
 *
 * @since 0.0.2
 *
 * @param array $atts Shortcode atts, with id, video_id and video_source
 * @param bool $modal Optional. If is modal or not. Default is false
 *
 * @return string
 */
function cf_video_overlays_render( $atts, $modal = false ){
	if( !class_exists( 'Caldera_forms') ) {
		return false;
	}
	$form = Caldera_Forms_Forms::get_form( $atts['id'] );
	if( 'youtube' == $atts['video_source'] ) {
		wp_enqueue_script( 'youtube-iframe-api' );
	}
	wp_enqueue_script( 'cf_video_overlay_script-' . $atts['video_source'] );
	wp_enqueue_style( 'cf_video_overlay_style' );

	$form_html = Caldera_Forms::render_form( $form );
	$video_form_html = include( plugin_dir_path( __FILE__ ) . '/video-templates/' . $atts['video_source'] . '.php' );

	if( $modal ){
		$modal_id = Caldera_Forms_Render_Modals::modal_id( $form );
		$output = Caldera_Forms_Render_Modals::modal_button( $atts, '', $form, $modal_id );
		$modal_body = Caldera_Forms_Render_Modals::modal_body( $video_form_html, $modal_id, 'cf-video-overlay-modal-body' );
		Caldera_Forms_Render_Modals::add_to_footer( $modal_body );
	}else{
		$output = $video_form_html;

	}

	return $output;

}
/**
 * Make plugin go
 *
 * @since 0.0.1
 */
function cf_video_overlays_init(){
	//You must have CF 1.5.0.7 or you will not go to space today
	if (  class_exists( 'Caldera_Forms_Render_Modals' ) ) {
		new CF_Video_Overlays();
	}

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
function cf_video_overlays_allowed_provider( $source_type ){
	return in_array( $source_type, cf_video_overlays_allowed_providers() );

}

/**
 * Get the allowed video providers
 *
 * @since 0.0.2
 *
 * @return array
 */
function cf_video_overlays_allowed_providers(){
	/**
	 * Filter for the allowed video types
	 *
	 * @since 0.0.2
	 *
	 * @param array $allowed_types
	 */
	return apply_filters( 'cf_video_overlays_allowed_providers', array() );
}




class CF_Video_Overlays {

	const ID_ATT = 'video_id';

	const SOURCE_ATT = 'video_source';

	/**
	 * @var array
	 */
	protected $modal_forms;

	/**
	 * CF_Video_Overlays constructor.
	 *
	 * @since 0.0.1
	 */
	public function __construct(){
		add_filter( 'shortcode_atts_caldera_form', array( $this, 'shortcode_atts' ), 10, 4 );
		add_filter( 'shortcode_atts_caldera_form_modal', array( $this, 'shortcode_atts' ), 10, 4 );
		add_filter( 'caldera_forms_pre_render_form', array( $this, 'maybe_load' ), 99, 4 );
		add_filter( 'cf_video_overlays_allowed_providers', array( $this, 'allowed_sources' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		$this->modal_forms = array();
	}

	/**
	 * Filter Callback for allowed sources
	 *
	 * @param $allowed
	 *
	 * @return array]
	 */
	function allowed_sources( $allowed ) {
		$allowed[] = 'youtube';
		return $allowed;
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
			$allowed[ self::ID_ATT ] = $atts[ self::ID_ATT ];
			if( isset( $allowed[ self::SOURCE_ATT ] ) && cf_video_overlays_allowed_provider( $allowed[ self::SOURCE_ATT ] )  ){
				$allowed[ self::SOURCE_ATT ] = $atts[ self::SOURCE_ATT ];
			}else{
				$allowed[ self::SOURCE_ATT ] = 'youtube';
			}

			if( 'caldera_form_modal' === $shortcode && ! empty(  $atts[ 'id' ] ) ){
				$this->modal_forms[ Caldera_Forms_Render_Util::get_current_form_count() ] = $atts[ 'id' ];
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

		if( isset( $atts[ self::ID_ATT ] ) || $this->is_modal_form_to_load( $form[ 'ID' ] ) ){

			remove_filter( 'caldera_forms_pre_render_form', array( $this, 'maybe_load' ), 10 );

			if( 'youtube' == $atts['video_source'] ) {
				wp_enqueue_script( 'youtube-iframe-api' );
			}
			wp_enqueue_script( 'cf_video_overlay_script-' . $atts['video_source'] );
			wp_enqueue_style( 'cf_video_overlay_style' );

			$output = cf_video_overlays_render( $atts, $this->is_modal_form_to_load( $atts[ 'id'] ) );

		}

		return $output;
	}

	/**
	 * Check if is a modal form we should use
	 *
	 * @since 0.0.2
	 *
	 * @param string $form_id Form ID
	 *
	 * @return bool
	 */
	protected function is_modal_form_to_load( $form_id ){
		$current = Caldera_Forms_Render_Util::get_current_form_count();
		if( isset( $this->modal_forms[ $current ] ) && $this->modal_forms[ $current ] == $form_id ){
			return true;
		}
		return false;
	}

	/**
	 * Load needed JavaScript for the player
	 *
	 * @since 0.0.1
	 */
	public function register_scripts(){
		$allowed = cf_video_overlays_allowed_providers();
		foreach( $allowed as $key => $value ) {
			wp_register_script( 'cf_video_overlay_script-' . $value, plugin_dir_url( __FILE__ ) . '/video-templates/scripts/cf-source-' . $value . '.js', array('jquery'), null, 'all' );
		}
		wp_register_style( 'cf_video_overlay_style', plugin_dir_url( __FILE__ ) . '/video-templates/styles/cf-video-overlay-styles.css', array(), null, 'all' );

		$protocol = 'https';
		if( ! is_ssl() ){
			$protocol = 'http';
		}

		wp_register_script( 'youtube-iframe-api', "$protocol://www.youtube.com/iframe_api", array( 'cf_video_overlay_script-youtube' ), null, false );

	}


}