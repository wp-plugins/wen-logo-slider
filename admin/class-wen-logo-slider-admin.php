<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://wenthemes.com
 * @since      1.0.0
 *
 * @package    WEN_Logo_Slider
 * @subpackage WEN_Logo_Slider/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @package    WEN_Logo_Slider
 * @subpackage WEN_Logo_Slider/admin
 * @author     WEN Themes <info@wenthemes.com>
 */
class WEN_Logo_Slider_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		$screen = get_current_screen();
		if ( WEN_LOGO_SLIDER_POST_TYPE_LOGO_SLIDER == $screen->id ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wen-logo-slider-admin.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();
		if ( WEN_LOGO_SLIDER_POST_TYPE_LOGO_SLIDER == $screen->id ) {

			wp_enqueue_script('jquery-ui-sortable');

			wp_enqueue_media();

			wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wen-logo-slider-admin.js', array( 'jquery' ), $this->version, false );
			$extra_array = array(
				'lang' => array(
					'are_you_sure'       => __( 'Are you sure?', 'wen-logo-slider' ),
					'yes'                => __( 'Yes', 'wen-logo-slider' ),
					'no'                 => __( 'No', 'wen-logo-slider' ),
					'remove'             => __( 'Remove', 'wen-logo-slider' ),
					'image'              => __( 'Image', 'wen-logo-slider' ),
					'upload'             => __( 'Upload', 'wen-logo-slider' ),
					'insert'             => __( 'Insert', 'wen-logo-slider' ),
					'select'             => __( 'Select', 'wen-logo-slider' ),
					'select_image'       => __( 'Select Image', 'wen-logo-slider' ),
					'title'              => __( 'Title', 'wen-logo-slider' ),
					'enter_title'        => __( 'Enter Title', 'wen-logo-slider' ),
					'url'                => __( 'URL', 'wen-logo-slider' ),
					'enter_url'          => __( 'Enter URL', 'wen-logo-slider' ),
					'open_in_new_window' => __( 'Open in new window', 'wen-logo-slider' ),
				),
			);
			wp_localize_script( $this->plugin_name, 'WLS_OBJ', $extra_array );
			wp_enqueue_script( $this->plugin_name );

		}

	}

	function add_slider_meta_boxes(){

		$screens = array( WEN_LOGO_SLIDER_POST_TYPE_LOGO_SLIDER );

		foreach ( $screens as $screen ) {

			add_meta_box(
				'wen_logo_slider_content_id',
				__( 'Slides', 'wen-logo-slider' ),
				array($this,'slides_meta_box_callback'),
				$screen,
				'normal',
				'high'
			);
			add_meta_box(
				'wen_logo_slider_usage_content_id',
				__( 'Usage', 'wen-logo-slider' ),
				array( $this, 'usage_meta_box_callback' ),
				$screen,
				'side'
			);
			add_meta_box(
				'wen_logo_slider_settings_id',
				__( 'Slider Settings', 'wen-logo-slider' ),
				array($this,'settings_meta_box_callback'),
				$screen,
				'side',
				'high'
			);

		}

	}

	function settings_meta_box_callback( $post ){

		$wls_settings = get_post_meta( $post->ID, 'wen_logo_slider_settings', true );

		if ( empty( $wls_settings ) ) {
			$wls_settings = array();
		}

		$defaults = array(
			'slider_delay'            => 4,
			'transition_time'         => 1,
			'image_size'              => 'thumbnail',
			'images_per_slide'        => 5,
			'enable_navigation_arrow' => 0,
			'enable_random_order'     => 0,
			);
		$settings_args = array_merge( $defaults, $wls_settings );

		?>

		<?php wp_nonce_field( plugin_basename( __FILE__ ), 'wen_logo_slider_settings_nonce' ); ?>

		<p><strong><?php _e( 'Slider Delay', 'wen-logo-slider' ); ?></strong>&nbsp;<span class="description">(<?php _e( 'in seconds', 'wen-logo-slider' ); ?>)</span></p>
		<input type="text" name="wen_logo_slider_settings[slider_delay]" value="<?php echo esc_attr( $settings_args['slider_delay'] ); ?>" />
		<p><strong><?php _e( 'Transition Time', 'wen-logo-slider' ); ?></strong>&nbsp;<span class="description">(<?php _e( 'in seconds', 'wen-logo-slider' ); ?>)</span></p>
		<input type="text" name="wen_logo_slider_settings[transition_time]" value="<?php echo esc_attr( $settings_args['transition_time'] ); ?>" />
		<p><strong><?php _e( 'Images per Slide', 'wen-logo-slider' ); ?></strong>&nbsp;<span class="description">(<?php
			echo sprintf( __( 'number between %d-%d', 'wen-logo-slider' ), 1, 9) ;
		?>)</span></p>
		<input type="text" name="wen_logo_slider_settings[images_per_slide]" value="<?php echo esc_attr( $settings_args['images_per_slide'] ); ?>" />
		<?php
		$image_sizes = $this->get_image_sizes();
		?>
		<p><strong><?php _e( 'Image Size', 'wen-logo-slider' ); ?></strong></p>
		<select name="wen_logo_slider_settings[image_size]" >
			<?php foreach ($image_sizes as $key => $size): ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $settings_args['image_size'], $key ); ?>><?php echo esc_attr( $key ); ?><?php echo ' ('.$size['width'] . 'x'.$size['height'] . ')'; ?></option>
			<?php endforeach ?>
		</select>
		<p><strong><?php _e( 'Navigation Arrow', 'wen-logo-slider' ); ?></strong></p>
		<input type="hidden" name="wen_logo_slider_settings[enable_navigation_arrow]" value="0" />
		<input type="checkbox" name="wen_logo_slider_settings[enable_navigation_arrow]" value="1" <?php checked( $settings_args['enable_navigation_arrow'], 1, true); ?> />
		<span class="small"><?php _e( 'Enable', 'wen-logo-slider' ); ?></span>
		<p><strong><?php _e( 'Random Order', 'wen-logo-slider' ); ?></strong></p>
		<input type="hidden" name="wen_logo_slider_settings[enable_random_order]" value="0" />
		<input type="checkbox" name="wen_logo_slider_settings[enable_random_order]" value="1" <?php checked( $settings_args['enable_random_order'], 1, true); ?> />
		<span class="small"><?php _e( 'Enable', 'wen-logo-slider' ); ?></span>
		<?php

	}

	private function get_image_sizes(){

		global $_wp_additional_image_sizes;
		$sizes = array();
    $get_intermediate_image_sizes = get_intermediate_image_sizes();

    // Create the full array with sizes and crop info
    foreach( $get_intermediate_image_sizes as $_size ) {

      if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

        $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
        $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
        $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );

      } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

        $sizes[ $_size ] = array(
                'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
        );

      }

    }

		return $sizes;
	}

	function slides_meta_box_callback( $post ){

		?>

		<?php wp_nonce_field( plugin_basename( __FILE__ ), 'wen_logo_slider_slides_nonce' ); ?>

		<div id="main-slides-list-wrap">

			<?php
				$slides = get_post_meta( $post->ID, '_wls_slides', true ) ;
			 ?>
			 <?php if ( ! empty( $slides ) ): ?>

			 	<?php foreach ($slides as $key => $slide): ?>

						<div class="slide-item-wrap">
							<div class="slide-item-left">
								<div class="wls-form-row">
									<?php

										$thumbnail_url = '';
										$thumbnail_id = $slide['slide_image_id'];
										if ($thumbnail_id) {
											$thumbnail_url = wp_get_attachment_thumb_url( $thumbnail_id );
										}
										$upload_button_status = ' style="display:none;" ';
										if ( empty( $thumbnail_url ) ) {
											$upload_button_status = '';
										}

									?>

									<input type="hidden" name="slide_image_id[]" value="<?php echo esc_attr( $slide['slide_image_id'] ); ?>" class="wls-slide-image-id" />
									<input type="button" class="wls-select-img button button-primary" value="<?php _e( 'Upload', 'wen-logo-slider' ); ?>" data-uploader_button_text="<?php _e( 'Select', 'wen-logo-slider' );?>" data-uploader_title="<?php _e( 'Select Image', 'wen-logo-slider' );?>" <?php echo $upload_button_status; ?>/>

									<?php
										$style_text="display:none;";
										if ( !empty($thumbnail_url)) {
											$style_text = '';
										}
									 ?>

									<div class="image-preview-wrap" style="<?php echo $style_text; ?>" >
										<img class="img-preview" alt="<?php _e( 'Preview', 'wen-logo-slider' ); ?>" src="<?php echo $thumbnail_url; ?>" />
										<a href="#" class="btn-wls-remove-image-upload">
											<span class="dashicons dashicons-dismiss"></span>
										</a>
									</div>

								</div>

							</div>
							<div class="slide-item-right">
								<div class="wls-form-row">
									<i class="dashicons dashicons-editor-textcolor"></i>
									<input type="text" name="slide_title[]" value="<?php echo esc_attr( $slide['title'] ); ?>" placeholder="<?php _e( 'Enter Title', 'wen-logo-slider' ); ?>" class="txt-slide-title regular-text code" />
									<span class="description"><?php _e( 'Enter Title', 'wen-logo-slider' ); ?></span>
								</div>
								<div class="wls-form-row">
									<i class="dashicons dashicons-admin-site"></i>

									<input type="text" name="slide_url[]" value="<?php echo esc_url( $slide['url'] ); ?>" placeholder="<?php _e( 'Enter URL', 'wen-logo-slider' ); ?>" class="txt-slide-url regular-text code" />
									<span class="description"><?php _e( 'Enter URL', 'wen-logo-slider' ); ?></span>
								</div>
								<div class="wls-form-row">
									<i class="dashicons dashicons-share-alt2"></i>
									<select name="slide_new_window[]">
										<option value="yes" <?php selected( $slide['slide_new_window'], 'yes' ); ?>><?php _e( 'Yes', 'wen-logo-slider' ); ?></option>
										<option value="no" <?php selected( $slide['slide_new_window'], 'no' ); ?>><?php _e( 'No', 'wen-logo-slider' ); ?></option>
									</select>
									<span class="description"><?php _e( 'Open in new window', 'wen-logo-slider' ); ?></span>

								</div>

								<input type="button" value="<?php  esc_attr( _e( 'Remove', 'wen-logo-slider' ) ); ?>" class="button btn-remove-slide-item"/>

							</div>




						</div> <!-- .slide-item-wrap -->

			 	<?php endforeach ?>

			 <?php endif ?>

		</div><!-- #main-slides-list-wrap -->
		<p><input type="button" value="<?php  esc_attr( _e( 'Add New Slide', 'wen-logo-slider' ) ); ?>" class="button button-primary btn-add-slide-item" /></p>
		<?php

	}
	function usage_meta_box_callback( $post ){

		?>
		<h4><?php _e( 'Shortcode', 'wen-logo-slider' ); ?></h4>
		<p><?php _e( 'Copy and paste this shortcode directly into any WordPress post or page.', 'wen-logo-slider' ); ?></p>
		<textarea class="large-text code" readonly="readonly"><?php echo '[WLS id="'.$post->ID.'"]'; ?></textarea>

		<h4><?php _e( 'Template Include', 'wen-logo-slider' ); ?></h4>
		<p><?php _e( 'Copy and paste this code into a template file to include the slider within your theme.', 'wen-logo-slider' ); ?></p>
		<textarea class="large-text code" readonly="readonly">&lt;?php echo do_shortcode("[WLS id='<?php echo $post->ID; ?>']"); ?&gt;</textarea>
		<?php

	}


	function save_settings_meta_box( $post_id ){

		if ( WEN_LOGO_SLIDER_POST_TYPE_LOGO_SLIDER != get_post_type( $post_id ) ) {
			return $post_id;
		}

		// Bail if we're doing an auto save
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		// if our nonce isn't there, or we can't verify it, bail
		if ( !isset( $_POST['wen_logo_slider_settings_nonce'] ) || !wp_verify_nonce( $_POST['wen_logo_slider_settings_nonce'], plugin_basename( __FILE__ ) ) )
		    return $post_id;

		// if our current user can't edit this post, bail
		if( !current_user_can( 'edit_post' , $post_id ) )
			return $post_id;

		$refined_settings = array();
		if ( ! empty( $_POST['wen_logo_slider_settings'] ) ) {
			foreach ( $_POST['wen_logo_slider_settings'] as $key => $value) {
				$refined_settings[$key] = esc_attr($value);
				switch ( $key ) {
					case 'slider_delay':
						if( intval($value) < 1 ) {
							$refined_settings[$key] = 4;
						}
						break;
					case 'transition_time':
						if( intval($value) < 1 ) {
							$refined_settings[$key] = 1;
						}
						break;
					case 'images_per_slide':
						if( intval($value) < 1 || intval($value) > 9  ) {
							$refined_settings[$key] = 5;
						}
						break;

					default:
						# code...
						break;
				}
			}
		}
		if ( ! empty( $refined_settings ) ) {
			update_post_meta( $post_id, 'wen_logo_slider_settings', $refined_settings );
		}

	}

	function save_slides_meta_box( $post_id ){

		if ( WEN_LOGO_SLIDER_POST_TYPE_LOGO_SLIDER != get_post_type( $post_id ) ) {
			return $post_id;
		}

		// Bail if we're doing an auto save
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		// if our nonce isn't there, or we can't verify it, bail
		if ( !isset( $_POST['wen_logo_slider_slides_nonce'] ) || !wp_verify_nonce( $_POST['wen_logo_slider_slides_nonce'], plugin_basename( __FILE__ ) ) )
		    return $post_id;

		// if our current user can't edit this post, bail
		if( !current_user_can( 'edit_post' , $post_id ) )
			return $post_id;

		$slide_title_array = array();
		if ( isset( $_POST['slide_title'] ) ) {

			$slide_title_array = $_POST['slide_title'];

		}
		if ( empty( $slide_title_array ) ) {
			return;
		}
		$slides_array = array();
		$cnt = 0;
		foreach ( $slide_title_array as $key => $title ) {

			if ( empty( $title ) ) {
				continue;
			}
			$slides_array[$cnt]['title']            = sanitize_text_field( $title );
			$slides_array[$cnt]['url']              = esc_url( $_POST['slide_url'][$key] );
			$slides_array[$cnt]['slide_new_window'] = esc_attr( $_POST['slide_new_window'][$key] );
			$slides_array[$cnt]['slide_image_id']   = sanitize_text_field( $_POST['slide_image_id'][$key] );

			$cnt++;

		}
		if ( ! empty( $slides_array ) ) {
			update_post_meta( $post_id, '_wls_slides', $slides_array );
		}
		else{
			delete_post_meta( $post_id, '_wls_slides' );
		}

	}

	function usage_column_head( $columns ){

		$new_columns['cb']     = '<input type="checkbox" />';
		$new_columns['title']  = $columns['title'];
		$new_columns['id']     = _x( 'ID', 'column name', 'wen-logo-slider' );
		$new_columns['slides'] = _x( 'Slides', 'column name', 'wen-logo-slider' );
		$new_columns['usage']  = __( 'Usage', 'wen-logo-slider' );
		$new_columns['date']   = $columns['date'];
		return $new_columns;

	}

	function usage_column_content( $column_name, $post_id ){

		switch ( $column_name ) {
			case 'id':
				echo $post_id;
				break;

			case 'usage':
				echo '<code>[WLS id="' . $post_id . '"]</code>';
				break;

			case 'slides':
				$slides = get_post_meta( $post_id, '_wls_slides', true );
				echo $count = ( empty( $slides ) ) ? 0 : count( $slides ) ;
				break;

			default:
				break;
		}

	}

	public function hide_publishing_actions() {
		global $post;
		if ( WEN_LOGO_SLIDER_POST_TYPE_LOGO_SLIDER != $post->post_type ) {
			return;
		}
		?>
		<style type="text/css">
		#misc-publishing-actions,#minor-publishing-actions{
			display:none;
		}
		</style>
		<?php
		return;
	}

	function customize_row_actions( $actions, $post ){

		if ( WEN_LOGO_SLIDER_POST_TYPE_LOGO_SLIDER == $post->post_type ) {

			unset( $actions['inline hide-if-no-js'] );

		}

		return $actions;

	}

	function tinymce_button(){

		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
		     add_filter( 'mce_buttons', array($this,'register_tinymce_button' ) );
		     add_filter( 'mce_external_plugins', array($this,'add_tinymce_button' ) );
		}

	}

	function register_tinymce_button( $buttons ){

		array_push( $buttons, 'wen_logo_slider' );
		return $buttons;

	}

	function add_tinymce_button( $plugin_array ){

		$plugin_array['wen_logo_slider'] = WEN_LOGO_SLIDER_URL . '/admin/js/wen-logo-slider-tinymce-plugin.js';
		return $plugin_array;

	}

	function tinymce_external_language( $locales ){

		$locales ['wen-logo-slider'] = WEN_LOGO_SLIDER_DIR. '/admin/partials/wen-logo-slider-tinymce-plugin-langs.php';
    return $locales;

	}

	function tinymce_popup(){
		?>
		<div id="WLS-popup-form" style="display:none">
		  <div>
			<?php
			$args = array(
				'post_type'      => WEN_LOGO_SLIDER_POST_TYPE_LOGO_SLIDER,
				'posts_per_page' => -1,
				);
			$all_slides = get_posts($args);
			 ?>
			 <?php if ( ! empty($all_slides ) ): ?>
			    <p><?php _e( 'Select Slider', 'wen-logo-slider' ); ?>
			    <select name="wls-slide" id="wls-slide">
			    <?php foreach ($all_slides as $key => $slide): ?>

				    	<option value="<?php echo esc_attr( $slide->ID); ?>"><?php echo esc_attr( $slide->post_title); ?></option>

			    <?php endforeach ?>
			    </select>
			    </p>
			    <p class="submit">
			      <input type="button" id="WLS-submit" class="button-primary" value="<?php esc_attr( _e( 'Insert', 'wen-logo-slider' ) ); ?>" name="submit" />
			    </p>
			    <script type="text/javascript">

			    jQuery(document).ready(function($){
			      $('#WLS-submit').click(function(e){
			        e.preventDefault();

			        var shortcode = '[WLS';
			        var wls_slide = $('#wls-slide').val();
			        if ( '' != wls_slide) {
			          shortcode += ' id="'+wls_slide+'"';
			        }
			        shortcode += ']';

			        // inserts the shortcode into the active editor
			        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			        // closes Thickbox
			        tb_remove();

			      });
			    });

			       </script>

			 	<?php else: ?>
			 		<p><strong><?php _e( 'No slider found', 'wen-logo-slider' ); ?></strong></p>
			 <?php endif ?>

		  </div>
		</div><!-- #WLS-popup-form -->
		<?php


	}

	function html_templates(){
		?>
		<script type="text/template" id='template-wls-slider-item'>
			<div class="slide-item-wrap">
				<div class="slide-item-left">
					<div class="wls-form-row">
						<input type="hidden" name="slide_image_id[]" value="" class="wls-slide-image-id" />
						<input type="button" class="wls-select-img button button-primary" value="<?php _e( 'Upload', 'wen-logo-slider' ); ?>" data-uploader_button_text="<?php _e( 'Select', 'wen-logo-slider' );?>" data-uploader_title="<?php _e( 'Select Image', 'wen-logo-slider' );?>" />
						<div class="image-preview-wrap" style="display:none;" >
							<img class="img-preview" alt="<?php _e( 'Preview', 'wen-logo-slider' ); ?>" src="" />
							<a href="#" class="btn-wls-remove-image-upload">
								<span class="dashicons dashicons-dismiss"></span>
							</a>
						</div>

					</div>
				</div>
				<div class="slide-item-right">

					<div class="wls-form-row">
						<i class="dashicons dashicons-editor-textcolor"></i>
						<input type="text" name="slide_title[]" value="" placeholder="<?php _e( 'Enter Title', 'wen-logo-slider' ); ?>" class="txt-slide-title regular-text code" />
						<span class="description"><?php _e( 'Enter Title', 'wen-logo-slider' ); ?></span>
					</div>

					<div class="wls-form-row">
						<i class="dashicons dashicons-admin-site"></i>

						<input type="text" name="slide_url[]" value="" placeholder="<?php _e( 'Enter URL', 'wen-logo-slider' ); ?>" class="txt-slide-url regular-text code" />
						<span class="description"><?php _e( 'Enter URL', 'wen-logo-slider' ); ?></span>
					</div>

					<div class="wls-form-row">
						<i class="dashicons dashicons-share-alt2"></i>
						<select name="slide_new_window[]">
							<option value="yes"><?php _e( 'Yes', 'wen-logo-slider' ); ?></option>
							<option value="no"><?php _e( 'No', 'wen-logo-slider' ); ?></option>
						</select>
						<span class="description"><?php _e( 'Open in new window', 'wen-logo-slider' ); ?></span>

					</div>

					<input type="button" value="<?php  esc_attr( _e( 'Remove', 'wen-logo-slider' ) ); ?>" class="button btn-remove-slide-item"/>

				</div>
		</script>

		<?php
	}

	function updated_messages( $messages ){

		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		$messages[WEN_LOGO_SLIDER_POST_TYPE_LOGO_SLIDER] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Slider updated.', 'wen-logo-slider' ),
			2  => __( 'Custom field updated.', 'wen-logo-slider' ),
			3  => __( 'Custom field deleted.', 'wen-logo-slider' ),
			4  => __( 'Slider updated.', 'wen-logo-slider' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Slider restored to revision from %s', 'wen-logo-slider' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Slider created.', 'wen-logo-slider' ),
			7  => __( 'Slider saved.', 'wen-logo-slider' ),
			8  => __( 'Slider submitted.', 'wen-logo-slider' ),
			9  => sprintf(
				__( 'Slider scheduled for: <strong>%1$s</strong>.', 'wen-logo-slider' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'wen-logo-slider' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Slider draft updated.', 'wen-logo-slider' )
		);

		return $messages;

	}

}
