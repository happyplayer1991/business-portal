<?php 
 /*------------------------------------------------------------------------
  # VenusTheme Brand Module 
  # ------------------------------------------------------------------------
  # author:    VenusTheme.Com
  # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
  # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.venustheme.com
  # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/

class Ves_Brand_Model_System_Config_Source_ListComment
{	
 
    public function toOptionArray()
    {
		

		$output = array();
		$output[] = array("value"=>"" , "label" => Mage::helper('adminhtml')->__("Default Engine"));
		$output[] = array("value"=>"disqus" , "label" => Mage::helper('adminhtml')->__("Disqus"));
		$output[] = array("value"=>"facebook" , "label" => Mage::helper('adminhtml')->__("Facebook"));
		
        return $output ;
    }    
}
