<?php
/**
 * Uninstaller
 *
 * Uninstall the plugin by removing any options from the database
 *
 * @package Artiss-Transient Cleaner
 */

// If the uninstall was not called by WordPress, exit.

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Delete options.

delete_option( 'transient_clean_expired' );
delete_option( 'transient_clean_all' );

// Remove the daily housekeeping hook.

wp_clear_scheduled_hook( 'housekeep_transients' );
