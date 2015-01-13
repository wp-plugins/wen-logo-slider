<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://wenthemes.com
 * @since      1.0.0
 *
 * @package    WEN_Logo_Slider
 * @subpackage WEN_Logo_Slider/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    WEN_Logo_Slider
 * @subpackage WEN_Logo_Slider/public
 * @author     WEN Themes <info@wenthemes.com>
 */
class WEN_Logo_Slider_Public {

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
	 * @var      string    $plugin_name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name . '-owl-carousel', WEN_LOGO_SLIDER_URL . '/vendors/owl-carousel/owl.carousel.css', array(), '1.3.2', 'all' );
		wp_enqueue_style( $this->plugin_name . '-owl-theme', WEN_LOGO_SLIDER_URL . '/vendors/owl-carousel/owl.theme.css', array(), '1.3.2', 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wen-logo-slider-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name . '-owl-carousel', WEN_LOGO_SLIDER_URL . '/vendors/owl-carousel/owl.carousel.min.js', array( 'jquery' ), '1.3.2', false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wen-logo-slider-public.js', array( 'jquery', $this->plugin_name . '-owl-carousel' ), $this->version, true );

	}

	public function custom_post_types(){

		// Register Slider Post Type
		$labels = array(
			'name'               => _x( 'Sliders', 'post type general name', 'wen-logo-slider' ),
			'singular_name'      => _x( 'Slider', 'post type singular name', 'wen-logo-slider' ),
			'menu_name'          => _x( 'Slider', 'admin menu', 'wen-logo-slider' ),
			'name_admin_bar'     => _x( 'Slider', 'add new on admin bar', 'wen-logo-slider' ),
			'add_new'            => _x( 'Add New', WEN_LOGO_SLIDER_POST_TYPE_LOGO_SLIDER, 'wen-logo-slider' ),
			'add_new_item'       => __( 'Add New Slider', 'wen-logo-slider' ),
			'new_item'           => __( 'New Slider', 'wen-logo-slider' ),
			'edit_item'          => __( 'Edit Slider', 'wen-logo-slider' ),
			'view_item'          => __( 'View Slider', 'wen-logo-slider' ),
			'all_items'          => __( 'All Sliders', 'wen-logo-slider' ),
			'search_items'       => __( 'Search Sliders', 'wen-logo-slider' ),
			'parent_item_colon'  => __( 'Parent Sliders:', 'wen-logo-slider' ),
			'not_found'          => __( 'No sliders found.', 'wen-logo-slider' ),
			'not_found_in_trash' => __( 'No sliders found in Trash.', 'wen-logo-slider' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => false,
			// 'rewrite'            => array( 'slug' => 'logo-slider' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_icon'          => 'dashicons-images-alt2',
			'supports'           => array( 'title' )
		);

		register_post_type( WEN_LOGO_SLIDER_POST_TYPE_LOGO_SLIDER, $args );

	}


}
