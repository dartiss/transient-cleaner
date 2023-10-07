<?php
/**
 * Admin Config Functions
 *
 * Various functions relating to the various administration screens
 *
 * @package Artiss-Transient-Cleaner
 */

/**
 * Add meta to plugin details
 *
 * Add options to plugin meta line
 *
 * @param  string $links   Current links.
 * @param  string $file    File in use.
 * @return string          Links, now with settings added
 */
function tc_set_plugin_meta( $links, $file ) {

	if ( strpos( $file, 'artiss-transient-cleaner.php' ) !== false ) {

		$links = array_merge(
			$links,
			array( '<a href="https://wordpress.org/support/plugin/artiss-transient-cleaner">' . __( 'Support', 'artiss-transient-cleaner' ) . '</a>' ),
			array( '<a href="https://artiss.blog/donate">' . __( 'Donate', 'artiss-transient-cleaner' ) . '</a>' ),
			array( '<a href="https://wordpress.org/support/plugin/artiss-transient-cleaner/reviews/#new-post">' . __( 'Write a Review', 'artiss-transient-cleaner' ) . '&nbsp;⭐️⭐️⭐️⭐️⭐️</a>' )
		);
	}

	return $links;
}
add_filter( 'plugin_row_meta', 'tc_set_plugin_meta', 10, 2 );

/**
 * Add Settings link to plugin list
 *
 * Add a Settings link to the options listed against this plugin.
 *
 * @param  string $links   Current links.
 * @param  string $file    File in use.
 * @return string          Links, now with settings added
 */
function tc_add_settings_link( $links, $file ) {

	static $this_plugin;
	if ( ! $this_plugin ) {
		$this_plugin = plugin_basename( __FILE__ );
	}

	global $_wp_using_ext_object_cache;
	if ( defined( 'TC_LITE' ) && TC_LITE ) {
		$lite = true;
	} else {
		$lite = false;
	}

	if ( ! $_wp_using_ext_object_cache && ! $lite && strpos( $file, 'artiss-transient-cleaner.php' ) !== false ) {

		$settings_link = '<a href="tools.php?page=transient-options">' . __( 'Settings', 'artiss-transient-cleaner' ) . '</a>';

		array_unshift( $links, $settings_link );
	}

	return $links;
}

add_filter( 'plugin_action_links', 'tc_add_settings_link', 10, 2 );

/**
 * Check for minimum requirements
 *
 * Make sure that an object cache is not in use and the WordPress version is sufficient. If not, deactivate the plugin.
 */
function tc_validate_requirements() {

	$error = false;

	global $_wp_using_ext_object_cache;

	if ( $_wp_using_ext_object_cache ) {
		$error = __( 'An external object cache is in use so Transient Cleaner is not required.', 'artiss-transient-cleaner' );
	}

	global $wp_version;

	if ( version_compare( $wp_version, '4.9', '>=' ) ) {
		/* translators: 1: The version of WordPress currently in use. */
		$error = sprintf( __( 'Transient cleaning is now part of WordPress core, as of version 4.9. You are running version %1$s', 'artiss-transient-cleaner' ), $wp_version );
	}

	// Check if an issue was found, above.

	if ( false !== $error ) {

		// Grab the plugin details.

		$plugins = get_plugins();
		$name    = $plugins[ TRANSIENT_CLEANER_PLUGIN_BASE ]['Name'];

		// Deactivate this plugin.

		deactivate_plugins( TRANSIENT_CLEANER_PLUGIN_BASE );

		// Set up a message and output it via wp_die.

		/* translators: 1: The plugin name. */
		$message  = '<p><b>' . sprintf( __( '%1$s has been deactivated', 'artiss-transient-cleaner' ), $name ) . '</b></p><p>' . __( 'Reason:', 'artiss-transient-cleaner' ) . '</p>';
		$message .= '<ul><li>' . $error . '</li></ul>';

		$allowed = array(
			'p'  => array(),
			'b'  => array(),
			'ul' => array(),
			'li' => array(),
		);

		wp_die( wp_kses( $message, $allowed ), '', array( 'back_link' => true ) );
	}
}

add_action( 'admin_init', 'tc_validate_requirements' );

/**
 * Admin Screen Initialisation.
 *
 * Set up admin menu
 */
function tc_menu_initialise() {

	if ( defined( 'TC_LITE' ) && TC_LITE ) {
		$lite = true;
	} else {
		$lite = false;
	}

	if ( ! $lite ) {

		// Add submenu to tools menu.

		global $tc_options_hook;

		$tc_options_hook = add_submenu_page( 'tools.php', __( 'Transient Cleaner Options', 'artiss-transient-cleaner' ), __( 'Transient Cleaner', 'artiss-transient-cleaner' ), 'install_plugins', 'transient-options', 'tc_options' );

		add_action( 'load-' . $tc_options_hook, 'tc_add_options_help' );
	}
}

add_action( 'admin_menu', 'tc_menu_initialise' );

/**
 * Include options screen
 *
 * XHTML screen to prompt and update settings.
 */
function tc_options() {

	include_once plugin_dir_path( __FILE__ ) . 'options-general.php';
}

/**
 * Add Options Help
 *
 * Add help tab to options screen.
 *
 * @uses     tc_options_help    Return help text
 */
function tc_add_options_help() {

	global $tc_options_hook;
	$screen = get_current_screen();

	if ( $screen->id !== $tc_options_hook ) {
		return;
	}

	$screen->add_help_tab(
		array(
			'id'      => 'tc-options-help-tab',
			'title'   => __( 'Help', 'artiss-transient-cleaner' ),
			'content' => tc_options_help(),
		)
	);

	$screen->set_help_sidebar( tc_options_sidebar() );
}

/**
 * Options Help
 *
 * Return help text for options screen.
 *
 * @return string   Help Text
 */
function tc_options_help() {

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
function tc_options_sidebar() {

	$help_text  = '<p><strong>' . __( 'For more information:', 'artiss-transient-cleaner' ) . '</strong></p>';
	$help_text .= '<p><a href="https://wordpress.org/plugins/artiss-transient-cleaner/">' . __( 'Instructions', 'artiss-transient-cleaner' ) . '</a></p>';
	$help_text .= '<p><a href="https://wordpress.org/support/plugin/artiss-transient-cleaner">' . __( 'Support Forum', 'artiss-transient-cleaner' ) . '</a></p></h4>';

	return $help_text;
}
