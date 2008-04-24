<?php
function make_svg_head ($title, $abstract) {
  //set the content type
  header("Content-type: image/svg+xml");
  //mark this as XML
  print('<?xml version="1.0" encoding="iso-8859-1"?>' . "\n");
  //Point to SVG DTD
  print('<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/2001/PR-SVG-20010719/DTD/svg10.dtd">' . "\n");
  //start SVG document
  echo "<svg xmlns=\"http://www.w3.org/2000/svg\">\n";
  echo "<title>" . $title . "</title>" . "\n";
  echo "<desc>" . $abstract . "</desc>" . "\n";
}
$Nbr=$_POST["Nbr"];
function CordCercles($r,$Nbr,$cx,$cy){
	$a=360/($Nbr+1);
	for($i=0;$i<$Nbr;$i++){
		$angle=$a*($i+1);
		if($angle<=90){
			
			$cord[$i][1]=$cx-(cos(deg2rad(90-$angle))*$r);
		    $cord[$i][2]=cos(deg2rad($angle))*$r+$cy;
		    //echo $cord[$i][1]."<90>".$cord[$i][2]."</br>";
		
		}elseif
			
		    (($angle >90)&&($angle <=180)){
			$cord[$i][1]=cos(deg2rad(90-$angle))*$r+$cx;
			 $cord[$i][2]=$cy+cos(deg2rad($angle))*$r;
		     //echo$cord[$i][1]."<90-180>".$cord[$i][2]."</br>";
		
		}elseif(($angle>180)&&($angle<=270)){
			
			$cord[$i][1]=cos(deg2rad(270-$angle))*$r+$cx;
			$cord[$i][2]=$cy-(cos(deg2rad($angle))*$r);
			//echo$cord[$i][1]."<180-270>".$cord[$i][2]."</br>";
		
		}elseif($angle>270){
			
			$cord[$i][1]=$cx-(cos(deg2rad($angle-270))*$r);
	        $cord[$i][2]=$cy-(cos(deg2rad($angle))*$r);
	        //echo$cord[$i][1]."<270>".$cord[$i][2]."</br>";
		}
	  
	   
	}
	return $cord; 
}
make_svg_head ($title, $abstract);
    Cercle(600,250,200,"red");
	$Cord=CordCercles(200,$Nbr,600,250);
	for($i=0;$i<$Nbr;$i++){
		Cercle($Cord[$i][1],$Cord[$i][2],10,"green");
		Text($Cord[$i][1],$Cord[$i][2],$i);
	}
   
echo'</svg>';

function Cercle($cx,$cy,$r,$color){
	echo "<circle cx='".$cx."' cy='".$cy."' r='".$r."' fill='".$color."' />";
	
}
Function Text($x,$y,$text){
	echo '<text x="'.$x.'" y="'.$y.'" font-family="Verdana" font-size="15" fill="yellow" >'.$text.'</text>';
}
?>