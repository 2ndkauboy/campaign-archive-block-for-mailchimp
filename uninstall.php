<?php
/**
 * Functionality that is executed when he plugin is uninstalled via built-in WordPress commands.
 *
 * @package 2ndkauboy/campaign-archive-block-for-mailchimp
 */

/**
 * Uninstall function.
 */
function cabfm_uninstall() {
	if (
		! defined( 'WP_UNINSTALL_PLUGIN' ) ||
		! WP_UNINSTALL_PLUGIN ||
		dirname( WP_UNINSTALL_PLUGIN ) !== dirname( plugin_basename( __FILE__ ) )
	) {
		status_header( 404 );
		exit;
	}

	// Delete all options.
	delete_option( 'cabfm_api_key' );
	delete_option( 'cabfm_api_credentials_validation_result' );
	delete_option( 'cabfm_api_credentials_validation_message' );
	delete_option( 'cabfm_api_credentials_account_name' );
}

cabfm_uninstall();
