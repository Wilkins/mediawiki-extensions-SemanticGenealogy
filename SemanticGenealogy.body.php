<?php

/**
 * Static class for hooks handled by the Semantic Genealogy extension.
 *
 * @file    SemanticGenealogy.body.php
 * @ingroup SemanticGenealogy
 *
 * @licence GNU GPL v2+
 * @author  Thomas Pellissier Tanon <thomaspt@hotmail.fr>
 */
class SemanticGenealogy {

	/**
	 * Get an array key => value of genealogical properties as SMWDIProperty
	 *
	 * @throws MWException
	 *
	 * @return array the properties array
	 */
	public static function getProperties() {
		static $properties;

		if ( $properties !== null ) {
			return $properties;
		}

		global $wgGenealogicalProperties;
		$properties = [];

		if ( !is_array( $wgGenealogicalProperties ) ) {
			throw new MWException( 'Configuration variable $wgGenealogicalProperties must be an array !' );
		}

		foreach ( $wgGenealogicalProperties as $key => $value ) {
			if ( $value ) {
				$properties[$key] = SMWDIProperty::newFromUserLabel( $value );
			}
		}
		return $properties;
	}

	/**
	 * List the namespaces declared into SemanticGenealogy
	 * @return array list of namespaces declared by lang
	 */
	public static function getNamespaces() {
		global $wgExtensionMessagesFiles;
		require $wgExtensionMessagesFiles['SemanticGenealogyNamespaces'];

		/*
		$all = get_defined_constants( true );
		$namespaces = array_filter( $all['user'], function ( $name ) {
			return preg_match( '/NS_/', $name );
		}, ARRAY_FILTER_USE_KEY );
		*/

		/*
		$namespaceNames['base_fr'][14]  = 'Catégorie';
		$namespaceNames['base_fr'][102] = 'Propriété';
		$namespaceNames['base_fr'][106] = 'Formulaire';
		*/
		$namespaceNames['base_en'][14]  = 'Category';
		$namespaceNames['base_en'][102] = 'Property';
		$namespaceNames['base_en'][106] = 'Form';
		return $namespaceNames;
	}

	/**
	 * Get the namespace id from the namespace name
	 *
	 * @throws SemanticGenealogyException if namespace is not found.
	 *
	 * @param string $searchName the namespace we are looking for
	 *
	 * @return integer the id of the namespace
	 */
	public static function getNamespaceFromName( $searchName ) {
		$sgNamespaces = self::getNamespaces();

		foreach ( $sgNamespaces as $lang => $langNamespaces ) {
			foreach ( $langNamespaces as $nsId => $nsName ) {
				if ( $searchName == $nsName ) {
					return $nsId;
				}
			}
		}
		throw new SemanticGenealogyException( "Namespace name « ${searchName} » was not found in SemanticGenealogy. This should not happen, please contact developpers extension with tag: Error101" );
	}

	public static function setNamespaceAliases( $namespaces ) {
		global $wgNamespaceAliases;

		foreach ( $namespaces as $nsId => $namespace ) {
			$wgNamespaceAliases[$namespace] = $nsId;
		}
	}
}
