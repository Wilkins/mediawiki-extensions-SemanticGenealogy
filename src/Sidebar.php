<?php

namespace SemanticGenealogy;

/**
 * Static methods to add the Genealogy sidebar
 *
 * @file    Sidebar.php
 * @ingroup SemanticGenealogy
 *
 * @licence GNU GPL v2+
 * @author  Thibault Taillandier <thibault@taillandier.name>
 */
class Sidebar {

	/**
	 * Adds a Genealogy SideBar
	 *
	 * @param object $skin the Skin object
	 * @param array &$sidebar the editable sidebar
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/SkinBuildSidebar
	 *
	 * @return boolean always true
	 */
	public static function addGenealogySideBar( $skin, &$sidebar ) {
		global $wgSGeneaSidebarAdd, $wgSGeneaSidebarPosition;

		// User doesn't want the Sidebar
		if ( !$wgSGeneaSidebarAdd ) {
			return true;
		}

		// Genealogy Menu Page not found
		$menuText = self::getTemplateMenuContent();
		if ( ! $menuText ) {
			return true;
		}

		// Decode and check the JSON content
		$menuJson = json_decode( $menuText, true );
		if ( is_null( $menuJson ) ) {
			throw new Exception( "The Genealogy:Menu seems to contain invalid JSON." );
		}

		$geneaSidebar = [];
		$geneaTitle = 'semanticgenealogy-sidebar-title';

		// Adds menu item for each json elements
		foreach ( $menuJson as $menuItem ) {
			$text = preg_match( '#^semanticgenealogy-#', $menuItem['text'] ) ? $skin->msg( $menuItem['text'] ) : $menuItem['text'];
			$title = preg_match( '#^semanticgenealogy-#', $menuItem['title'] ) ? $skin->msg( $menuItem['title'] ) : $menuItem['title'];
			$id = strtolower( preg_replace( '[^A-Z0-9-]', '', $text ) );
			$geneaSidebar[] = [
				'text'  => $text,
				'href'  => $menuItem['href'],
				'title' => $title,
				'id'    => 'n-'.$id,
			];
		}

		// Part to insert the Genealogy Menu at the right position : $wgSGeneaSidebarPosition
		$newSidebar = [];
		$menublock = 1;
		foreach ( $sidebar as $title => $menu ) {
			if ( $menublock == $wgSGeneaSidebarPosition ) {
				$newSidebar[$geneaTitle] = $geneaSidebar;
			}
			$newSidebar[$title] = $menu;
			$menublock++;
		}
		$sidebar = $newSidebar;
		return true;
	}

	/**
	 * Retrive the Genealogy Menu Content
	 *
	 * The page is located in the Genealogy:Menu page if the page import from Special:ImportGenealogyPages has been ran
	 *
	 * @return string the Menu Content
	 */
	public static function getTemplateMenuContent() {
		require __DIR__ . "/../SemanticGenealogy.namespaces.php";
		$dbr = wfGetDb( DB_MASTER );
		$res = $dbr->select(
			[ 'revision', 'page', 'text' ],
			[ 'old_text' ],
				"page_title = 'Menu' and page_namespace=" . NS_SGENEALOGY,
				__METHOD__,
				[],
				[
					'page' => [
						'INNER JOIN',
						   [ 'rev_id=page_latest' ]
					   ],
					'text' => [
						'INNER JOIN',
						   [ 'rev_text_id=old_id' ]
					   ]
				]
			);

		if ( $res->numRows() >= 0 ) {
			foreach ( $res as $row ) {
				return $row->old_text;
			}
		}
		return "";
	}
}
