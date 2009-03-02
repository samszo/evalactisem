<?php
# Made in April 2005 by Daniel Schneider, TECFA, University of Geneva
# This is freeware, if you mention the URLs below in your code:
#
# URL for this file:
# http://tecfa.unige.ch/guides/svg/ex/objects-in-circles/
#
# The algorithm for drawing the circle comes from foafnaut.svg
# See http://jibbering.com/foaf/
# newX=theBlub.x + Math.round(Math.cos(2*Math.PI/numBlubsToCreate*(i%numBlubsToCreate) + offset) * (numBlubsToCreate*4+50));
# newY=theBlub.y + 1.2*Math.round(Math.sin(2*Math.PI/numBlubsToCreate*(i%numBlubsToCreate) + offset) * (numBlubsToCreate*4+50));
#
# M_PI is the php constant for Pi == 3.142
# A whole circle (360 degrees) == 2 * Pi, A half circle == Pi
# So in radians (degrees) a circle goes from 0 (0 degs.) to 6.283 (360 degs.)
# (If you need to know: rad = deg / 180 * Pi )
# Cosine and sine are used to compute x,y given a radius and the angle (expressed in radians)
#
# Note: Filling the full arc is not perfect (e.g. try a half circle), I am too lazy to fix this.

error_reporting(E_ALL);
function make_html_head ($title, $abstract) {
  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
  echo("<html><head>");
  echo "<META http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n";
  echo("<title>$title</title></head><body>\n"); 
  echo "<h1>" . $title . "</h1>\n";
  echo "<p>" . $abstract . "</p>\n";
  echo "<p> To have a look at the source code, please simply consult <a href=\"./\">the current directory</a> and lock at either *.phps or *.text files with the same name. You also can look at a simpler example: <a href='elements-on-circle-with-php.php'>elements-on-circle-with-php.php <a> or a more complex one that reads an XML RSS 0.91 content <a href='elements-on-arc-with-simple-xml.php'>elements-on-arc-with-simple-xml.php <a> </p>\n";
}

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


function draw_elements ($center_x, $center_y, $n_els, $radius, $display_angle, $start_angle, $coords_on, $spikes_on) {

  	//trace
	echo '<text x="30" y="30" style="font-size:16pt;">'.$center_x.', '.$center_y.', '.$n_els.', '.$radius.', '.$display_angle.', '.$start_angle.', '.$coords_on.', '.$spikes_on.', </text>';
	
  // draw the center as big dot
  print ('<circle cx="' . $center_x . '" cy="' . $center_y . '" r="20" style="fill:red" />');

  for ($i=0; $i<$n_els; $i++) {

    $pos_x = $center_x + round (cos($display_angle / $n_els * ($i % $n_els) + $start_angle) * $radius ); // ($i % $n_els) = roughly $i ;)
    $pos_y = $center_y + round (sin($display_angle / $n_els * ($i % $n_els) + $start_angle) * $radius );
    
    if ($coords_on) {
      // yellow rectangle, origin is upper left corner of rectangle
      echo '<rect x="' . $pos_x . '" y="' . $pos_y . '" width="35" height="24" style="fill:#FFFF00;fill-opacity:0.2;stroke:#000099;"/>' . "\n";
      // add some text to show coordinates
      $line_h = 10;
      $pos_x2 = $pos_x+4;
      $pos_y2 = $pos_y+$line_h;
      $pos_y3 = $pos_y+2*$line_h;
      echo '<text x="' . $pos_x2 . '" y="' . $pos_y2 . '" style="font-size:6pt;">' . 'x=' . $pos_x . ' </text>' . "\n";
      echo '<text x="' . $pos_x2 . '" y="' . $pos_y3 . '" style="font-size:6pt;">' . 'y=' . $pos_y . ' </text>' . "\n";
    }
      
    if ($spikes_on) {
      printf ('<line x1="%s" y1="%s" x2="%s" y2="%s"  style="stroke:black;"/> \n', $center_x, $center_y, $pos_x, $pos_y);
    }

    // this is the precise coordinate
    print ('<circle cx="' . $pos_x . '" cy="' . $pos_y . '" r="3" style="fill:red" />');
    // this is the el number
    echo '<text x="' . $pos_x . '" y="' . $pos_y . '" style="font-size:8pt;">' . $i . ' </text>' . "\n";
  }

}


