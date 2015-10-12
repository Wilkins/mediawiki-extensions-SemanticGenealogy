<?php



class Tools
{

	public static function getSubclassesOf( $dir, $superClass ) {

		$classes = array();
		foreach ( new DirectoryIterator( $dir ) as $file ) {
			if ( $file->isDot() ) {
				continue;
			}
			if ( !$file->isFile() ) {
				continue;
			}
			if ( !preg_match( "#.php$#", $file->getFilename() ) ) {
				continue;
			}
			require_once $file->getPathname();
			$className = $file->getBasename( '.php' );
			try {
				$classRef = new ReflectionClass( $className );
			} catch ( ReflectionException $e ) {
				continue;
			}

			if ( !$classRef->isAbstract() && $classRef->isSubclassOf( $superClass ) ) {
				$classes[] = new $className;
			}
		}
		return $classes;

	}
}
