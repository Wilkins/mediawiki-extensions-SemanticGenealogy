<?php

namespace SemanticGenealogy\Tree;

use SemanticGenealogy\PersonPageValues;

/**
 * AncestorsFamilyTree object
 *
 * Handle a FamilyTree to display the ancestors of the person
 *
 * @file    AncestorsFamilyTree.php
 * @ingroup SemanticGenealogy
 *
 * @licence GNU GPL v2+
 * @author  Thomas Pellissier Tanon <thomaspt@hotmail.fr>
 * @author  Thibault Taillandier <thibault@taillandier.name>
 */
class AncestorsFamilyTree extends FamilyTree {

	const NAME = 'ancestors';

	/**
	 * List the ancestors of the person for all needed generations
	 *
	 * @return array the generations tree
	 */
	private function getAncestors() {
		$tree = [];
		$tree[0][1] = new PersonPageValues( $this->person );

		for ( $i = 0; $i < $this->numOfGenerations && $tree[$i] !== null; $i++ ) {
			$tree = $this->addGenInTree( $i + 1, $tree );
		}
		return $tree;
	}

	/**
	 * Render the tree of ancestors
	 *
	 * @return void
	 */
	public function render() {
		$tree = $this->getAncestors();
		$output = $this->getOutput();
		$output->addHTML( '<table class="decorator-'.$this->decorator. ' smg-tree-root-ancestors">' );

		$col = 1;
		for ( $i = $this->numOfGenerations - 1; $i >= 0; $i-- ) {
			if ( isset( $tree[$i] ) ) {
				#print_r($tree[$i]);
				$output->addHTML( '<tr class="smg-tree-line smg-tree-gen-row-'.$i.'">' );
				#print_r(array_keys($tree[$i]));

				foreach ( $tree[$i] as $sosa => $person ) {
					#echo $sosa."-";
					#continue;
					//$mariage = false;
					//$mariageText = "";
					/*
					if ($person === null ) {
						echo "$sosa : null<br>\n";
					} else {
						echo "$sosa : ".($person->getPersonName('pagename'))." ".($person === null ? "null" : "pas null")." <br>\n";
					}
					*/
					if ( $person !== null ) {
						$fatherName = $person->getFatherName( );
						$motherName = $person->getMotherName( );
						//"Mariage de $fatherName et de $motherName";
						//".$person->getFatherName( )." et "; //$tree[$i][$sosa-1]->getMotherName( );
						$output->addHTML( '<td class="smg-tree-person col-width-'.$col.'" colspan="'.$col.'">' );
						if ( $fatherName && $motherName ) {
							$mariageLink = wfMessage( 'semanticgenealogy-specialfamilytree-marriage-link', $fatherName, $motherName )->text();
							$mariageText = wfMessage( 'semanticgenealogy-specialfamilytree-marriage-title' )->text();
							$output->addHTML( '<table class="smg-tree-marriage"><tr><td colspan="2">' );
							
							$title = \Title::newFromDbKey( $mariageLink );
							$page = \WikiPage::factory( $title );
							if ( $page->exists() ) {
								$output->addWikiTextAsContent( '[['.$mariageLink.'|'.$mariageText.']]' );
							} else {
								$output->addWikiTextAsContent( '{{#formlink:form=Mariage|link text='.$mariageText.'|target='.$mariageLink.' }}' );
							}
							$output->addHTML( '</td></tr><tr><td></td><td></td></tr></table>' );
						}
						//$output->addHTML( '<span class="sosa-num">'.$sosa.'</span>' );
						$sosa = $person->getSosa(); 
						$output->addWikiTextAsContent( $person->getDescriptionWikiText( true, $this->displayName , $sosa ) );
						if ( $sosa != 1 ) {
							if ( $sosa % 2 == 0 ) {
								$output->addHTML( '<table class="father-link"><tr><td></td><td></td><td></td></tr>'
									.'<tr><td></td><td></td><td></td></tr></table>' );
							} else {
								$output->addHTML( '<table class="mother-link"><tr><td></td><td></td><td></td></tr>'
									.'<tr><td></td><td></td><td></td></tr></table>' );
								/*
								if ( $tree[$i][$sosa-1] !== null ) {
									$mariage = true;
									$mariageText = "Mariage de ".$person->getFatherName( )." et ".$tree[$i][$sosa-1]->getMotherName( );
								}
								*/
							}
						}
					} else {
						$output->addHTML( 
							'<td class="smg-tree-person col-width-'.$col.' person-empty" colspan="'.$col.'">'
						);
						//$output->addWikiTextAsContent( 'empty' );
						$output->addHTML( '</td>' );
					}
					#$output->addHTML( '</td>' );
					/*
					if ( $mariage ) {
						$output->addHTML( '<td class="smg-tree-person col-width-'.$col.'" colspan="'.$col.'"> ' );
						$output->addHTML( 'MARIAGE : '.$mariageText );
						$output->addHTML( '</td>' );
					}
					 */
				}
				$output->addHTML( '</tr>' );
			}
			$col *= 2;
		}
		$output->addHTML( '</table>' );
	}
}
