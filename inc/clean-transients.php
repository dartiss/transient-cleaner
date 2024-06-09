<?php
/**
 * Clean Transients
 *
 * Functions to clear down transient data
 *
 * @package artiss-transient-cleaner
 */

// Exit if accessed directly.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delete Transients
 *
 * Shared function that will clear down requested transients
 *
 * @param   string $clear_all  TRUE or FALSE, whether to clear all transients or not.
 * @return  string             Number of removed transients
 */
function transient_cleaner_transient_delete( $clear_all ) {

	$cleaned = 0;

	global $_wp_using_ext_object_cache;

	if ( ! $_wp_using_ext_object_cache ) {

		$options = transient_cleaner_get_options();

		global $wpdb;
		$records = 0;

		// Build and execute required SQL.

		if ( $clear_all ) {

			// Clean from options table.

			$clean    = $wpdb->query(
				$wpdb->prepare(
					"DELETE FROM $wpdb->options
					WHERE option_name LIKE %s",
					$wpdb->esc_like( '_transient_' ) . '%'
				)
			);
			$records .= $clean;

			// If multisite, and the main network, also clear the sitemeta table.

			if ( is_multisite() && is_main_network() ) {
				$clean    = $wpdb->query(
					$wpdb->prepare(
						"DELETE FROM $wpdb->sitemeta
						WHERE meta_key LIKE %s",
						$wpdb->esc_like( '_site_transient_' ) . '%'
					)
				);
				$records .= $clean;
			}
		} else {

			// Delete transients from options table.

			$clean    = $wpdb->query(
				$wpdb->prepare(
					"DELETE a, b FROM {$wpdb->options} a, {$wpdb->options} b
					WHERE a.option_name LIKE %s
					AND a.option_name NOT LIKE %s
					AND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, CHAR_LENGTH('_transient_') + 1 ) )
					AND b.option_value < %d",
					$wpdb->esc_like( '_transient_' ) . '%',
					$wpdb->esc_like( '_transient_timeout_' ) . '%',
					time()
				)
			);
			$records .= $clean;

			// Delete transients from multisite, if configured as such.

			if ( is_multisite() && is_main_network() ) {

				$clean    = $wpdb->query(
					$wpdb->prepare(
						"DELETE a, b FROM {$wpdb->sitemeta} a, {$wpdb->sitemeta} b
						WHERE a.meta_key LIKE %s
						AND a.meta_key NOT LIKE %s
						AND b.meta_key = CONCAT( '_site_transient_timeout_', SUBSTRING( a.meta_key, CHAR_LENGTH('_site_transient_') + 1 ) )
						AND b.meta_value < %d",
						$wpdb->esc_like( '_site_transient_' ) . '%',
						$wpdb->esc_like( '_site_transient_timeout_' ) . '%',
						time()
					)
				);
				$records .= $clean;
			}
		}

		// Save options field with number & timestamp.

		$results = array();

		$results['timestamp'] = time() + ( get_option( 'gmt_offset' ) * 3600 );
		$results['records']   = $records;

		$option_name = 'transient_clean_';
		if ( $clear_all ) {
			$option_name .= 'all';
		} else {
			$option_name .= 'expired';
		}
		update_option( $option_name, $results );

		// Optimize the table after the deletions.

		if ( ( ( $options['upgrade_optimize'] ) && ( $clear_all ) ) || ( ( $options['clean_optimize'] ) && ( ! $clear_all ) ) ) {
			$wpdb->query( "OPTIMIZE TABLE $wpdb->options" );
			if ( is_multisite() && is_main_network() ) {
				$wpdb->query( "OPTIMIZE TABLE $wpdb->sitemeta" );
			}
		}
	}

	return $cleaned;
}
