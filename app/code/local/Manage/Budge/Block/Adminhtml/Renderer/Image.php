<?php
class Manage_Budge_Block_Adminhtml_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
    protected function _getValue(Varien_Object $row)
    {      
        /*
        $val = $row->getData($this->getColumn()->getIndex());
        $val = str_replace("no_selection", "", $val);
        if(empty($val)) {
            return "";
        }
        $url = Mage::getBaseUrl('media') . $val;
        $out = "<img src=". $url ." width='100px' />";
        return $out;
        */

        $mediaurl=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $value = $row->getImage();
        return '<img src="'.$mediaurl.DS.$value.'"  style="text-align:center;"/>'; 
    }
}