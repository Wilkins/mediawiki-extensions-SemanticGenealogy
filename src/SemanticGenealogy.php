<?php

namespace SemanticGenealogy;

use MWException;
use SMWDIProperty;
use SemanticGenealogy\NamespaceManager;

/**
 * Static class for hooks handled by the Semantic Genealogy extension.
 *
 * @file    SemanticGenealogy.php
 * @ingroup SemanticGenealogy
 *
 * @licence GNU GPL v2+
 * @author  Thomas Pellissier Tanon <thomaspt@hotmail.fr>
 */
class SemanticGenealogy
{

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
		throw new Exception('test');
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
	 * @throws Exception if namespace is not found.
	 *
	 * @param string $searchName the namespace we are looking for
	 *
	 * @return integer the id of the namespace
	 */
	public static function getNamespaceFromName( $searchName ) {
		global $wgNamespaceAliases;

		if ( isset( $wgNamespaceAliases[$searchName] ) ) {
			return $wgNamespaceAliases[$searchName];
		}

		throw new Exception( "Namespace name « ${searchName} » was not found in SemanticGenealogy. This should not happen, please contact developpers extension with tag: Error101" );
	}

	public static function setNamespaceAliases( $namespaces ) {
		global $wgNamespaceAliases;

		foreach ( $namespaces as $nsId => $namespace ) {
			$wgNamespaceAliases[$namespace] = $nsId;
		}
	}

	public static function onCanonicalNamespaces( array &$namespaces ) {
		require_once __DIR__."/../SemanticGenealogy.namespaces.php";
		#print_r( $namespaces );
		#global $namespaceNames;

		#print_r($namespaceNames);
		foreach ( $namespaceNames['en'] as $nsId => $nsName ) {
			$namespaces[$nsId] = $nsName;
		}
		#print_r( $namespaces );

	}

	public static function addExtensionCSS( &$parser, &$text) {

	  global $addSgeneCSSScripts;
	  if ( $addSgeneCSSScripts === true ) {
		return true;
	  }

	  $parser->mOutput->addHeadItem(
		'<link rel="stylesheet" href="/load.php?debug=false&amp;lang=en&amp;modules=ext.smg.specialfamilytree&amp;only=styles&amp;skin=semanticgenealogy"/>'
	);
	  /*
	   */

	  $addSgeneCSSScripts = true;

	  return true;

	}

	public static function initExtension() {
		$GLOBALS['smwgResultFormats']['gedcom'] = 'SemanticGenealogy\Gedcom\Gedcom5ResultPrinter';
		$GLOBALS['smwgResultFormats']['gedcom5'] = 'SemanticGenealogy\Gedcom\Gedcom5ResultPrinter';

		$GLOBALS['wgSGeneaSidebarAdd'] = true;
		$GLOBALS['wgSGeneaSidebarPosition'] = 2;

		$GLOBALS['sgeneawgExtraneousLanguageFileDir'] = __DIR__.'/../i18n/extra';

		#$GLOBALS['wgHooks']['CanonicalNamespaces'][] 
		#	= 'SemanticGenealogy\SemanticGenealogy::onCanonicalNamespaces';

		if ( !isset( $GLOBALS['wgGenealogicalProperties'] ) ) {
			$GLOBALS['wgGenealogicalProperties'] = [
				'givenname' => 'Prénom',
				'surname' => 'Nom',
				'nickname' => '',
				'sex' => 'Sexe',
				'birthdate' => 'Date de naissance',
				'birthplace' => 'Lieu de naissance',
				'deathdate' => 'Date de décès',
				'deathplace' => 'Lieu de décès',
				'father' => 'Père',
				'mother' => 'Mère',
				'partner' => 'Conjoint',
				'sosa' => 'Sosa',
			];
		}

		if ( !defined( 'SMW_NS_PROPERTY' ) ) {
			define( 'SMW_NS_PROPERTY', 102 );
		}
		if ( !defined( 'NS_FORM' ) ) {
			define( 'NS_FORM', 106 );
		}

		$GLOBALS['wgLanguageCode'] = 'fr';
		NamespaceManager::initCustomNamespace( $GLOBALS, 'fr' );

		$GLOBALS['moduleTemplate'] = [
			'localBasePath' => __DIR__.'/../',
			'remoteBasePath' => ( $GLOBALS['wgExtensionAssetsPath'] === false ? $GLOBALS['wgScriptPath']
				. '/extensions' : $GLOBALS['wgExtensionAssetsPath'] ) . '/SemanticGenealogy',
			'group' => 'ext.smg'
		];

		$GLOBALS['wgResourceModules']['ext.smg.specialfamilytree'] = $GLOBALS['moduleTemplate'] + [
			'scripts' => 'modules/specialFamilyTree.js',
			'styles' => 'modules/styles.css',
			'dependencies' => [
				'jquery.ui.autocomplete'
			],
			'messages' => [
			]
		];



	}

}
