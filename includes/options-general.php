<?php
/**
* General Options Page
*
* Screen for specifying general options for the plugin
*
* @package	Artiss-Transient-Cleaner
* @since	1.2
*/
?>
<div class="wrap">

<h1><?php _e( 'Transient Cleaner Options', 'artiss-transient-cleaner' ); ?></h1>

<?php
if ( ( ( !empty( $_POST[ 'Options' ] ) ) or ( !empty( $_POST[ 'Upgrade' ] ) ) or ( !empty( $_POST[ 'Clean' ] ) ) ) && ( check_admin_referer( 'transient-cleaner-options' , 'transient_cleaner_options_nonce' ) ) ) {

	$old_options = tc_get_options();

	// Assign variable contents to options array

	if ( isset( $_POST[ 'clean_enable' ] ) ) { $options[ 'clean_enable' ] = sanitize_text_field( $_POST[ 'clean_enable' ] ); } else { $options[ 'clean_enable' ] = ''; }
	if ( isset( $_POST[ 'clean_optimize' ] ) ) { $options[ 'clean_optimize' ] = sanitize_text_field( $_POST[ 'clean_optimize' ] ); } else { $options[ 'clean_optimize' ] = ''; }
	if ( isset( $_POST[ 'upgrade_enable' ] ) ) { $options[ 'upgrade_enable' ] = sanitize_text_field( $_POST[ 'upgrade_enable' ] ); } else { $options[ 'upgrade_enable' ] = ''; }
	if ( isset( $_POST[ 'upgrade_optimize' ] ) ) { $options[ 'upgrade_optimize' ] = sanitize_text_field( $_POST[ 'upgrade_optimize' ] ); } else { $options[ 'upgrade_optimize' ] = ''; }

	// If the scheduled time has changed, remove the old schedule and set up a new one

	if ( 0 < $_POST[ 'when_to_run' ] && 24 > $_POST[ 'when_to_run' ] ) {
		$options[ 'schedule' ] = $_POST[ 'when_to_run' ];
	} else {
		$options[ 'schedule' ] = '00';
	}

	if ( $options[ 'schedule' ] != $old_options[ 'schedule' ] ) {
		wp_clear_scheduled_hook( 'housekeep_transients' );
		tc_set_schedule( $options[ 'schedule' ] );
	}

	// Update the options

	update_option( 'transient_clean_options', $options );

	// Run any transient housekeeping, if requested

	if ( !empty( $_POST[ 'Clean' ] ) ) { $deleted = tc_transient_delete( false ); }
	if ( !empty( $_POST[ 'Upgrade' ] ) ) { $deleted = tc_transient_delete( true ); }

	// Write out an appropriate message

	$text = __( 'Options Saved.', 'artiss-transient-cleaner' );
	if ( ( !empty( $_POST[ 'Clean' ] ) ) or ( !empty( $_POST[ 'Upgrade' ] ) ) ) {
		$text .= ' ' . __( 'Transients cleared.', 'artiss-transient-cleaner' );
	}

	echo '<div class="updated fade"><p><strong>' . $text . '</strong></p></div>' . "\n";
}

$options = tc_get_options();
?>

<form method="post" action="<?php echo get_bloginfo( 'wpurl' ) . '/wp-admin/tools.php?page=tc-options' ?>">

<?php

// Show current number of transients, including number of expired

global $wpdb;
$total_transients = $wpdb -> get_var( "SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '%_transient_%'" );
$total_timed_transients = $wpdb -> get_var( "SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '%_transient_timeout_%'" );
if ( is_multisite() ) {
	$total_transients .= $wpdb -> get_var( "SELECT COUNT(*) FROM $wpdb->sitemeta WHERE meta_key LIKE '_site_transient_%'" );
	$total_timed_transients .= $wpdb -> get_var( "SELECT COUNT(*) FROM $wpdb->sitemeta WHERE meta_key LIKE '_site_transient_timeout_%'" );
}
$transient_number = $total_transients - $total_timed_transients;

