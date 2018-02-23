<?php

namespace runcommand\Doctor\Checks;

use WP_CLI;

/**
 * Warns when there are plugin updates available.
 */
class Plugin_Update extends Plugin {

	/**
	 * Status that is set when this check fails
	 *
	 * @var string
	 */
	protected $status_for_failure = 'warning';

	public function run() {
		$plugins = self::get_plugins();

		$outdated_plugins = array_filter($plugins, function($plugin) {
			return 'available' === $plugin['update'];
		});
		$update_count = count( $outdated_plugins );
		$outdated_plugin_names = implode(
			array_map(function($plugin) { return $plugin['name']; }, $outdated_plugins),
			', '
		);

		$this->set_data( $outdated_plugins );

		if ( 1 === $update_count ) {
			$this->set_status( $this->status_for_failure );
			$this->set_message( "1 plugin has an update available." );
			$this->set_recommendation( "Update the {$outdated_plugin_names} plugin." );
		} else if ( $update_count ) {
			$this->set_status( $this->status_for_failure );
			$this->set_message( "{$update_count} plugins have updates available." );
			$this->set_recommendation( "Update these plugins: {$outdated_plugin_names}" );
		} else {
			$this->set_status( 'success' );
			$this->set_message( 'Plugins are up to date.' );
		}

	}

}
