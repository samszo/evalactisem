<?php header("Content-type: image/svg+xml");
$large = $_GET["large"];
$haut = $_GET["haut"];
$titre = $_GET["titre"];
$donnees = $_GET["donnees"];
$noms = $_GET["noms"];
$type = $_GET["type"];
$col1 = $_GET["col1"];
$col2 = $_GET["col2"];
$col3 = $_GET["col3"];
$col4 = $_GET["col4"];

function faire_rect($x,$y,$width,$height,$style,$id)
{print("<rect id='".$id."' x='".$x."' y='".$y."' width='".$width."' height='".$height."' style='".$style."'/>\n");}

function faire_circle($cx,$cy,$r,$style,$id)
{print("<circle id='".$id."' cx='".$cx."' cy='".$cy."' r='".$r."' style='".$style."'/>\n");}

function ecrire($data,$x,$y,$style)
{print("<text x='".$x."' y='".$y."' style='".$style."' pointer-events='none'>".$data."</text>\n");}

function faire_path($d,$style,$id)
{	if ($id != "data0")
		print("<path id='".$id."' d='".$d."' style='".$style."'/>\n");
	else
		print("<path id='".$id."' d='".$d."' style='".$style."' pointer-events='none'/>\n");
}

function analyse_donnees()
{global $donnees,$nbdonnees,$donnees_s;
$don3="0;".$donnees;
if (substr($don3,strlen($don3)-1,strlen($don3))==";") 
{$don3=substr($don3,0,strlen($don3)-1);};
$donnees_s=explode(";",$don3);
$nbdonnees=count($donnees_s)-1;}

function analyse_noms()
{global $noms,$noms_s,$nbnoms;
$don3="0;".$noms;
if (substr($don3,strlen($don3)-1,strlen($don3))==";") 
{$don3=substr($don3,0,strlen($don3)-1);};
$noms_s=explode(";",$don3);
$nbnoms=count($noms_s)-1;}

function minmax()
{global $mini,$maxi,$donnees_s,$nbdonnees;
$mini=min($donnees_s);
$maxi=max($donnees_s);}

function faire_titre()
{global $large,$haut,$titre,$col4;
ecrire($titre,round($large/2),20,"text-anchor:middle;fill:".$col4.";");}

