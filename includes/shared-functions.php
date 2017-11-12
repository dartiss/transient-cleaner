<?php
/**
* Get options
*
* Fetch options and, if any are missing, complete the data
*
* @package	Artiss-Transient-Cleaner
* @since	1.2
*
* @return   string			Array of options
*/

function tc_get_options() {

	if ( defined( 'TC_LITE' ) && TC_LITE ) { $lite = true; } else { $lite = false; }

	if ( !$lite ) { $options = get_option( 'transient_clean_options' ); }

	// If options don't exist, create an empty array

	if ( !is_array( $options ) ) { $options = array(); }

	// Because of upgrading, check each option - if not set, apply default

	$default_array = array(
						'clean_enable' => true,
						'clean_optimize' => false,
						'upgrade_enable' => true,
						'upgrade_optimize' => true,
						'schedule'  => '00'
						);

	// Merge existing and default options - any missing from existing will take the default settings

	$new_options = array_merge( $default_array, $options );

	// Update the options, if changed, and return the result

	if ( $options != $new_options && !$lite ) { update_option( 'transient_clean_options', $new_options ); }

	return $new_options;
}

/**
* Set scheduler
*
* Set up scheduler. Set up seperately as this is done from 2 difference places
* and wanted to keep a consistent process.
*
* @since	1.4
*
* @params	string		$hour		The hour to be scheduled
* @return	string					The hour parameter that was used
*/

function tc_set_schedule( $hour ) {

	// If the hour to be set is before now, setting it will cause the schedule to run immediately
	// Therefore, in this case it's set to that time for tomorrow

	$hour .= ':00';
	if ( $hour <= date( 'H' ) ) { $hour .= ' tomorrow'; }

	// Now create the scheduled event

	wp_schedule_event( strtotime( $hour ) , 'daily', 'housekeep_transients' );

	return $hour;

}
?>