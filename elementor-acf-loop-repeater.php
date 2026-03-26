<?php
/*
Plugin Name: Elementor ACF Loop Repeater Addon
Description: Aggiunge un widget per mostrare repeater ACF nei Loop Grid di Elementor.
Version: 1.0
Author: Dario
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register oEmbed Widget.
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */
function register_acf_loop_repeater_widget( $widgets_manager ) {

	require_once( __DIR__ . '/widget-acf-loop-repeater.php' );

	$widgets_manager->register( new \Elementor_Acf_Loop_Repeater_Widget() );

}
add_action( 'elementor/widgets/register', 'register_acf_loop_repeater_widget' );