function faire_gradu()
{global $large,$haut,$maxi,$mini,$type,$nbdonnees,$col4;
if ($type=="barre3d") {$type2="barre";} else {$type2=$type;};
switch($type2)
{CASE "pie":
break;
CASE "pie3d":
break;
CASE "barre":
$unite_y=round(($haut-60)/$nbdonnees);
if ($mini>=0) {$x0=40;$unite_x=($large-60-$unite_y)/$maxi;} else
{$unite_x=($large-60-$unite_y)/($maxi-$mini);$x0=round(40-$unite_x*$mini);};
$chemin="M".$x0." ".($haut-20)." L".$x0." 40 M".$x0." ".($haut-20)."l0 5 M40 ".($haut-20)." l".($large-60)." 0";
faire_path($chemin,'opacity:1;stroke:black;fill:none;','');
ecrire("0",$x0,$haut-5,"text-anchor:middle;fill:".$col4.";");
if ($mini>=0) {$echelle=round(log10($maxi));} else {$echelle=round(log10($maxi-$mini));};
$pas=pow(10,$echelle-1);
$base=$x0+round($pas*$unite_x);$valeur=$pas;
while ($base<=($large-20))
{$chemin="M".$base." ".($haut-20)." l0 5";faire_path($chemin,'opacity:1;stroke:black;fill:none;','');
$chemin="M".$base." ".($haut-20)." l0 -".($haut-60);faire_path($chemin,'opacity:0.3;stroke:black;fill:none;','');
ecrire($valeur,$base,$haut-5,"text-anchor:middle;fill:".$col4.";");
$base=$base+round($pas*$unite_x);$valeur=$valeur+$pas;};
if ($mini<0)
{$base=$x0-round($pas*$unite_x);$valeur=-$pas;
while ($base>=40)
{$chemin="M".$base." ".($haut-20)." l0 5";faire_path($chemin,'opacity:1;stroke:black;fill:none;','');
$chemin="M".$base." ".($haut-20)." l0 -".($haut-60);faire_path($chemin,'opacity:0.3;stroke:black;fill:none;','');
ecrire($valeur,$base,$haut-5,"text-anchor:middle;fill:".$col4.";");
$base=$base-round($pas*$unite_x);$valeur=$valeur-$pas;}};
break;
default:
$unite_x=round(($large-60)/$nbdonnees);
if ($mini>=0) {$y0=$haut-20;$unite_y=($haut-60-$unite_x)/$maxi;} else
{$unite_y=($haut-60-$unite_x)/($maxi-$mini);$y0=round($haut-20+$unite_y*$mini);};
$chemin="M40 ".($haut-20)." L40 40 M40 ".$y0."l-5 0 M40 ".$y0." l".($large-60)." 0";
faire_path($chemin,'opacity:1;stroke:black;fill:none;','');
ecrire("0",35,$y0,"text-anchor:end;fill:".$col4);
if ($mini>=0) {$echelle=round(log10($maxi));} else {$echelle=round(log10($maxi-$mini));};
$pas=pow(10,$echelle-1);
$base=$y0-round($pas*$unite_y);$valeur=$pas;
while ($base>=40)
{$chemin="M40 ".$base." l-5 0";faire_path($chemin,'opacity:1;stroke:black;fill:none;','');
$chemin="M40 ".$base." l".($large-60)." 0";faire_path($chemin,'opacity:0.3;stroke:black;fill:none;','');
ecrire($valeur,35,$base,"text-anchor:end;fill:".$col4.";");
$base=$base-round($pas*$unite_y);$valeur=$valeur+$pas;};
if ($mini<0)
{$base=$y0+round($pas*$unite_y);$valeur=-$pas;
while ($base<=($haut-20))
{$chemin="M40 ".$base." l-5 0";faire_path($chemin,'opacity:1;stroke:black;fill:none;','');
$chemin="M40 ".$base." l".($large-60)." 0";faire_path($chemin,'opacity:0.3;stroke:black;fill:none;','');
ecrire($valeur,35,$base,"text-anchor:end;fill:".$col4.";");
$base=$base+round($pas*$unite_y);$valeur=$valeur-$pas;}};
}}

function faire_noms()
{global $large,$haut,$nbdonnees,$donnees_s,$mini,$maxi,$nbnoms,$noms_s,$type,$col4;
switch($type)
{CASE "barre3d":
$type2="barre";
break;
CASE "pie3d":
$type2="pie";
if ($haut<$large) {$rayon_x=round($haut/3);} else {$rayon_x=round($large/3);};
$rayon_y=round($rayon_x/2);
break;
default:
$type2=$type;
if ($haut<$large) {$rayon_x=round($haut/3);} else {$rayon_x=round($large/3);};
$rayon_y=$rayon_x;}
switch($type2)
{CASE "pie":
$total=0;
for ($i=1;$i<=$nbdonnees;$i++) {$total=$total+abs($donnees_s[$i]);};$angle=0;
$xc=round($large/2);$yc=round($haut/2);
for ($i=1;$i<=$nbdonnees;$i++)
{$angle1=M_PI*abs($donnees_s[$i])/$total;$angle3=$angle+$angle1;$angle=$angle+2*$angle1;
$xd=round($xc+$rayon_x*cos($angle3));
$yd=round($yc-$rayon_y*sin($angle3));
ecrire($noms_s[$i],$xd,$yd,"text-anchor:middle;fill:".$col4.";");};
break;
CASE "barre":
$unite_y=round(($haut-60)/$nbdonnees);
for ($i=1;$i<=$nbdonnees;$i++)
{if ($i<=$nbnoms) 
{$xd=$large-20;$yd=round($haut-20-$unite_y*($i-2/3));
ecrire($noms_s[$i],$xd,$yd,"text-anchor:end;fill:".$col4.";");}}
break;
default:
$unite_x=round(($large-60)/$nbdonnees);
if ($mini>=0) {$y0=$haut-20;$unite_y=($haut-60-$unite_x)/$maxi;} else
{$unite_y=($haut-60-$unite_x)/($maxi-$mini);$y0=round($haut-20+$unite_y*$mini);};
for ($i=1;$i<=$nbdonnees;$i++)
{if ($i<=$nbnoms) 
{$xd=40+($i-1)*$unite_x;$yd=round($y0-$unite_y*$donnees_s[$i]-$unite_x/2);
ecrire($noms_s[$i],round($xd+0.5*$unite_x),$yd,"text-anchor:middle;fill:".$col4.";");}}
}}

