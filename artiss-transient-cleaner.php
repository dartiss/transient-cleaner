<?php
/*
Plugin Name: Transient Cleaner
Plugin URI: https://github.com/dartiss/transient-cleaner
Description: Housekeep expired transients from your options table.
Version: 1.5.4
Author: David Artiss
Author URI: https://artiss.blog
Text Domain: artiss-transient-cleaner
*/

/**
* Artiss Transient Cleaner
*
* Main code - include various functions
*
* @package	Artiss-Transient-Cleaner
* @since	1.2
*/

$functions_dir = plugin_dir_path( __FILE__ ) . 'includes/';

// Include all the various functions

include_once( $functions_dir . 'clean-transients.php' );     			// General configuration set-up

include_once( $functions_dir . 'shared-functions.php' );     			// Assorted shared functions

if ( is_admin() ) {

	include_once( $functions_dir . 'set-admin-config.php' );			// Administration configuration

}
?>