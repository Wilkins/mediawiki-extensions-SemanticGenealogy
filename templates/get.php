<?php

$pages = array(
/*
'template:Fiche_Personne',
'template:Personne_texte',
'template:Mariage_texte',
'template:Fiche_Personne_photo',
'template:Famille',
'template:PersonneContenu',
'template:Endroit',
'template:Arbre',
'template:Arbre_lieux',
'template:PoidsLieu',
'template:PersonneLieux',
'template:Galerie',
'form:Personne',
'form:Mariage',
'Aide:Accueil',
'Property:Actedeces',
'Property:Actemariage',
'Property:Actenaissance',
'Property:Anneemariage',
'Property:Anneenaissance',
'Property:Civil',
'Property:Conjoint',
'Property:Coords',
'Property:Date_mariage',
'Property:Datedeces',
'Property:Datenaissance',
'Property:Datephoto',
'Property:Endroit',
'Property:Epouse',
'Property:Epoux',
'Property:Utilise_le_formulaire',
'Property:Has_fullpagename',
'Property:Jeunefille',
'Property:Lacune',
'Property:Lieudeces',
'Property:Lieunaissance',
'Property:Mapdata',
'Property:MapdataCircle',
'Property:Mariageparent',
'Property:Mere',
'Property:Montre',
'Property:Name',
'Property:Nom',
 */
);

$baseurl = "http://famille:edouard@wiki.familletaillandier.com/index.php?title=%s&action=raw";
$prefix = "";


foreach ($pages as $page) {
	$url = sprintf($baseurl, $page);
	shell_exec("wget \"$url\" -O $prefix$page.txt");
}