function faire_histo()
{global $large,$haut,$donnees_s,$nbdonnees,$mini,$maxi,$col2,$col3;
$unite_x=round(($large-60)/$nbdonnees);
if ($mini>=0) {$y0=$haut-20;$unite_y=($haut-60-$unite_x)/$maxi;} else
{$unite_y=($haut-60-$unite_x)/($maxi-$mini);$y0=round($haut-20+$unite_y*$mini);};
for ($i=1;$i<=$nbdonnees;$i++)
{$xd=40+($i-1)*$unite_x;
if ($donnees_s[$i]>=0)
{$yd=round($y0-$unite_y*$donnees_s[$i]);}
else
{$yd=$y0;};
faire_rect($xd,$yd,$unite_x,round(abs($unite_y*$donnees_s[$i])),"stroke:".$col3.";fill:".$col2.";","data".$i);
}}

function faire_barre()
{global $large,$haut,$donnees_s,$nbdonnees,$mini,$maxi,$col2,$col3;
$unite_y=round(($haut-60)/$nbdonnees);
if ($mini>=0) {$x0=40;$unite_x=($large-60-$unite_y)/$maxi;} else
{$unite_x=($large-60-$unite_y)/($maxi-$mini);$x0=round(40-$unite_x*$mini);};
for ($i=1;$i<=$nbdonnees;$i++)
{$yd=round($haut-20-$unite_y*$i);
if ($donnees_s[$i]>=0) {$xd=$x0;} else {$xd=$x0+$unite_x*$donnees_s[$i];};
faire_rect($xd,$yd,round(abs($unite_x*$donnees_s[$i])),$unite_y,"stroke:".$col3.";fill:".$col2.";","data".$i);}}

function faire_barre3d()
{global $large,$haut,$donnees_s,$nbdonnees,$mini,$maxi,$col2,$col3;
$unite_y=round(($haut-60)/(3*$nbdonnees));
if ($mini>=0) {$x0=40;$unite_x=($large-60-3*$unite_y)/$maxi;} else
{$unite_x=($large-60-3*$unite_y)/($maxi-$mini);$x0=round(40-$unite_x*$mini);};
for ($i=1;$i<=$nbdonnees;$i++)
{$yd=round($haut-20-$unite_y*(3*($i-1)+2));
if ($donnees_s[$i]>=0) {$xd=$x0;} else {$xd=$x0+$unite_x*$donnees_s[$i];};
faire_rect($xd,$yd,round($unite_x*abs($donnees_s[$i])),round($unite_y*2),"opacity:0.7;stroke:".$col3.";fill:".$col2.";","data".$i);
$chemin="M".($xd+round($unite_x*abs($donnees_s[$i])))." ".$yd." l 0 ".($unite_y*2)." ".$unite_y." -".$unite_y." 0 -".($unite_y*2)."z";
faire_path($chemin,"opacity:1;stroke:".$col3.";fill:".$col2.";","dat2".$i);
$chemin="M".$xd." ".$yd." l".$unite_y." -".$unite_y." ".round($unite_x*abs($donnees_s[$i]))." 0 -".$unite_y." ".$unite_y."z";
faire_path($chemin,"opacity:0.5;stroke:".$col3.";fill:".$col2.";","dat3".$i);
}}

