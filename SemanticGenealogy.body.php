<?php

/**
 * Static class for hooks handled by the Semantic Genealogy extension.
 *
 * @file SemanticGenealogy.body.php
 * @ingroup SemanticGenealogy
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon < thomaspt@hotmail.fr >
 */
class SemanticGenealogy {

	/**
	 * Get an array key => value of genealogical properties as SMWDIProperty
	 * @throws MWException
	 */
	public static function getProperties() {
		static $properties;

		if( $properties !== null )
			return $properties;

		global $wgGenealogicalProperties, $wgOut;
		$properties = array();

		if( !is_array( $wgGenealogicalProperties ) ) {
			throw new MWException( 'Configuration variable $wgGenealogicalProperties must be an array !' );
		}

		foreach( $wgGenealogicalProperties as $key => $value ) {
			if( $value ) {
				$properties[$key] = SMWDIProperty::newFromUserLabel( $value );
			}
		}
		return $properties;
	}
}
