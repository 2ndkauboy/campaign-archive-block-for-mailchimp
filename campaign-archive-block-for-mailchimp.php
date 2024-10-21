<?php
/**
 * Campaign Archive Block for Mailchimp
 *
 * @package 2ndkauboy/campaign-archive-block-for-mailchimp
 * @author  Bernhard Kau
 * @license GPLv3
 *
 * @wordpress-plugin
 * Plugin Name: Campaign Archive Block for Mailchimp
 * Plugin URI: https://github.com/2ndkauboy/campaign-archive-block-for-mailchimp
 * Description: Adds a block to show an archive for Mailchimp Campaigns.
 * Version: 2.2.0
 * Author: Bernhard Kau
 * Author URI: https://kau-boys.com
 * Text Domain: campaign-archive-block-for-mailchimp
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

define( 'CABFM_VERSION', '2.2.0' );
define( 'CABFM_FILE', __FILE__ );
define( 'CABFM_PATH', plugin_dir_path( CABFM_FILE ) );
define( 'CABFM_URL', plugin_dir_url( CABFM_FILE ) );

// The pre_init functions check the compatibility of the plugin and calls the init function, if check were successful.
cabfm_pre_init();

/**
 * Pre init function to check the plugin's compatibility.
 */
function cabfm_pre_init() {
	// Check, if the min. required PHP version is available and if not, show an admin notice.
	if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
		add_action( 'admin_notices', 'cabfm_min_php_version_error' );

		// Stop the further processing of the plugin.
		return;
	}

	if ( ! file_exists( CABFM_PATH . 'build/index.js' ) ) {
		add_action( 'admin_notices', 'cabfm_build_files_missing' );

		// Stop the further processing of the plugin.
		return;
	}

	if ( file_exists( CABFM_PATH . 'composer.json' ) && ! file_exists( CABFM_PATH . 'vendor/autoload.php' ) ) {
		add_action( 'admin_notices', 'cabfm_autoloader_missing' );

		// Stop the further processing of the plugin.
		return;
	} else {
		$autoloader = CABFM_PATH . 'vendor/autoload.php';

		if ( is_readable( $autoloader ) ) {
			include $autoloader;
		}
	}

	// If all checks were successful, load the plugin.
	require_once CABFM_PATH . 'lib/load.php';
}

/**
 * Show an admin notice error message, if the PHP version is too low.
 */
function cabfm_min_php_version_error() {
	echo '<div class="error"><p>';
	esc_html_e( 'Campaign Archive for Mailchimp requires PHP version 5.6 or higher to function properly. Please upgrade PHP or deactivate Campaign Archive for Mailchimp.', 'campaign-archive-block-for-mailchimp' );
	echo '</p></div>';
}

/**
 * Show an admin notice error message, if the composer autoloader is missing.
 */
function cabfm_autoloader_missing() {
	echo '<div class="error"><p>';
	esc_html_e( 'Campaign Archive for Mailchimp is missing the Composer autoloader file. Please run `composer install --no-dev -o` in the root folder of the plugin or use a release version including the `vendor` folder.', 'campaign-archive-block-for-mailchimp' );
	echo '</p></div>';
}

/**
 * Show an admin notice error message, if the build files are missing.
 */
function cabfm_build_files_missing() {
	echo '<div class="error"><p>';
	esc_html_e( 'Campaign Archive for Mailchimp is missing the build file. Please run `npm install` and `npm run build` in the root folder of the plugin or use a release version including the `build` folder.', 'campaign-archive-block-for-mailchimp' );
	echo '</p></div>';
}