function faire_nuage()
{global $large,$haut,$donnees_s,$nbdonnees,$mini,$maxi,$col2,$col3;
$unite_x=round(($large-60)/$nbdonnees);
if ($mini>=0) {$y0=$haut-20;$unite_y=($haut-60-$unite_x)/$maxi;} else
{$unite_y=($haut-60-$unite_x)/($maxi-$mini);$y0=round($haut-20+$unite_y*$mini);};
for ($i=1;$i<=$nbdonnees;$i++)
{$xd=round(40+($i-0.5)*$unite_x);$yd=round($y0-$unite_y*$donnees_s[$i]);
faire_circle($xd,$yd,5,"stroke:".$col3.";fill:".$col2.";","data".$i);}}

function faire_pie()
{global $large,$haut,$donnees_s,$nbdonnees,$mini,$maxi,$col2,$col3;
$xc=round($large/2);$yc=round($haut/2);
if ($haut<$large) {$rayon_x=round($haut/3);} else {$rayon_x=round($large/3);};
$total=0;$rayon_y=$rayon_x;$ecart=round($rayon_x/5);
for ($i=1;$i<=$nbdonnees;$i++) {$total=$total+abs($donnees_s[$i]);};
$angle=0;
for ($i=1;$i<=$nbdonnees;$i++)
{$angle1=2*M_PI*abs($donnees_s[$i])/$total;
$xc1=$xc+$ecart*cos($angle+$angle1/2);$yc1=$yc-$ecart*sin($angle+$angle1/2);
$xA=round($xc1+$rayon_x*cos($angle));$yA=round($yc1-$rayon_y*sin($angle));
$angle=$angle+$angle1;
$xB=round($xc1+$rayon_x*cos($angle));$yB=round($yc1-$rayon_y*sin($angle));
if ($angle1<M_PI) 
{$chaine="M".$xA." ".$yA." A ".$rayon_x." ".$rayon_y." 0 0 0 ".$xB." ".$yB." L".$xc1.",".$yc1." z";}
else
{$chaine="M".$xA." ".$yA." A ".$rayon_x." ".$rayon_y." 0 1 0 ".$xB." ".$yB." L".$xc1.",".$yc1." z";}
faire_path($chaine,"stroke:".$col3.";fill:".$col2.";","data".$i);
$xA=$xc+($rayon_x+$ecart)*cos($angle);
$yA=$yc-($rayon_y+$ecart)*sin($angle);}}