// ****************************** SVG OUTPUT

if (array_key_exists('Submitted', $_POST)) {

  // get the user parameters (note: only needed if your server has global vars off, not my case therefore not tested)
  $n_els         = $_POST['n_els'];
  $radius_multip = $_POST['radius_multip'];
  $radius        = $n_els * $radius_multip * 2;
  $center_x      = $_POST['center_x'];
  $center_y      = $_POST['center_y'];
  $start_pos     = $_POST['start_pos'];
  $start_angle   = $start_pos / 180 * M_PI; // start angle must be in radians, user gives degrees
  $display_ang   = $_POST['display_ang'];
  $display_angle = $display_ang / 180 * M_PI; // display angle must be in radians, user gives degrees
  if (array_key_exists('show_coords_p', $_POST)) $show_coords_p = TRUE; else $show_coords_p = NULL;
  if (array_key_exists('show_spikes_p', $_POST)) $show_spikes_p = TRUE; else $show_spikes_p = NULL;

  make_svg_head ("Elements along circle arcs - SVG generation with php", "This example shows how to create a simple SVG graphic with an arbitrary amount of elements drawn around a circle arc. Radius will adapt to number of elements.");

  //print ('<rect x="5" y="5" rx="5" ry="5" width="350" height="60" style="fill:#CCCCFF;stroke:#000099"/>' . "\n");
  //print('<text x="15" y="30" style="stroke:#000099;fill:#000099;font-size:10pt;">HELLO dear visitor, you wanted ' . $n_els . ' elements</text>' . "\n"); 
  //print('<text x="15" y="45" style="stroke:#000099;fill:#000099;font-size:8pt;">(click back button to see all you wanted !)</text>' . "\n"); 

  // this will draw the n elements you wanted
  draw_elements ($center_x, $center_y, $n_els, $radius, $display_angle, $start_angle, $show_coords_p, $show_spikes_p);


  // end SVG output
  print('</svg>' . "\n");
}




// ******************** HTML form

else {
  // Make the header

  make_html_head ("Elements along circle arcs - SVG generation with php", "This example shows how to create a simple SVG graphic with an arbitrary amount of elements drawn around a circle arc. Radius will adapt to number of elements. Drawing starts to right of the circle (unless you change the start angle) and continues clockwise. If you use more than 20 elements make the radius multiplier very small or else you have to pan the image to see the elements (Alt-mousedrag in SVG viewer)). If you empty input fields results are undetermined (this simple example code, that you can easily fix)");
  
  echo "<form action= 'elements-on-arc.php' name ='form' method ='post'>\n";
  echo "Number of elements: <input type='text' name='n_els' maxlength='2' size='2' value='3'> (less than 3 and more than 30 is a bad idea!.  <br>\n";
  echo "Center of circle: x = <input type='text' name='center_x' maxlength='3' size='3' value='400'> \n";
  echo "y = <input type='text' name='center_y' maxlength='3' size='3' value='200'> <br> \n";
  echo "radius multiplier = <input  type='text' name='radius_multip' maxlength='1' size='1' value='4'> (multiplier * number of elements * 2 = radius of the circle)<br> \n";
  echo "Display angle = <input type='text' name='display_ang' maxlength='3' size='3' value='180'> (by default a half circle in degrees: 180) <br> \n";
  echo "Start angle = <input type='text' name='start_pos' maxlength='3' size='3' value='180'> (start position of half circle in degrees: 0 to 360) <br> \n";
  echo "Show coordinates ? <input type='checkbox' name='show_coords_p'> <br> \n";
  echo "Show spikes ?      <input type='checkbox' name='show_spikes_p' checked='checked'> <br> \n";
  echo "<input type = 'submit'  name ='Submitted' value ='Submit'\n>";
  echo "</form>\n\n";
  // end html
  echo "<hr><address><a name='Signature' href='http://tecfa.unige.ch/tecfa-people/schneider.html'>D.K.S. - April 2005</a></address>";
  echo("</body></html>"); 
}

?>
