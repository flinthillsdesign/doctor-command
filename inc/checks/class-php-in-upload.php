<?php

namespace runcommand\Doctor\Checks;

use WP_CLI;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Warns when a PHP file is present in the Uploads folder.
 */
class PHP_In_Upload extends Check {

	/**
	 * Array containing list of files found in the uploads folder
	 *
	 * @var array
	 */
	protected $php_files_array = array();

	/**
	 * Status that is set when this check fails
	 *
	 * @var string
	 */
	protected $status_for_failure = 'warning';

	public function run() {

		// Path to the uploads folder.
		$wp_content_dir = wp_upload_dir();
		$directory = new RecursiveDirectoryIterator( $wp_content_dir['basedir'], RecursiveDirectoryIterator::SKIP_DOTS );
		$iterator = new RecursiveIteratorIterator( $directory, RecursiveIteratorIterator::CHILD_FIRST );

		foreach ( $iterator as $file ) {
			if ( 'php' === $file->getExtension() ) {
				$this->php_files_array[] = $file;
			}
		}

		if ( ! empty( $this->php_files_array ) ) {
			$this->set_status( $this->status_for_failure );
			$this->set_message( 'PHP files detected in the Uploads folder.' );
		} else {
			$this->set_status( 'success' );
			$this->set_message( 'No PHP files found in the Uploads folder.' );
		}
	}
}
