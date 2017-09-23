<?php
/**
 * Initialization file for the Semantic Genealogy extension.
 *
 * On MediaWiki.org: http://www.mediawiki.org/wiki/Extension:Semantic_Genealogy
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

if ( version_compare( $wgVersion, '1.19', '<' ) ) {
	die( '<b>Error:</b> This version of Semantic Genealogy requires MediaWiki 1.19 or above.' );
}

// Show a warning if Semantic MediaWiki is not loaded.
if ( ! defined( 'SMW_VERSION' ) ) {
	die( '<b>Error:</b> You need to have '
		.'<a href="http://semantic-mediawiki.org/wiki/Semantic_MediaWiki">Semantic MediaWiki</a> '
		.'installed in order to use '
		.'<a href="http://www.mediawiki.org/wiki/Extension:Semantic Maps">Semantic Maps</a>.<br />'
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
		'[http://www.mediawiki.org/wiki/User:Tpt Tpt]'
	],
	'url' => 'https://www.mediawiki.org/wiki/Extension:Semantic_Genealogy',
	'descriptionmsg' => 'semanticgenealogy-desc'
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
	'mother' => 'Mère'
];

$dir = __DIR__ . '/';
$dirTree = $dir.'src/Tree/';
$dirDecorator = $dir.'src/Decorator/';

$wgMessagesDirs['SemanticGenealogy'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['SemanticGenealogy'] =  $dir . 'SemanticGenealogy.i18n.php';
$wgExtensionMessagesFiles['SemanticGenealogyAlias'] = $dir . 'SemanticGenealogy.alias.php';
$wgExtensionMessagesFiles['SemanticGenealogyNamespaces'] = __DIR__ . '/SemanticGenealogy.namespaces.php';

$wgAutoloadClasses['SemanticGenealogy'] = $dir . 'SemanticGenealogy.body.php';
$wgAutoloadClasses['PersonPageValues'] = $dir . 'PersonPageValues.php';
$wgAutoloadClasses['FamilyTree'] = $dirTree . 'FamilyTree.php';
$wgAutoloadClasses['AncestorsFamilyTree'] = $dirTree . 'AncestorsFamilyTree.php';
$wgAutoloadClasses['DescendantFamilyTree'] = $dirTree . 'DescendantFamilyTree.php';
$wgAutoloadClasses['LinkFamilyTree'] = $dirTree . 'LinkFamilyTree.php';
$wgAutoloadClasses['FamilyTreeFactory'] = $dirTree . 'FamilyTreeFactory.php';
$wgAutoloadClasses['TreeDecoratorFactory'] = $dirDecorator . 'TreeDecoratorFactory.php';
$wgAutoloadClasses['TreeDecorator'] = $dirDecorator . 'TreeDecorator.php';
$wgAutoloadClasses['SimpleDecorator'] = $dirDecorator . 'SimpleDecorator.php';
$wgAutoloadClasses['BoxDecorator'] = $dirDecorator . 'BoxDecorator.php';
$wgAutoloadClasses['Tools'] = $dir . 'Tools.php';

$wgAutoloadClasses['SemanticGenealogyException'] = $dir . 'SemanticGenealogyException.php';

$wgAutoloadClasses['GenealogicalFilePrinter'] = $dir . 'GenealogicalFilePrinter.php';
$wgAutoloadClasses['Gedcom5FilePrinter'] = $dir . 'Gedcom5FilePrinter.php';
$wgAutoloadClasses['Gedcom5ResultPrinter'] = $dir . 'Gedcom5ResultPrinter.php';
$smwgResultFormats['gedcom'] = 'Gedcom5ResultPrinter';
$smwgResultFormats['gedcom5'] = 'Gedcom5ResultPrinter';

$wgAutoloadClasses['Importer'] = $dir . '/src/Importer.php';
$wgAutoloadClasses['Sidebar'] = $dir . '/src/Sidebar.php';
$wgAutoloadClasses['SpecialFamilyTree'] = $dir . 'SpecialFamilyTree.php';
$wgAutoloadClasses['SpecialImportPages'] = $dir . '/src/SpecialImportPages.php';
$wgSpecialPages['FamilyTree'] = 'SpecialFamilyTree';
$wgSpecialPages['ImportGenealogyPages'] = 'SpecialImportPages';

$wgHooks['SkinBuildSidebar'][] = 'Sidebar::addGenealogySideBar';

$wgSGeneaSidebarAdd = true;
$wgSGeneaSidebarPosition = 2;

$moduleTemplate = [
	'localBasePath' => $dir,
	'remoteBasePath' => ( $wgExtensionAssetsPath === false ? $wgScriptPath
		. '/extensions' : $wgExtensionAssetsPath ) . '/SemanticGenealogy',
	'group' => 'ext.smg'
];

$wgResourceModules['ext.smg.specialfamilytree'] = $moduleTemplate + [
	'scripts' => 'modules/specialFamilyTree.js',
	'styles' => 'modules/styles.css',
	'dependencies' => [ 'jquery.ui.autocomplete' ],
	'messages' => [
	]
];
