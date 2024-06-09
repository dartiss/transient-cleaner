<?php
/**
 * Admin Config Functions
 *
 * Various functions relating to the various administration screens
 *
 * @package artiss-transient-cleaner
 */

// Exit if accessed directly.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Screen Initialisation.
 *
 * Set up admin menu
 */
function transient_cleaner_menu_initialise() {

	if ( defined( 'transient_cleaner_LITE' ) && transient_cleaner_LITE ) {
		$lite = true;
	} else {
		$lite = false;
	}

	if ( ! $lite ) {

		// Add submenu to tools menu.

		global $transient_cleaner_options_hook;

		$transient_cleaner_options_hook = add_submenu_page( 'tools.php', __( 'Transient Cleaner Options', 'artiss-transient-cleaner' ), __( 'Transient Cleaner', 'artiss-transient-cleaner' ), 'install_plugins', 'transient-options', 'transient_cleaner_options' );

		add_action( 'load-' . $transient_cleaner_options_hook, 'transient_cleaner_add_options_help' );
	}
}

add_action( 'admin_menu', 'transient_cleaner_menu_initialise' );

/**
 * Include options screen
 *
 * XHTML screen to prompt and update settings.
 */
function transient_cleaner_options() {

	include_once plugin_dir_path( __FILE__ ) . 'settings-screen.php';
}

/**
 * Add Options Help
 *
 * Add help tab to options screen.
 *
 * @uses     transient_cleaner_options_help    Return help text
 */
function transient_cleaner_add_options_help() {

	global $transient_cleaner_options_hook;
	$screen = get_current_screen();

	if ( $screen->id !== $transient_cleaner_options_hook ) {
		return;
	}

	$screen->add_help_tab(
		array(
			'id'      => 'tc-options-help-tab',
			'title'   => __( 'Help', 'artiss-transient-cleaner' ),
			'content' => transient_cleaner_options_help(),
		)
	);

	$screen->set_help_sidebar( transient_cleaner_options_sidebar() );
}

/**
 * Options Help
 *
 * Return help text for options screen.
 *
 * @return string   Help Text
 */
function transient_cleaner_options_help() {

	$help_text  = '<p>' . __( 'This screen allows you to specify the default options for the Transient Cleaner plugin.', 'artiss-transient-cleaner' ) . '</p>';
	$help_text .= '<p>' . __( "In addition, details of recent transient cleans are shown. Tick the 'Run Now' options to perform a clean, whether a full removal of transients or just the removal of expired transients.", 'artiss-transient-cleaner' ) . '</p>';
	$help_text .= '<p>' . __( 'Remember to click the Save Changes button at the bottom of the screen for new settings to take effect.', 'artiss-transient-cleaner' ) . '</p></h4>';

	return $help_text;
}

/**
 * Options Help Sidebar
 *
 * Add a links sidebar to the options help.
 *
 * @return string   Help Text
 */
function transient_cleaner_options_sidebar() {

	$help_text  = '<p><strong>' . __( 'For more information:', 'artiss-transient-cleaner' ) . '</strong></p>';
	$help_text .= '<p><a href="https://wordpress.org/plugins/artiss-transient-cleaner/">' . __( 'Instructions', 'artiss-transient-cleaner' ) . '</a></p>';
	$help_text .= '<p><a href="https://wordpress.org/support/plugin/artiss-transient-cleaner">' . __( 'Support Forum', 'artiss-transient-cleaner' ) . '</a></p></h4>';

	return $help_text;
}

/**
 * Get options
 *
 * Fetch options and, if any are missing, complete the data
 *
 * @return string   Array of options
 */
function transient_cleaner_get_options() {

	if ( defined( 'transient_cleaner_LITE' ) && transient_cleaner_LITE ) {
		$lite = true;
	} else {
		$lite = false;
	}

	if ( ! $lite ) {
		$options = get_option( 'transient_clean_options' );
	}

	// If options don't exist, create an empty array.

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	// Because of upgrading, check each option - if not set, apply default.

	$default_array = array(
		'clean_enable'     => true,
		'clean_optimize'   => false,
		'upgrade_enable'   => true,
		'upgrade_optimize' => true,
		'schedule'         => '00',
	);

	// Merge existing and default options - any missing from existing will take the default settings.

	$new_options = array_merge( $default_array, $options );

	// Update the options, if changed, and return the result.

	if ( $options !== $new_options && ! $lite ) {
		update_option( 'transient_clean_options', $new_options );
	}

	return $new_options;
}
