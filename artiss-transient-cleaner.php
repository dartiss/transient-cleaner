<?php
/**
 * Transient Cleaner
 *
 * @package           Artiss-Transient-Cleaner
 * @author            David Artiss
 * @license           GPL-2.0-or-later
 *
 * Plugin Name:       Transient Cleaner
 * Plugin URI:        https://wordpress.org/plugins/artiss-transient-cleaner/
 * Description:       🧼 Clear expired transients from your options table.
 * Version:           1.6
 * Requires at least: 4.4
 * Requires PHP:      7.4
 * Author:            David Artiss
 * Author URI:        https://artiss.blog
 * Text Domain:       artiss-transient-cleaner
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

// Define global to hold the plugin base file name.

if ( ! defined( 'TRANSIENT_CLEANER_PLUGIN_BASE' ) ) {
	define( 'TRANSIENT_CLEANER_PLUGIN_BASE', plugin_basename( __FILE__ ) );
}

$functions_dir = plugin_dir_path( __FILE__ ) . 'includes/';

// Include all the various functions.

require_once $functions_dir . 'clean-transients.php';      // General configuration set-up.

require_once $functions_dir . 'shared-functions.php';      // Assorted shared functions.

if ( is_admin() ) {

	include_once $functions_dir . 'set-admin-config.php';  // Administration configuration.

}