function faire_pie3d()
{global $large,$haut,$donnees_s,$nbdonnees,$mini,$maxi,$col2,$col3;
$xc=round($large/2);$yc=round($haut/2);
if ($haut<$large) {$rayon_x=round($haut/3);} else {$rayon_x=round($large/3);};
$total=0;$rayon_y=round($rayon_x/2);$ecart=round($rayon_x/5);
for ($i=1;$i<=$nbdonnees;$i++) {$total=$total+abs($donnees_s[$i]);};
$angle=0;$bord=round($rayon_y/7);
for ($i=1;$i<=$nbdonnees;$i++)
{$angle1=2*M_PI*abs($donnees_s[$i])/$total;
$xc1=$xc+$ecart*cos($angle+$angle1/2);$yc1=$yc-$ecart*sin($angle+$angle1/2);
$xA=round($xc1+$rayon_x*cos($angle));$yA=round($yc1-$rayon_y*sin($angle));
$angle=$angle+$angle1;
$xB=round($xc1+$rayon_x*cos($angle));$yB=round($yc1-$rayon_y*sin($angle));
if ($angle1<M_PI) 
{$chaine[1]="M".$xA." ".$yA." l0 ".$bord." A ".$rayon_x." ".$rayon_y." 0 0 0 ".$xB." ".($yB+$bord)." l0 -".$bord." A ".$rayon_x." ".$rayon_y." 0 0 1 ".$xA." ".$yA." z";}
else
{$chaine[1]="M".$xA." ".$yA." l0 ".$bord." A ".$rayon_x." ".$rayon_y." 0 1 0 ".$xB." ".($yB+$bord)." l0 -".$bord." A ".$rayon_x." ".$rayon_y." 0 1 1 ".$xA." ".$yA." z";}
$chaine[2]="M".$xc1." ".$yc1." l0 ".$bord." L".$xB." ".($yB+$bord)." L".$xB." ".$yB." z";
$chaine[3]="M".$xc1." ".$yc1." l0 ".$bord." L".$xA." ".($yA+$bord)." L".$xA." ".$yA." z";
if ($angle<M_PI/2)
{faire_path($chaine[1],"opacity:0.8;stroke:".$col3.";fill:".$col2.";","dat1".$i);
faire_path($chaine[2],"opacity:0.8;stroke:".$col3.";fill:".$col2.";","dat2".$i);
faire_path($chaine[3],"opacity:0.8;stroke:".$col3.";fill:".$col2.";","dat3".$i);}
else
{if ($angle<M_PI)
{faire_path($chaine[1],"opacity:0.8;stroke:".$col3.";fill:".$col2.";","dat1".$i);
faire_path($chaine[3],"opacity:0.8;stroke:".$col3.";fill:".$col2.";","dat3".$i);
faire_path($chaine[2],"opacity:0.8;stroke:".$col3.";fill:".$col2.";","dat2".$i);}
else
{if ($angle<3*M_PI/2)
{faire_path($chaine[3],"opacity:0.8;stroke:".$col3.";fill:".$col2.";","dat3".$i);
faire_path($chaine[2],"opacity:0.8;stroke:".$col3.";fill:".$col2.";","dat2".$i);
faire_path($chaine[1],"opacity:0.8;stroke:".$col3.";fill:".$col2.";","dat1".$i);}
else
{faire_path($chaine[2],"opacity:0.8;stroke:".$col3.";fill:".$col2.";","dat2".$i);
faire_path($chaine[3],"opacity:0.8;stroke:".$col3.";fill:".$col2.";","dat3".$i);
faire_path($chaine[1],"opacity:0.8;stroke:".$col3.";fill:".$col2.";","dat1".$i);}
}};
$xA=$xc+($rayon_x+$ecart)*cos($angle);
$yA=$yc-($rayon_y+$ecart)*sin($angle);}
$angle=0;
for ($i=1;$i<=$nbdonnees;$i++)
{$angle1=2*M_PI*abs($donnees_s[$i])/$total;
$xc1=$xc+$ecart*cos($angle+$angle1/2);$yc1=$yc-$ecart*sin($angle+$angle1/2);
$xA=round($xc1+$rayon_x*cos($angle));$yA=round($yc1-$rayon_y*sin($angle));
$angle=$angle+$angle1;
$xB=round($xc1+$rayon_x*cos($angle));$yB=round($yc1-$rayon_y*sin($angle));
if ($angle1<M_PI) 
{$chaine="M".$xA." ".$yA." A ".$rayon_x." ".$rayon_y." 0 0 0 ".$xB." ".$yB." L".$xc1.",".$yc1." z";}
else
{$chaine="M".$xA." ".$yA." A ".$rayon_x." ".$rayon_y." 0 1 0 ".$xB." ".$yB." L".$xc1.",".$yc1." z";}
faire_path($chaine,"stroke:".$col3.";fill:".$col2.";","data".$i);
$xA=$xc+($rayon_x+$ecart)*cos($angle);
$yA=$yc-($rayon_y+$ecart)*sin($angle);}}

function faire_courbe()
{global $large,$haut,$donnees_s,$nbdonnees,$mini,$maxi,$col2,$col3;
$unite_x=round(($large-60)/$nbdonnees);
if ($mini>=0) {$y0=$haut-20;$unite_y=($haut-60-$unite_x)/$maxi;} else
{$unite_y=($haut-60-$unite_x)/($maxi-$mini);$y0=round($haut-20+$unite_y*$mini);};
for ($i=1;$i<=$nbdonnees;$i++)
{$xd=round(40+($i-0.5)*$unite_x);$yd=round($y0-$unite_y*$donnees_s[$i]);
faire_circle($xd,$yd,5,"stroke:".$col3.";fill:".$col2,"data".$i);
if ($i==1) {$chemin="M".$xd." ".$yd;} else {$chemin=$chemin." L".$xd." ".$yd;}};
faire_path($chemin,"opacity:1;stroke-width:3;stroke:".$col3.";fill:none;","data0");}

