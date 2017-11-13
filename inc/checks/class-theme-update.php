<?php

namespace runcommand\Doctor\Checks;

use WP_CLI;

/**
 * Warns when there are theme updates available.
 */
class Theme_Update extends Check {

	/**
	 * Status that is set when this check fails
	 *
	 * @var string
	 */
	protected $status_for_failure = 'warning';

	public function run() {
		ob_start();
		WP_CLI::run_command( array( 'theme', 'list' ), array( 'format' => 'json' ) );
		$ret = ob_get_clean();
		$themes = ! empty( $ret ) ? json_decode( $ret, true ) : array();
		$update_count = 0;
		foreach( $themes as $theme ) {
			if ( 'available' === $theme['update'] ) {
				$update_count++;
			}
		}

		if ( 1 === $update_count ) {
			$this->set_status( $this->status_for_failure );
			$this->set_message( "1 theme has an update available." );
		} else if ( $update_count ) {
			$this->set_status( $this->status_for_failure );
			$this->set_message( "{$update_count} themes have updates available." );
		} else {
			$this->set_status( 'success' );
			$this->set_message( 'Themes are up to date.' );
		}

	}

}
