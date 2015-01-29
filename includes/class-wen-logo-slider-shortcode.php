<?php

/**
 * The file that defines shortcode
 *
 * @link       http://wenthemes.com
 * @since      1.0.0
 *
 * @package    WEN_Logo_Slider
 * @subpackage WEN_Logo_Slider/includes
 */

/**
 * Shortcode class.
 *
 * This class contains shortcode stuff.
 *
 * @since      1.0.0
 * @package    WEN_Logo_Slider
 * @subpackage WEN_Logo_Slider/includes
 * @author     WEN Themes <info@wenthemes.com>
 */
class WEN_Logo_Slider_Shortcode {

  public function init() {

    add_shortcode( 'WLS', array( $this, 'wen_logo_slider_shortcode_callback' ) );

  }

  private function check_if_valid_slider($args){

    $output = false;
    if ( isset($args['id']) && intval( $args['id'] ) > 0  ) {

      $slider = get_post(intval($args['id']));

      if ( ! empty( $slider ) && WEN_LOGO_SLIDER_POST_TYPE_LOGO_SLIDER == $slider->post_type ) {
        $output = true;
      }
    }
    return $output;

  }

  function wen_logo_slider_shortcode_callback( $atts, $content = "" ){

    $atts = shortcode_atts( array(
        'id' => '',
    ), $atts, 'WLS' );

    $atts['id'] = absint($atts['id']);

    $is_valid_slider = $this->check_if_valid_slider($atts);

    if ( ! $is_valid_slider ) {
      return __( 'Slider not found', 'wen-logo-slider' );
    }

    ob_start();
    ?>

    <?php
      $slides = get_post_meta($atts['id'],'_wls_slides',true);
     ?>
     <?php if ( ! empty( $slides ) ): ?>

      <?php
       $slider_settings = get_post_meta($atts['id'],'wen_logo_slider_settings',true);
       $defaults = array(
        'slider_delay'            => 4,
        'transition_time'         => 1,
        'image_size'              => 'thumbnail',
        'images_per_slide'        => 5,
        'enable_navigation_arrow' => 0,
        'enable_random_order'     => 0,
        );
       $slider_settings = array_merge( $defaults, $slider_settings );
       $slider_settings['random_id'] = uniqid(esc_attr($atts['id']).'-');
       ?>
       <?php
        if ( 1 == $slider_settings['enable_random_order'] ){
          // Shuffle slider images
          shuffle( $slides );
        }
       ?>

      <div id="wls-carousel-<?php echo esc_attr($slider_settings['random_id']);?>" class="owl-carousel wls-carousel">

      <?php foreach ($slides as $key => $slide): ?>

        <?php if (empty($slide['slide_image_id'])): ?>
          <?php continue; ?>
        <?php endif ?>

        <div>
        <?php
          $attachment = get_post($slide['slide_image_id']);
          if ( empty( $attachment ) ) {
            continue;
          }
          $image_info = wp_get_attachment_image_src( $attachment->ID, $slider_settings['image_size'] );
          $image_url  = array_shift($image_info);
          $link_open  = '';
          $link_close = '';
          if ( ! empty( $slide['url'] ) ) {
            $link_open = '<a href="'.esc_url( $slide['url'] ).'"';
            if ( 'yes' == $slide['slide_new_window'] ) {
              $link_open .= ' target="_blank" ';
            }
            if ( ! empty( $slide['title'] ) ) {
              $link_open .= ' title="' . esc_attr( $slide['title'] ) . '" ';
            }
            $link_open .='>';
            $link_close = '</a>';
          }

         ?>

         <?php echo $link_open; ?>
         <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $slide['title'] ); ?>" title="<?php echo esc_attr( $slide['title'] ); ?>" />
         <?php echo $link_close; ?>


        </div>

      <?php endforeach ?>

      </div> <!-- owl-carousel -->

      <?php echo $this->get_slider_script( $atts, $slider_settings ); ?>


     <?php endif ?>

    <?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;

  }

  function get_slider_script( $args, $settings ){

    ob_start();
    ?>
    <script>
    jQuery(document).ready(function($){

      $("#wls-carousel-<?php echo esc_attr( $settings['random_id'] ); ?>").owlCarousel({
        items: <?php echo esc_attr( $settings['images_per_slide'] ); ?>,
        pagination: false,
        navigation: <?php echo ($settings['enable_navigation_arrow'])? 'true':'false'; ?>,
        navigationText : false,
        stopOnHover : true,
        paginationSpeed: <?php echo esc_attr( $settings['transition_time'] ) * 1000 ; ?>,
        autoPlay: <?php echo esc_attr( $settings['slider_delay'] ) * 1000 ; ?>
      });

    });
    </script>

    <?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;


  }

}
