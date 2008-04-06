<?php
$tab_index=array('partir','donnee','de','la','maison');
$tab_noir=array('de','la');
$tab_sans_noirs=array_diff($tab_index,$tab_noir);

print_r( $tab_sans_noirs);
?>