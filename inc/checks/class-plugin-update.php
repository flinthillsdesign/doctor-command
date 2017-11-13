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
		$update_count = 0;
		foreach( $plugins as $plugin ) {
			if ( 'available' === $plugin['update'] ) {
				$update_count++;
			}
		}

		if ( 1 === $update_count ) {
			$this->set_status( $this->status_for_failure );
			$this->set_message( "1 plugin has an update available." );
		} else if ( $update_count ) {
			$this->set_status( $this->status_for_failure );
			$this->set_message( "{$update_count} plugins have updates available." );
		} else {
			$this->set_status( 'success' );
			$this->set_message( 'Plugins are up to date.' );
		}

	}

}
