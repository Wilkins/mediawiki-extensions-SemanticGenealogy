<?php
/**
 * Initialization file for the Semantic Genealogy extension.
 *
 * On MediaWiki.org: https://www.mediawiki.org/wiki/Extension:Semantic_Genealogy
 *
 * @file    SemanticGenealogy.php
 * @ingroup SemanticGenealogy
 *
 * @licence GNU GPL v2+
 * @author  Thomas Pellissier Tanon <thomaspt@hotmail.fr>
 */

/**
 * This documentation group collects source code files belonging to Semantic Genealogy.
 *
 * Please do not use this group name for other code.
 * If you have an extension to Semantic Genealogy, please use your own group definition.
 *
 * @defgroup SemanticGenealogy Semantic Genealogy
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

if ( version_compare( $wgVersion, '1.23', '<' ) ) {
	die( '<b>Error:</b> This version of Semantic Genealogy requires MediaWiki 1.23 or above.' );
}

// Show a warning if Semantic MediaWiki is not loaded.
if ( ! defined( 'SMW_VERSION' ) ) {
	die( '<b>Error:</b> You need to have '
		.'<a href="https://semantic-mediawiki.org/wiki/Semantic_MediaWiki">Semantic MediaWiki</a> '
		.'installed in order to use '
		.'<a href="https://www.mediawiki.org/wiki/Extension:Semantic_Genealogy">Semantic Genealogy</a>.<br />'
	);
}

if ( version_compare( SMW_VERSION, '1.7.0 alpha', '<' ) ) {
	die( '<b>Error:</b> This version of Semantic Genealogy requires '
		.'Semantic MediaWiki 1.7 or above.' );
}

// Beware, SG_VERSION conflicts with SemanticGlossary
if ( !defined( 'SGENEA_VERSION' ) ) {
	define( 'SGENEA_VERSION', '0.3.0' );
}

$wgExtensionCredits['semantic'][] = [
	'path' => __FILE__,
	'name' => 'Semantic Genealogy',
	'version' => SGENEA_VERSION,
	'author' => [
		'[https://www.mediawiki.org/wiki/User:Tpt Tpt]',
		'[https://www.mediawiki.org/wiki/User:Thibault_Taillandier Thibault Taillandier]',
		'...'
	],
	'url' => 'https://www.mediawiki.org/wiki/Extension:Semantic_Genealogy',
	'descriptionmsg' => 'semanticgenealogy-desc',
	'license-name' => 'GPL-2.0+'
];

$wgGenealogicalProperties = [
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
	'partner' => 'Conjoint'
];

$wgMessagesDirs['SemanticGenealogy'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['SemanticGenealogy'] =  __DIR__ . '/SemanticGenealogy.i18n.php';
$wgExtensionMessagesFiles['SemanticGenealogyAlias'] = __DIR__ . '/SemanticGenealogy.alias.php';
$wgExtensionMessagesFiles['SemanticGenealogyNamespaces'] = __DIR__ . '/SemanticGenealogy.namespaces.php';

$smwgResultFormats['gedcom'] = 'SemanticGenealogy\Gedcom\Gedcom5ResultPrinter';
$smwgResultFormats['gedcom5'] = 'SemanticGenealogy\Gedcom\Gedcom5ResultPrinter';

$wgSpecialPages['FamilyTree'] = 'SemanticGenealogy\SpecialFamilyTree';
$wgSpecialPages['ImportGenealogyPages'] = 'SemanticGenealogy\SpecialImportPages';

$wgHooks['SkinBuildSidebar'][] = 'SemanticGenealogy\Sidebar::addGenealogySideBar';

$wgSGeneaSidebarAdd = true;
$wgSGeneaSidebarPosition = 2;

$autoloadFile = __DIR__.'/vendor/autoload.php';
if ( file_exists( $autoloadFile ) ) {
	require_once $autoloadFile;
} else {
	throw new Exception( "File $autoloadFile is missing. It is required to load all the SemanticGenealogy classes." );
}

$moduleTemplate = [
	'localBasePath' => __DIR__,
	'remoteBasePath' => ( $wgExtensionAssetsPath === false ? $wgScriptPath
		. '/extensions' : $wgExtensionAssetsPath ) . '/SemanticGenealogy',
	'group' => 'ext.smg'
];

$wgResourceModules['ext.smg.specialfamilytree'] = $moduleTemplate + [
	'scripts' => 'modules/specialFamilyTree.js',
	'styles' => 'modules/styles.css',
	'dependencies' => [
		'jquery.ui.autocomplete'
	],
	'messages' => [
	]
];
