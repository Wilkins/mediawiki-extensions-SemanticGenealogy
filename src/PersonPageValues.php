<?php

namespace SemanticGenealogy;

use SMWDataItem;
use SMWDIBlob;
use SMWDIProperty;
use SMWDITime;
use SMWDIWikiPage;
use SMWTimeValue;
use Title;
use SemanticGenealogy\SemanticGenealogy;

/**
 * Model object that store genealogical data of a person
 *
 * @file    PersonPageValues.php
 * @ingroup SemanticGenealogy
 *
 * @licence GNU GPL v2+
 * @author  Thomas Pellissier Tanon <thomaspt@hotmail.fr>
 */
class PersonPageValues {
	protected $page;
	public $title;
	public $fullname;
	public $surname;
	public $givenname;
	public $nickname;
	public $prefix;
	public $suffix;
	public $gender;
	public $birthdate;
	public $birthplace;
	public $deathdate;
	public $deathplace;
	public $father;
	public $mother;
	public $partner;
	public $sosa;
	protected $children;

	/**
	 * Constructor for a single indi in the file.
	 *
	 * @param SMWDIWikiPage $page the page
	 */
	public function __construct( SMWDIWikiPage $page, $gender = null ) {
		#print_r( $page );
		$values = [];
		$storage = smwfGetStore();
		$this->page = $page;
		$this->title = $page->getTitle();
		$this->gender = $gender;
		$properties = SemanticGenealogy::getProperties();
		foreach ( $properties as $key => $prop ) {
			$values = $storage->getPropertyValues( $page, $prop );
			if ( count( $values ) != 0 && property_exists( 'SemanticGenealogy\PersonPageValues', $key ) ) {
				$this->$key = $values[0];
				/*
				if ( $values[0] instanceof SMWDIBlob ) {
					echo $values[0]->getString()."\n";
				} else if ( $values[0] instanceof SMWDIWikiPage && $maxLevel ) {
					#print_r( $values[0]->getTitle() );
					#echo $values[0]->getString()."\n";
					echo $this->givenname->getString();
						$this->$key = new PersonPageValues( $values[0], $maxLevel-1 );
					if ( $this->givenname->getString() == 'Alice' ) {
					}
				}
				*/
			}

		}

		if ( !( $this->fullname instanceof SMWDIBlob ) ) {
			if ( $this->surname instanceof SMWDIBlob && $this->surname->getString() != '' ) {
				$fullname = '';
				if ( $this->givenname instanceof SMWDIBlob ) {
					$fullname .= $this->givenname->getString() . ' ';
				}
				$this->fullname = new SMWDIBlob( $fullname . $this->surname->getString() );
			} else {
				$this->fullname = new SMWDIBlob( $this->title->getText() );
			}
		}
	}

	/**
	 * Get the page of the person
	 *
	 * @return SMWDIWikiPage the current page
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * Return all the children as PersonPageValues
	 *
	 * @return array
	 */
	public function getChildren() {
		if ( $this->children !== null ) {
			return $this->children;
		}

		$this->children = [];
		$storage = smwfGetStore();
		$properties = SemanticGenealogy::getProperties();
		if ( $properties['father'] instanceof SMWDIProperty ) {
			$childrenPage = $storage->getPropertySubjects( $properties['father'], $this->page );
			foreach ( $childrenPage as $page ) {
				$this->children[] = new PersonPageValues( $page );
			}
		}
		if ( $properties['mother'] instanceof SMWDIProperty ) {
			$childrenPage = $storage->getPropertySubjects( $properties['mother'], $this->page );
			foreach ( $childrenPage as $page ) {
				$this->children[] = new PersonPageValues( $page );
			}
		}

		usort( $this->children, [ "SemanticGenealogy\PersonPageValues", "comparePeopleByBirthDate" ] );
		return $this->children;
	}

	/**
	 * Return the partner
	 *
	 * @return array
	 */
	public function getPartner() {
		if ( $this->partner !== null ) {
			return $this->partner;
		}

		$storage = smwfGetStore();
		$properties = SemanticGenealogy::getProperties();
		if ( $properties['partner'] instanceof SMWDIProperty ) {
			$page = $storage->getPropertySubjects( $properties['partner'], $this->page );
			if ( $page instanceof SMWDIWikiPage ) {
				$this->partner = new PersonPageValues( $page );
			}
		}
		return $this->partner;
	}

	/**
	 * Sorter to compare 2 persons based on their date of birth
	 *
	 * @return integer a comparaison integer
	 */
	private static function comparePeopleByBirthDate( PersonPageValues $personA,
		PersonPageValues $personB ) {
		if ( $personA->birthdate instanceof SMWDITime ) {
			$aKey = $personA->birthdate->getSortKey();
		} else {
			$aKey = 3000;
		}

		if ( $personB->birthdate instanceof SMWDITime ) {
			$bKey = $personB->birthdate->getSortKey();
		} else {
			$bKey = 3000;
		}

		if ( $bKey < $aKey ) {
			return 1;
		} elseif ( $bKey == $aKey ) {
			return 0;
		} else {
			return -1;
		}
	}

	/**
	 * Get the correct name to display a person (either the fullname, or the pagename)
	 *
	 * @param string $displayName the name to display
	 *
	 * @return string the name of the person
	 */
	public function getFatherName( ) {
		$storage = smwfGetStore();
		$properties = SemanticGenealogy::getProperties();
		$values = $storage->getPropertyValues( $this->page, $properties['father'] );
		if ( sizeof( $values ) && $values[0] instanceof SMWDIWikiPage ) {
			$this->father = new PersonPageValues( $values[0] );
			return $this->father->title->getFullText();
		}
		return false;
	}

