<?php
/**
* Uninstaller
*
* Uninstall the plugin by removing any options from the database
*
* @package	Artiss-Transient Cleaner
* @since	1.2
*/

// If the uninstall was not called by WordPress, exit

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit(); }

// Delete options

delete_option( 'transient_clean_expired' );
delete_option( 'transient_clean_all' );
?>