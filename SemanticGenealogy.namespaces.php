<?php
/*
$namespaceNames = [];

// For wikis without Semantic Genealogy installed.
// @see https://www.mediawiki.org/wiki/Extension_default_namespaces#Semantic_Genealogy
if ( !defined( 'NS_SGENEALOGY' ) ) {
	define( 'NS_SGENEALOGY', 2700 );
	define( 'NS_SGENEALOGY_TALK', 2701 );
	define( 'NS_SGENEALOGY_TEMPLATE', 2702 );
	define( 'NS_SGENEALOGY_TEMPLATE_TALK', 2703 );
	define( 'NS_SGENEALOGY_FORM', 2704 );
	define( 'NS_SGENEALOGY_FORM_TALK', 2705 );
}

$namespaceNames['en'] = [
	NS_SGENEALOGY => 'Genealogy',
	NS_SGENEALOGY_TALK => 'Genealogy_talk',
	NS_SGENEALOGY_TEMPLATE => 'Genealogy_template',
	NS_SGENEALOGY_TEMPLATE_TALK => 'Genealogy_template_talk',
	NS_SGENEALOGY_FORM => 'Genealogy_form',
	NS_SGENEALOGY_FORM_TALK => 'Genealogy_form_talk',
];
 */
/*
$namespaceNames['fr'] = [
	NS_SGENEALOGY => 'Généalogie',
	NS_SGENEALOGY_TALK => 'Discussion_généalogie',
	NS_SGENEALOGY_TEMPLATE => 'Modèle_de_généalogie',
	NS_SGENEALOGY_TEMPLATE_TALK => 'Discussion_modèle_de_généalogie',
	NS_SGENEALOGY_FORM => 'Formulaire_de_généalogie',
	NS_SGENEALOGY_FORM_TALK => 'Discussion_formulaire_de_généalogie',
];
 */

/*
// Register the namespaces id
// @see https://www.mediawiki.org/wiki/Manual:Using_custom_namespaces#Creating_a_custom_namespace
foreach ( $namespaceNames['en'] as $nsId => $nsName ) {
	$wgExtraNamespaces[$nsId] = $nsName;
}
*/

# print_r( $wgNamespaceAliases );

#SemanticGenealogy\SemanticGenealogy::setNamespaceAliases( $namespaceNames['fr'] );

# print_r( $wgNamespaceAliases );