	/**
	 * Get the correct name to display a person (either the fullname, or the pagename)
	 *
	 * @param string $displayName the name to display
	 *
	 * @return string the name of the person
	 */
	public function getMotherName( ) {
		$storage = smwfGetStore();
		$properties = SemanticGenealogy::getProperties();
		$values = $storage->getPropertyValues( $this->page, $properties['mother'] );
		if ( sizeof( $values ) && $values[0] instanceof SMWDIWikiPage ) {
			$this->mother = new PersonPageValues( $values[0] );
			return $this->mother->title->getFullText();
		}
		return false;
	}

	/**
	 * Get the correct name to display a person (either the fullname, or the pagename)
	 *
	 * @param string $displayName the name to display
	 *
	 * @return string the name of the person
	 */
	public function getPersonName( $displayName = 'pagename' ) {
		if ( $displayName == 'pagename' ) {
			return $this->title->getFullText();
		} elseif ( $displayName == 'fullname' ) {
			return $this->fullname->getString();
		}
		return $this->title->getFullText();
	}

	public function photoExists() {
		$pagename = 'Fichier:'.$this->getPersonName().'.jpg';
		return SemanticGenealogy::pageExists( $pagename );
	}

	/**
	 * Generate the Person description wiki text based on the special pages options
	 *
	 * @param boolean $withBr adding <br> tags or not
	 * @param string $displayName the display type tag
	 *
	 * @return string the text to display
	 */
	public function getDescriptionWikiText(
		$withBr = false,
	   	$displayName = 'fullname',
		$sosa = null,
		$withPhoto = false
		) {
		$yearRegexp = "/.*\b(\d\d\d\d)\b.*/";
		$text = "\n".'<div class="person-block '.( $withPhoto ? ' with-photo' : '' ).'">';
		$text .= $sosa ? '<span class="sosa-num">'.$sosa.'</span>' : '';
		$text .= "\n".'<div class="person-name">';
		if ( $withPhoto ) {
			if ( $this->photoExists() ) {
				$text .= '[[Fichier:' . $this->getPersonName(). '.jpg|frameless|70px|Photo]]<br/>';
			} else {
				$text .= '[[Fichier:Portrait_silouhette.png|frameless|70px|Photo]]<br/>';
			}
		}
		$person_name_display = $this->getPersonName( $displayName );
		$person_name_display = preg_replace( '/ \(.*\)/', '', $person_name_display );
		if ( SemanticGenealogy::pageExists( $this->title->getFullText() ) ) {
			$text .= '[[' . $this->title->getFullText() . '|' . $person_name_display . ']]';
		} else {
			$text .= '{{#formlink:form=Personne'
			.'|link text='.$person_name_display
				.'|target='.$this->title->getFullText().' }}';
		}
		if ( $this->birthdate || $this->deathdate ) {
			$text .= "\n".'<div class="person-dates">';
			if ( $withBr ) {
				//$text .= '<br />';
			}
			$text .= '(';
			if ( $this->birthdate instanceof SMWDITime ) {
				$text .= static::getWikiTextDateFromSMWDITime( $this->birthdate ) . ' ';
			} elseif ( is_string( ( string ) $this->birthdate ) 
				&& preg_match( $yearRegexp, ( string ) $this->birthdate ) ) {
				$text .= preg_replace( $yearRegexp, "$1", $this->birthdate );
			}
			$text .= '-';
			if ( $this->deathdate instanceof SMWDITime ) {
				$text .= ' ' . static::getWikiTextDateFromSMWDITime( $this->deathdate );
			} elseif ( is_string( ( string ) $this->deathdate ) 
				&& preg_match( $yearRegexp, ( string ) $this->deathdate ) ) {
				$text .= preg_replace( $yearRegexp, "$1", $this->deathdate );
			}
			$text .= ')</div>';
		}
		$text .= '</div>';
		$text .= '</div>';
		return $text;
	}

	/**
	 * Get a string base on the SMWDITime object
	 *
	 * @param SMWDITime $dataItem the time item
	 *
	 * @return string the wiki text for a given date
	 */
	protected static function getWikiTextDateFromSMWDITime( SMWDITime $dataItem ) {
		$val = new SMWTimeValue( SMWDataItem::TYPE_TIME );
		$val->setDataItem( $dataItem );
		return $val->getShortWikiText();
	}

	/**
	 * Get the page from the pageName
	 *
	 * @param string $pageName the page name
	 *
	 * @return SMWDIWikiPage the page object
	 */
	public static function getPageFromName( $pageName ) {
		$pageTitle = Title::newFromText( $pageName );
		return SMWDIWikiPage::newFromTitle( $pageTitle );
	}

	/**
	 * Get the sosa number
	 *
	 * @return number sosa
	 */
	public function getSosa() {
		$storage = smwfGetStore();
		$properties = SemanticGenealogy::getProperties();
		$values = $storage->getPropertyValues( $this->page, $properties['sosa'] );
		if ( sizeof( $values ) && get_class( $values[0] ) == 'SMWDINumber' ) {
			return $values[0]->getNumber();
		}
		return false;
	}


	/**
	 * Get the gender
	 *
	 * @return gender
	 */
	public function getGender() {
		if ( $this->gender ) {
			return $this->gender;
		}
		$storage = smwfGetStore();
		$properties = SemanticGenealogy::getProperties();
		$values = $storage->getPropertyValues( $this->page, $properties['gender'] );
		if ( sizeof( $values ) && get_class( $values[0] ) == 'SMWDIBlob' ) {
			return $values[0]->getString();
		}
		return false;
	}



}
