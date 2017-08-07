Semantic Genealogy
==================
THIS EXTENSION IS CURRENTLY IN DEVELOPMENT AND IS NOT DONE TO BE USED IN A PRODUCTION ENVIRONMENT. BREAKING CHANGES CAN BE INTRODUCED !


### Features
* Special page to generate a FamilyTree
* Include FamilyTree inside pages with a wiki code
* Several Trees: Ancestors Tree, Descendants Tree, Relation Link Tree, Descendants list
* Several Styles: Simple, Boxes
* GEDCOM file format export



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

### Authors
* Thomas Pellissier Tanon <thomaspt@hotmail.fr> (Maintainer)


### Links
* Official extension page : https://www.mediawiki.org/wiki/Extension:Semantic_Genealogy
