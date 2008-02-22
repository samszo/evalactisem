<?
//Lecture de la source HTML et transformation en chaîne texte
$source = "ring.rdf";
$chaineRdf = implode(file($source), " ");

$chaineRdf=strip_tags(implode(file($source), " "));
/
/fractionnement de la chaîne de caractères en éléments mots

$tab_termes = fractionner_chaine(" .,!;?()'’\"-", $chaineRdf);

//Affichage des résultats du fractionnement
for($i=1; $i<count($tab_termes); $i++) 
echo "$i : $tab_termes[$i] <BR>";

?>