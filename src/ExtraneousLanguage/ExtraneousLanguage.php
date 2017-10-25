<?php

namespace SemanticGenealogy\ExtraneousLanguage;


/**
 * This class provides "extraneous" language functions independent from MediaWiki
 * to handle certain language options in a way required by Semantic MediaWiki and
 * its registration system.
 *
 * @license GNU GPL v2+
 * @since 2.4
 *
 * @author mwjames
 * @author 
 *
 * @see https://github.com/SemanticMediaWiki/SemanticMediaWiki/blob/master/src/ExtraneousLanguage/ExtraneousLanguage.php 
 */
class ExtraneousLanguage {

	/**
	 * @var ExtraneousLanguage
	 */
	private static $instance = null;

	/**
	 * @var LanguageContents
	 */
	private $languageContents;

	/**
	 * @var boolean
	 */
	private $historicTypeNamespace = false;

	/**
	 * @var string
	 */
	private $languageCode = 'fr';

	/**
	 * @since 2.4
	 *
	 * @param LanguageContents $languageContents
	 */
	public function __construct( LanguageContents $languageContents ) {
		$this->languageContents = $languageContents;
	}

	/**
	 * @since 2.4
	 *
	 * @return ExtraneousLanguage
	 */
	public static function getInstance() {

		if ( self::$instance !== null ) {
			return self::$instance;
		}

		// $cache = ApplicationFactory::getInstance()->getCache()

		$jsonLanguageContentsFileReader = new JsonLanguageContentsFileReader();
		//$languageFileContentsReader->setCachePrefix( $cacheFactory->getCachePrefix() )

		self::$instance = new self(
			new LanguageContents(
				$jsonLanguageContentsFileReader,
				new LanguageFallbackFinder( $jsonLanguageContentsFileReader )
			)
		);

		/*
		self::$instance->setHistoricTypeNamespace(
			$GLOBALS['smwgHistoricTypeNamespace']
		);
		 */

		return self::$instance;
	}

	/**
	 * @since 2.4
	 */
	public static function clear() {
		self::$instance = null;
	}

	/**
	 * @since 2.5
	 *
	 * @param boolean $historicTypeNamespace
	 */
	/*
	public function setHistoricTypeNamespace( $historicTypeNamespace ) {
		$this->historicTypeNamespace = (bool)$historicTypeNamespace;
	}
	 */

	/**
	 * @since 2.4
	 *
	 * @return string
	 */
	public function getCode() {
		return $this->languageCode;
	}

	/**
	 * @since 2.4
	 *
	 * @return string
	 */
	public function fetchByLanguageCode( $languageCode ) {

		$this->languageCode = strtolower( trim( $languageCode ) );

		if ( !$this->languageContents->has( $this->languageCode ) ) {
			$this->languageContents->load( $this->languageCode );
		}

		return $this;
	}

	/**
	 * Function that returns an array of namespace identifiers.
	 *
	 * @since 2.4
	 *
	 * @return array
	 */
	public function getNamespaces() {

		$namespaces = $this->languageContents->getContentsFromLanguageById(
			$this->languageCode,
			'namespaces'
		);

		$namespaces += $this->languageContents->getContentsFromLanguageById(
			$this->languageContents->getCanonicalFallbackLanguageCode(),
			'namespaces'
		);

		foreach ( $namespaces as $key => $value ) {
			unset( $namespaces[$key] );
			$namespaces[constant( $key )] = $value;
		}
		#print_r($namespaces);
		#throw new \Exception('blah');

		if ( $this->historicTypeNamespace ) {
			return $namespaces;
		}

		return $namespaces;
	}

	/**
	 * Function that returns an array of namespace aliases, if any
	 *
	 * @since 2.4
	 *
	 * @return array
	 */
	public function getNamespaceAliases() {

		$namespaceAliases = $this->languageContents->getContentsFromLanguageById(
			$this->languageCode,
			'namespaceAliases'
		);

		$namespaceAliases += $this->languageContents->getContentsFromLanguageById(
			$this->languageContents->getCanonicalFallbackLanguageCode(),
			'namespaceAliases'
		);

		foreach ( $namespaceAliases as $alias => $namespace ) {
			$namespaceAliases[$alias] = constant( $namespace );
		}

		if ( $this->historicTypeNamespace ) {
			return $namespaceAliases;
		}

		return $namespaceAliases;
	}

}