$text =  sprintf( __( 'There are currently %s transients (%s records) in the database.', 'artiss-transient-cleaner' ), $transient_number, $total_transients );

if ( 0 <= $total_transients ) {

	$expired_transients = $wpdb -> get_var( "SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '%_transient_timeout_%' AND option_value < UNIX_TIMESTAMP()" );
	if ( is_multisite() ) { $expired_transients .= $wpdb -> get_var( "SELECT COUNT(*) FROM $wpdb->sitemeta WHERE meta_key LIKE '%_transient_timeout_%' AND meta_value < UNIX_TIMESTAMP()" ); }
	$text .= ' ';
	if ( 1 == $expired_transients ) {
		$text .= sprintf( __( '%s transient has expired.', 'artiss-transient-cleaner' ), $expired_transients );
	} else {
		$text .= sprintf( __( '%s transients have expired.', 'artiss-transient-cleaner' ), $expired_transients );
	}
}

echo '<p>' . $text . '</p>';
?>

<h3><?php _e( 'Clear Expired Transients', 'artiss-transient-cleaner' ); ?></h3>

<?php

// Show when expired transients were last cleared down and how many of them were

$array = get_option( 'transient_clean_expired' );

if ( $array !== false ) {
	if ( isset( $array[ 'records' ] ) ) {
		$text = sprintf( __( '%d expired transient records were cleared on %s at %s.', 'artiss-transient-cleaner' ), $array[ 'records' ], date( 'l, jS F Y', $array[ 'timestamp' ] ), date( 'H:i', $array[ 'timestamp' ] ) );
	} else {
		$text = sprintf( __( 'Expired transient records were cleared on %s at %s.', 'artiss-transient-cleaner' ), date( 'l, jS F Y', $array[ 'timestamp' ] ), date( 'H:i', $array[ 'timestamp' ] ) );
	}
} else {
	$text = __( 'No expired transients have yet been cleared.', 'artiss-transient-cleaner' );
}
echo '<p>' . $text . '</p>';
?>

<table class="form-table">

<tr>
<th scope="row"><label for="clean_enable"><?php _e( 'Enable', 'artiss-transient-cleaner' ); ?></label></th>
<td><input type="checkbox" name="clean_enable" value="1"<?php if ( isset( $options[ 'clean_enable' ] ) && ( $options[ 'clean_enable' ] ) ) { echo ' checked="checked"'; } ?>/><?php _e( 'Housekeep expired transients daily', 'artiss-transient-cleaner' ); ?></td>
</tr>

