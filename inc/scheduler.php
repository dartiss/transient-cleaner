<?php
/**
 * Scheduler
 *
 * Set up scheduler and hooks for performing the cleaning process.
 *
 * @package artiss-transient-cleaner
 */

// Exit if accessed directly.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clean Expired Transients
 *
 * Hook into scheduled deletions and clear down expired transients
 *
 * @return string  Number of transients removed
 */
function transient_cleaner_clean_transients() {

	$cleaned = 0;

	// Only perform clean if enabled.

	$options = transient_cleaner_get_options();

	if ( $options['clean_enable'] ) {
		$cleaned = transient_cleaner_transient_delete( false );
	}

	// Return number of cleaned transients.

	return $cleaned;
}

add_action( 'housekeep_transients', 'transient_cleaner_clean_transients' );

/**
 * Set cleaning schedule
 *
 * Set up scheduler for transient cleaning
 */
function transient_cleaner_set_up_scheduler() {

	global $_wp_using_ext_object_cache;

	// Check for conditions under which the scheduler requires settings up.

	if ( ! wp_next_scheduled( 'housekeep_transients' ) && ! wp_installing() && ! $_wp_using_ext_object_cache ) {
		$schedule = true;
	} else {
		$schedule = false;
	}

	// Set up schedule, if required.

	if ( $schedule ) {
		$options = transient_cleaner_get_options();
		transient_cleaner_set_schedule( $options['schedule'] );
	}
}

add_action( 'init', 'transient_cleaner_set_up_scheduler' );

/**
 * Set scheduler
 *
 * Set up scheduler.
 *
 * @param  string $hour   The hour to be scheduled.
 * @return string         The hour parameter that was used
 */
function transient_cleaner_set_schedule( $hour ) {

	// If the hour to be set is before now, setting it will cause the schedule to run immediately
	// Therefore, in this case it's set to that time for tomorrow.

	$hour .= ':00';
	if ( $hour <= gmdate( 'H' ) ) {
		$hour .= ' tomorrow';
	}

	// Now create the scheduled event.

	wp_schedule_event( strtotime( $hour ), 'daily', 'housekeep_transients' );

	return $hour;
}

/**
 * Clear All Transients
 *
 * Hook into database upgrade and clear transients
 *
 * @return string   Number of transients removed
 */
function transient_cleaner_clear_transients() {

	$cleared = 0;

	// Only perform clear if enabled.

	$options = transient_cleaner_get_options();

	if ( $options['upgrade_enable'] ) {
		$cleared = transient_cleaner_transient_delete( true );
	}

	// Return number of cleared transients.

	return $cleared;
}

add_action( 'after_db_upgrade', 'transient_cleaner_clear_transients' );
add_action( 'clear_all_transients', 'transient_cleaner_clear_transients' );
