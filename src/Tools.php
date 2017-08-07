<?php

namespace SemanticGenealogy;

use DirectoryIterator;
use ReflectionClass;

class Tools {

	/**
	 * Find the subclasses of that superclass in one directory
	 *
	 * @param string $dir the directory
	 * @param string $superClass the superclass name
	 *
	 * @return array an array of $classes subclass of the superClass
	 */
	public static function getSubclassesOf( $dir, $superClass ) {

		$classes = [];
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
			$className = preg_replace( "#\.php$#", "", $file->getPathname() );
			$className = preg_replace( "#.*SemanticGenealogy/src#", "SemanticGenealogy", $className );
			$className = preg_replace( "#/#", "\\\\", $className );
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