function faire_histo3d()
{global $large,$haut,$donnees_s,$nbdonnees,$mini,$maxi,$col2,$col3;
$unite_x=round(($large-60)/(3*$nbdonnees));
if ($mini>=0) {$y0=$haut-20;$unite_y=($haut-60-3*$unite_x)/$maxi;} else
{$unite_y=($haut-60-3*$unite_x)/($maxi-$mini);$y0=round($haut-20+$unite_y*$mini);};
for ($i=1;$i<=$nbdonnees;$i++)
{$xd=40+($i-1)*3*$unite_x;
if ($donnees_s[$i]>=0)
{$yd=round($y0-$unite_y*$donnees_s[$i]);}
else
{$yd=$y0;};
faire_rect($xd,$yd,$unite_x*2,round(abs($unite_y*$donnees_s[$i])),"opacity:0.7;stroke:".$col3.";fill:".$col2.";","data".$i);
$chemin="M".$xd." ".$yd." l".($unite_x*2)." 0 ".$unite_x." -".$unite_x." -".($unite_x*2)." 0z";
faire_path($chemin,"opacity:1;stroke:".$col3.";fill:".$col2.";","dat2".$i);
$chemin="M".($xd+2*$unite_x)." ".$yd." l".$unite_x." -".$unite_x." 0 ".round(abs($unite_y*$donnees_s[$i]))." -".$unite_x." ".$unite_x."z";
faire_path($chemin,"opacity:0.5;stroke:".$col3.";fill:".$col2.";","dat3".$i);
}}

function faire_svg()
{global $large,$haut,$nbnoms,$type,$titre,$col1;
print("<?xml version='1.0' encoding='iso-8859-1'?>\n");
print("<svg version='1.1' baseProfile='full' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' id='root' xml:space='preserve' width='".$large."' height='".$haut."'>\n"); 
print("<rect x='0' y='0' width='".$large."' height='".$haut."' style='fill:".$col1.";' pointer-events='none'/>\n"); 
if ($titre!="") {faire_titre();};
faire_gradu();
print("<g onmouseover='top.show_tooltip(evt)' onmouseout='top.hide_tooltip(evt)'>\n"); 
switch($type)
{CASE "histo":
faire_histo();
break;
CASE "histo3d":
faire_histo3d();
break;
CASE "barre":
faire_barre();
break;
CASE "barre3d":
faire_barre3d();
break;
CASE "pie":
faire_pie();
break;
CASE "pie3d":
faire_pie3d();
break;
CASE "courbe":
faire_courbe();
break;
CASE "nuage":
faire_nuage();
break;};
print("</g>\n");
if ($nbnoms>0) {faire_noms();};
print("<g id='tooltip' visibility='hidden' transform='translate(0 0)' pointer-events='none'>\n");
print("<rect x='0' y='0' width='100' height='20' stroke='black' fill='yellow' rx='5'/>\n");
print("<text id='tooltip_text' x='50' y='15' text-anchor='middle' fill='black'> </text>\n");
print("</g>\n");
print("</svg>");}

if ($donnees!="")
{analyse_donnees();minmax();
if ($noms!="") {analyse_noms();} else {$nbnoms=0;};
faire_svg();}
else
{print("<?xml version='1.0' encoding='iso-8859-1'?>\n");
print("<svg version='1.1' baseProfile='full' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' id='root' xml:space='preserve' width='".$large."' height='".$haut."'>\n"); 
print("<rect x='0' y='0' width='".$large."' height='".$haut."' style='fill:".$col1.";'/>\n");
print("<text x='10' y='50'>Il faut entrer des données!</text>\n");
print("</svg>");}

?>
