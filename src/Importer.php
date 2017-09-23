<?php

namespace SemanticGenealogy;

use DirectoryIterator;

class Importer {

	public $lang;

	/**
	 * @constructor
	 *
	 * @param string $lang the 2 chars lang
	 */
	public function __construct( $lang ) {
		if ( ! $lang ) {
			throw new Exception(
				"Lang parameter not defined, could not construct the Importer object"
			   );
		}
		$this->lang = $lang;
	}

	/**
	 * Import the file into the wiki database using the maintenance/importTextFiles.php script
	 *
	 * @param string $file the full file path
	 *
	 * @return void
	 */
	public function importFile( $file ) {
		global $IP;
		// Use where if windows platform ?
		$php = trim( shell_exec( "which php" ) );
		$maintenanceScript = "$IP/maintenance/importTextFiles.php";
		$config = "$IP/LocalSettings.php";
		$text = "Update from SemanticGenealogy (v". SGENEA_VERSION.")";

		$command = "$php $maintenanceScript --conf=$config "
			." -s '$text' --overwrite --rc --use-timestamp $file";
		// echo "$command\n";
		// shell_exec( $command );
	}

	/**
	 * List all the importable files from the given lang
	 *
	 * @return array the list of importable files
	 */
	public function listFiles() {
		$filesDir = __DIR__ . "/../templates/".$this->lang."/";
		if ( !is_dir( $filesDir ) ) {
			throw new Exception( "Directory $filesDir does not exist." );
		}
		$files = [];
		foreach ( new DirectoryIterator( $filesDir ) as $file ) {
			if ( $file->isDot() ) {
				continue;
			}
			if ( !$file->isFile() ) {
				continue;
			}
			if ( !preg_match( "#.txt$#", $file->getFilename() ) ) {
				continue;
			}

			$displayName = preg_replace( "#.txt$#", "", $file->getBasename() );
			$files[ $displayName ] = $file->getPathname();
		}
		asort( $files );
		return $files;
	}
}
