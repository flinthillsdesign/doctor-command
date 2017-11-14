<?php

namespace runcommand\Doctor\Checks;

use WP_CLI;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use BrightNucleus\MimeTypes\MimeTypes;

/**
 * Warns when the extension of a file doesn't match the MIME type.
 */
class Validate_Mime extends Check {

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
		$directory      = new RecursiveDirectoryIterator( $wp_content_dir['basedir'], RecursiveDirectoryIterator::SKIP_DOTS );
		$iterator       = new RecursiveIteratorIterator( $directory, RecursiveIteratorIterator::CHILD_FIRST );

		foreach ( $iterator as $file ) {
			$file_path      = $file->getPathname();
			$file_extension = $file->getExtension();
			$finfo = finfo_open( FILEINFO_MIME_TYPE ); // return mime type ala mimetype extension
			$file_mime_type = finfo_file( $finfo, $file_path )
			finfo_close( $finfo );

			if ( 'directory' !== $file_mime_type ) {
				$mime_types = MimeTypes::getTypesForExtension( $file_extension );

				if ( is_array( $mime_types ) && ! in_array( $file_mime_type, $mime_types ) ) {
					$this->php_files_array[] = $file;
				}
			}
		}

		if ( ! empty( $this->php_files_array ) ) {
			$this->set_status( $this->status_for_failure );
			$this->set_message( 'Files detected with different MIME type.' );
			return;
		}

		$this->set_status( 'success' );
		$this->set_message( 'All files have valid MIMEs' );
	}
}
