<?
//Lecture de la source HTML et transformation en cha�ne texte
$source = "ring.rdf";
$chaineRdf = implode(file($source), " ");

$chaineRdf=strip_tags(implode(file($source), " "));
/
/fractionnement de la cha�ne de caract�res en �l�ments mots

$tab_termes = fractionner_chaine(" .,!;?()'�\"-", $chaineRdf);

//Affichage des r�sultats du fractionnement
for($i=1; $i<count($tab_termes); $i++) 
echo "$i : $tab_termes[$i] <BR>";

?>