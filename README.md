Semantic Genealogy
==================

Features
--------

* Special page to generate a FamilyTree : `Special:FamilyTree`
* Include FamilyTree inside pages with a wiki code
* Several Trees: Ancestors Tree, Descendants Tree, Relation Link Tree, Descendants list
* Several Styles: Simple, Boxes
* GEDCOM file format export


Requirements
------------

Semantic Genealogy requires:
* MediaWiki 1.23 or above
* Semantic MediaWiki 1.7 or above
* PHP 5.4 or above


Installation
------------

#### Installation from source

Installation instructions are available online in a more convenient form for
reading at https://www.mediawiki.org/wiki/Extension:Semantic_Genealogy

Copy all files into MediaWiki's extensions folder, either by using Git or by
extracting an installation package. You need to enter one line to your local
settings (somewhere after the inclusion of Semantic MediaWiki):


#### Installation from composer

Not yet.


Configuration
-------------

```php
// Semantic Genealogy
require_once "$IP/extensions/SemanticGenealogy/SemanticGenealogy.php";

// Insert the $wgGenealogicalProperties array to specify which Semantic properties match which concept.
// The properties can differ if you used personnal nouns that fit your language.

// Here is an example of a french configuration
$wgGenealogicalProperties = array(
    'givenname' => 'Prenom',
    'surname' => 'Nom',
    'nickname' => 'Surnom',
    'sex' => 'Sexe',
    'birthdate' => 'Datenaissance',
    'birthplace' => 'Lieunaissance',
    'deathdate' => 'Datedeces',
    'deathplace' => 'Lieudeces',
    'father' => 'Pere',
    'mother' => 'Mere',
    'partner' => 'Conjoint'
);

// Here is an example of an english configuration
$wgGenealogicalProperties = array(
    'givenname' => 'Firstname',
    'surname' => 'Lastname',
    'nickname' => 'Nickname',
    'sex' => 'Sex',
    'birthdate' => 'date of birth',
    'birthplace' => 'place of birth',
    'deathdate' => 'date of death',
    'deathplace' => 'place of death',
    'father' => 'Father',
    'mother' => 'Mother',
    'partner' => 'Conjoint'
);

```

Known issues
------------
* This extension is usable under several languages, however it is not designed to work for multiple languages at the same time. They properties would not be shared between languages.
* Imported templates use the english namespace, but your language name should work fine if you call it.
* When showing descendant tree, the tree uses the actuel Partner define with property "partner", and show all the children of the person. Therefore wrong parent/child associations can be displayed in a tree, because we don't handle half brother/sisters yet
* Dates are just string for now.

Documentation
-------------
* `$wgSGeneaSidebarAdd` : allows you to decide wether the Genealogical sidebar is added to your sidebar (default: true)
* `$wgSGeneaSidebarPosition` : the position of the Genealogical sidebar in your sidebar. Useless if `$wgSGeneaSidebarAdd = false` (default: 2)



Authors
-------

* Thomas Pellissier Tanon <thomaspt@hotmail.fr> (Author, Maintainer)
* Thibault Taillandier <thibault@taillandier.name> (Developer)


Links
-----

* Official extension page : https://www.mediawiki.org/wiki/Extension:Semantic_Genealogy

