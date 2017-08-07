<?php

#namespace SemanticGenealogy;

#use Importer;

/**
 * Special page that show a family tree
 *
 * @file    SpecialImportFiles.php
 * @ingroup SemanticGenealogy
 *
 * @licence GNU GPL v2+
 * @author  Thibault Taillandier <thibault@taillandier.name>
 */
class SpecialImportPages extends SpecialPage
{

	const PAGE_NEW = "NEW";
	const PAGE_UNKNOWN = "UNKNOWN";
	const PAGE_OK = "OK";
	const PAGE_UPTODATE = "UPTODATE";

	/**
	 * @constructor
	 *
	 * @param string $name the name of the SpecialPage
	 *
	 * @return void
	 */
	public function __construct( $name = 'ImportGenealogyPages' ) {
		parent::__construct( $name, '' );
		$this->mIncludable = false;
	}

	/**
	 * Execute the Special Page
	 *
	 * @param string $par the url part
	 *
	 * @return boolean the status of the rendered page
	 */
	public function execute( $par ) {
		global $wgOut, $wgLang, $wgRequest;
		$this->setHeaders();

		//print_r( $wgRequest->getText('action')	);

		$this->showForm();

		if ( $wgRequest->getText('action') != 'import' ) {
			return Status::newGood();
		}

		$output = $this->getOutput();
		$lang = $wgLang->getCode();

		try {
			$importer = new Importer( $lang );
			$files = $importer->listFiles();
			foreach( $files as $displayName => $file ) {
				$wgOut->addWikiText( "Import de $displayName" );
				$importer->importFile( $file );
			}
		} catch ( SemanticGenealogyException $e ) {
			$wgOut->addWikiText( '<span class="error">' .  $e->getMessage() . '</span>' );
			return Status::newFatal( $e->getMessage() );
		} catch ( Exception $e ) {
			$wgOut->addWikiText( '<span class="error">' .  $e->getMessage() . '</span>' );
			return Status::newFatal( $e->getMessage() );
		}
		return Status::newGood();
	}

	/**
	 * Display the search form for a genealogy tree
	 *
	 * @param array $params the array of search parameters
	 *
	 * @return void
	 */
	protected function showForm() {

		global $wgScript, $wgLang;

		if ( $this->mIncluding ) {
			return false;
		}
		$output = $this->getOutput();
		$output->addModuleStyles( 'ext.smg.specialfamilytree' );
		$lang = $wgLang->getCode();

		$importer = new Importer( $lang );
		$files = $importer->listFiles();
		$output->addHTML( '<table id="semanticgenealogy-import-form"><tr>' );
		$output->addHTML( '<th>' . $this->msg( 'semanticgenealogy-specialimportpages-column-pagename' )->text() . '</th>' );
		$output->addHTML( '<th>' . $this->msg( 'semanticgenealogy-specialimportpages-column-version' )->text() . '</th>' );
		$output->addHTML( '<th>' . $this->msg( 'semanticgenealogy-specialimportpages-column-status' )->text() . '</th>' );
		$output->addHTML( '</tr>' );

		foreach ( $files as $displayName => $file ) {
			$status = null;
			$class = null;
			$version = $this->getVersionFromPagename( $displayName );

			if ( in_array( $version, [ self::PAGE_NEW, self::PAGE_UNKNOWN ] ) ) {
				$lversion = strtolower( $version );
				$version = '';
			} else {
				echo SGENEA_VERSION;
				if ( $version == SGENEA_VERSION ) {
					$lversion = strtolower( self::PAGE_UPTODATE );
				} else {
					$lversion = strtolower( self::PAGE_OK  );
				}
			}
			$status = $this->msg( 'semanticgenealogy-specialimportpages-status-'.$lversion )->text();
			$class = "status-" . $lversion;
			$output->addHTML( '<tr class="' . $class . '"><td>' );
			$isCat = preg_match( '/^Category:/', $displayName );
			$output->addWikiText( '[[' . ( $isCat ? ':' : '' ) . $displayName . ']]' );
			$output->addHTML( '</td>' );

			$output->addHTML( '<td>' .SGENEA_VERSION . ' - '. $version . ' - '. $lversion.'</td><td>' . $status . '</td></tr>' );
		}
		$output->addHTML( '</table>' );


		$output->addHTML(
			Xml::openElement( 'form', [ 'action' => $wgScript ] ) .
			Html::hidden( 'title', $this->getPageTitle()->getPrefixedText() ) .
			Html::hidden( 'action', 'import' ) .
			Xml::openElement( 'fieldset' ) .
			Xml::openElement( 'table', [ 'id' => 'smg-importpages-form' ] ) .
			Xml::closeElement( 'table' ) .
			Xml::submitButton( $this->msg( 'semanticgenealogy-specialimportpages-button-submit' )->text() ) .
			Xml::closeElement( 'fieldset' ) .
			Xml::closeElement( 'form' )
		);
	}

	/**
	 * Get version from the pagename
	 *
	 * @param string $fullPageName the fullpagename
	 *
	 * @return string the version
	 */
	public function getVersionFromPagename( $fullPageName ) {

		$comment = $this->getCommentFromPagename( $fullPageName );

		if ( $comment === -1 ) {
			return self::PAGE_NEW;
		} elseif ( preg_match( "#\(v(\d+\.\d+\.\d+)\)#", $comment ) ) {
			// if ( preg_match( "#\(v" . SGENEA_VERSION . "\)#", $comment ) ) {
			//	return self::PAGE_UPTODATE;
			// } else {
			return preg_replace( "#.*\(v(\d+\.\d+\.\d+)\).*#", "$1", $comment );
			// }
		}
		return self::PAGE_UNKNOWN;
	}

	/**
	 * Get comment from the page name
	 *
	 * @param string $fullPageName the fullpagename
	 *
	 * @return string the comment
	 */
	public function getCommentFromPagename( $fullPageName ) {
		list( $namespace, $pagename ) = explode( ':', $fullPageName );

		$nsId = SemanticGenealogy::getNamespaceFromName( $namespace );
		return $this->getComment ( $nsId, $pagename );
	}

	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Database_access
	 */
	public function getComment( $namespace, $pagename ) {
		$dbr = wfGetDb( DB_MASTER );
		$res = $dbr->select(
			[ 'revision', 'page' ],
			[ 'rev_comment' ],
				"page_title = '$pagename' and page_namespace=$namespace",
				__METHOD__,
				[],
				[
					'page' => [
						'INNER JOIN',
					   	[ 'rev_id=page_latest' ]
				   	]
				]
		);

		if ( $res->result->num_rows >= 0 ) {
			foreach( $res->result as $row ) {
				return $row['rev_comment'];
			}
		}
		return -1;
		//throw new SemanticGenealogyException( "" );
	}

	/**
	 * Wether the page is cachable
	 *
	 * @return boolean
	 */
	public function isCacheable() {
		return false;
	}

	/**
	 * Get the description
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->msg( 'semanticgenealogy-specialimportpages-title' )->text();
	}

	/**
	 * Get the groupe name
	 *
	 * @return string
	 */
	protected function getGroupName() {
		return 'genealogy';
	}
}