<tr>
<th scope="row"><?php _e( 'When to run', 'artiss-transient-cleaner' ); ?></th>
<td><label for="when_to_run"><select name="when_to_run">
<option value="00"<?php if ( "00" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>00:00</option>
<option value="01"<?php if ( "02" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>01:00</option>
<option value="02"<?php if ( "02" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>02:00</option>
<option value="03"<?php if ( "03" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>03:00</option>
<option value="04"<?php if ( "04" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>04:00</option>
<option value="05"<?php if ( "05" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>05:00</option>
<option value="06"<?php if ( "06" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>06:00</option>
<option value="07"<?php if ( "07" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>07:00</option>
<option value="08"<?php if ( "08" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>08:00</option>
<option value="09"<?php if ( "09" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>09:00</option>
<option value="10"<?php if ( "10" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>10:00</option>
<option value="11"<?php if ( "11" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>11:00</option>
<option value="12"<?php if ( "12" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>12:00</option>
<option value="13"<?php if ( "13" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>13:00</option>
<option value="14"<?php if ( "14" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>14:00</option>
<option value="15"<?php if ( "15" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>15:00</option>
<option value="16"<?php if ( "16" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>16:00</option>
<option value="17"<?php if ( "17" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>17:00</option>
<option value="18"<?php if ( "18" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>18:00</option>
<option value="19"<?php if ( "19" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>19:00</option>
<option value="20"<?php if ( "20" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>20:00</option>
<option value="21"<?php if ( "21" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>21:00</option>
<option value="22"<?php if ( "22" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>22:00</option>
<option value="23"<?php if ( "23" == $options[ 'schedule' ] ) { echo " selected='selected'"; } ?>>23:00</option>
</select></label><p class="description"><?php _e( 'Housekeeping will occur at this time every day.', 'artiss-transient-cleaner' ); ?></p></td>
</tr>

<tr>
<th scope="row"><label for="clean_optimize"><?php _e( 'Optimize', 'artiss-transient-cleaner' ); ?></label></th>
<td><input type="checkbox" name="clean_optimize" value="1"<?php if ( isset( $options[ 'clean_optimize' ] ) && ( $options[ 'clean_optimize' ] ) ) { echo ' checked="checked"'; } ?>/><?php _e( 'Optimize table(s) afterward the housekeeping.', 'artiss-transient-cleaner' ); ?> <strong><?php _e( 'Not recommended', 'artiss-transient-cleaner' ); ?></strong></td>
</tr>

<tr>
<th scope="row"><label for="Clean"><?php _e( 'Clean now', 'artiss-transient-cleaner' ); ?></label></th>
<td><input type="checkbox" name="Clean" value="1"/><?php _e( 'Remove all expired transients', 'artiss-transient-cleaner' ); ?></td>
</tr>

</table>

<h3><?php _e( 'Remove All Transients', 'artiss-transient-cleaner' ); ?></h3>

<?php

// Show when transients were last cleared down and how many

$array = get_option( 'transient_clean_all' );

if ( $array !== false ) {
	if ( isset( $array[ 'records' ] ) ) {
		echo '<p>' . sprintf( __( 'All %d transient records were removed on %s at %s.', 'artiss-transient-cleaner' ), $array[ 'records' ], date( 'l, jS F Y', $array[ 'timestamp' ] ), date( 'H:i', $array[ 'timestamp' ] ) ) . '</p>';
	} else {
		echo '<p>' . sprintf( __( 'All transient records removed on %s at %s.', 'artiss-transient-cleaner' ), date( 'l, jS F Y', $array[ 'timestamp' ] ), date( 'H:i', $array[ 'timestamp' ] ) ) . '</p>';
	}
}
?>

<table class="form-table">

<tr>
<th scope="row"><label for="upgrade_enable"><?php _e( 'Enable', 'artiss-transient-cleaner' ); ?></label></th>
<td><input type="checkbox" name="upgrade_enable" value="1"<?php if ( isset( $options[ 'upgrade_enable' ] ) && ( $options[ 'upgrade_enable' ] ) ) { echo ' checked="checked"'; } ?>/><?php _e( 'Remove all transients when a database upgrade occurs', 'artiss-transient-cleaner' ); ?></td>
</tr>

<tr>
<th scope="row"><label for="upgrade_optimize"><?php _e( 'Optimize afterwards', 'artiss-transient-cleaner' ); ?></label></th>
<td><input type="checkbox" name="upgrade_optimize" value="1"<?php if ( isset( $options[ 'upgrade_optimize' ] ) && ( $options[ 'upgrade_optimize' ] ) ) { echo ' checked="checked"'; } ?>/><?php _e( 'Optimize table(s) afterward the housekeeping', 'artiss-transient-cleaner' ); ?></td>
</tr>

<tr>
<th scope="row"><label for="Upgrade"><?php _e( 'Clean now', 'artiss-transient-cleaner' ); ?></label></th>
<td><input type="checkbox" name="Upgrade" value="1"/><?php _e( 'Remove all transients', 'artiss-transient-cleaner' ); ?></td>
</tr>

</table>

<?php wp_nonce_field( 'transient-cleaner-options', 'transient_cleaner_options_nonce', true, true ); ?>

<input type="submit" name="Options" class="button-primary" value="<?php _e( 'Save Changes', 'artiss-transient-cleaner' ); ?>"/>

</form>

</div>