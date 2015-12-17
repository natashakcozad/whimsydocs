<?php
/*
 * Plugin Name: Whimsy+Docs
 * Version: 1.0.1
 * Plugin URI: http://www.thefanciful.com/plugins/whimsy/docs
 * Description: A beautiful set of lightweight docs, part of the Whimsy Framework.
 * Author: The Fanciful
 * Author URI: http://www.thefanciful.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: whimsy-sc
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author The Fanciful
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-whimsy-docs.php' );

/**
 * Returns the main instance of Whimsy_Docs to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Whimsy_Docs
 */
function Whimsy_Docs () {
	$instance = Whimsy_Docs::instance( __FILE__, '1.0.1' );

	return $instance;
}

Whimsy_Docs();