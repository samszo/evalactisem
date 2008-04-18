<?php
/** 
 *  SvgPolygon.php
 *
 * @since 4.1.1
 */

class SvgPolygon extends SvgElement
{
    var $mPoints;
    
    function SvgPolygon($points=0, $style="", $transform="")
    {
        // Call the parent class constructor.
        $this->SvgElement();
        
        $this->mPoints = $points;
        $this->mStyle = $style;
        $this->mTransform = $transform;
        
    }
    
    function printElement()
    {
        print("<polygon points=\"$this->mPoints\" ");
        
        if (is_array($this->mElements)) { // Print children, start and end tag.
            
            $this->printStyle();
            $this->printTransform();
            print(">\n");
            parent::printElement();
            print("</polygon>\n");
            
        } else { // Print short tag.
            
            $this->printStyle();
            $this->printTransform();
            print("/>\n");
            
        } // end else
        
    }
    
    function setShape($points)
    {
        $this->mPoints = $points;
    }
}
?>
