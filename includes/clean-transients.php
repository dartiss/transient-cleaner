<?php
/**
* Clean Transients
*
* Functions to clear down transient data
*
* @package	Artiss-Transient-Cleaner
*/

/**
* Clean Expired Transients
*
* Hook into scheduled deletions and clear down expired transients
*
* @since	1.0
*
* @return	string		Number of transients removed
*/

function tc_clean_transients() {

	$cleaned = 0;

	// Only perform clean if enabled

	$options = tc_get_options();

	if ( $options[ 'clean_enable' ] ) { $cleaned = tc_transient_delete( false ); }

	// Return number of cleaned transients

	return $cleaned;
}

add_action( 'housekeep_transients', 'tc_clean_transients' );

/**
* Set housekeeping schedule
*
* Set up scheduler for housekeeping
*
* @since	1.4
*/

function tc_set_up_scheduler() {

	// Check for conditions under which the scheduler requires settings up

	if ( !wp_next_scheduled( 'housekeep_transients' ) && !wp_installing() ) { $schedule = true; } else { $schedule = false; }

	// Set up schedule, if required

	if ( $schedule ) {
		$options = tc_get_options();
		tc_set_schedule( $options[ 'schedule' ] );
	}
}

add_action( 'init', 'tc_set_up_scheduler' );

/**
* Clear All Transients
*
* Hook into database upgrade and clear transients
*
* @since	1.0
*
* @return	string		Number of transients removed
*/

function tc_clear_transients() {

	$cleared = 0;

	// Only perform clear if enabled

	$options = tc_get_options();

	if ( $options[ 'upgrade_enable' ] ) { $cleared = tc_transient_delete( true ); }

	// Return number of cleared transients

	return $cleared;

}

add_action( 'after_db_upgrade', 'tc_clear_transients' );
add_action( 'clear_all_transients', 'tc_clear_transients' );

/**
* Delete Transients
*
* Shared function that will clear down requested transients
*
* @since	1.0
*
* @param	string	$expired	TRUE or FALSE, whether to clear all transients or not
* @return	string				Number of removed transients
*/

function tc_transient_delete( $clear_all ) {

	$cleaned = 0;

	global $_wp_using_ext_object_cache;

	if ( !$_wp_using_ext_object_cache ) {

		$options = tc_get_options();

		global $wpdb;
		$records = 0;

		// Build and execute required SQL

		if ( $clear_all ) {

			// Clean from options table

			$sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '%_transient_%'";
			$clean = $wpdb -> query( $sql );
			$records .= $clean;

			// If multisite, and the main network, also clear the sitemeta table

			if ( is_multisite() && is_main_network() ) {
				$sql = "DELETE FROM $wpdb->sitemeta WHERE meta_key LIKE '_site_transient_%'";
				$clean = $wpdb -> query( $sql );
				$records .= $clean;
			}

		} else {

			// Delete transients from options table

			$sql = "
				DELETE
					a, b
				FROM
					{$wpdb->options} a, {$wpdb->options} b
				WHERE
					a.option_name LIKE '%_transient_%' AND
					a.option_name NOT LIKE '%_transient_timeout_%' AND
					b.option_name = CONCAT(
						'_transient_timeout_',
						SUBSTRING(
							a.option_name,
							CHAR_LENGTH('_transient_') + 1
						)
					)
				AND b.option_value < UNIX_TIMESTAMP()
			";

			$clean = $wpdb -> query( $sql );
			$records .= $clean;

			// Delete transients from multisite, if configured as such

			if ( is_multisite() && is_main_network() ) {

				$sql = "
					DELETE
						a, b
					FROM
						{$wpdb->sitemeta} a, {$wpdb->sitemeta} b
					WHERE
						a.meta_key LIKE '_site_transient_%' AND
						a.meta_key NOT LIKE '_site_transient_timeout_%' AND
						b.meta_key = CONCAT(
							'_site_transient_timeout_',
							SUBSTRING(
								a.meta_key,
								CHAR_LENGTH('_site_transient_') + 1
							)
						)
					AND b.meta_value < UNIX_TIMESTAMP()
				";

				$clean = $wpdb -> query( $sql );
				$records .= $clean;
			}
		}

		// Save options field with number & timestamp

		$results[ 'timestamp' ] = time() + ( get_option( 'gmt_offset' ) * 3600 );
		$results[ 'records' ] = $records;

		$option_name = 'transient_clean_';
		if ( $clear_all ) { $option_name .= 'all'; } else { $option_name .= 'expired'; }
		update_option( $option_name, $results );

		// Optimize the table after the deletions

		if ( ( ( $options[ 'upgrade_optimize' ] ) && ( $clear_all ) ) or ( ( $options[ 'clean_optimize' ] ) && ( !$clear_all ) ) ) {
			$wpdb -> query( "OPTIMIZE TABLE $wpdb->options" );
			if ( is_multisite() && is_main_network() ) { $wpdb -> query( "OPTIMIZE TABLE $wpdb->sitemeta" ); }
		}
	}

	return $cleaned;
}
?>