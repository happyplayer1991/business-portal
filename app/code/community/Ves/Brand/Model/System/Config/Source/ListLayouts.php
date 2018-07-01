<?php

class Ves_Brand_Model_System_Config_Source_ListLayouts
{
 public function toOptionArray()
    {
        return array(
        	array('value' => "list", 'label'=>Mage::helper('adminhtml')->__('List')),
            array('value' => "grid", 'label'=>Mage::helper('adminhtml')->__('Grid'))
        );
    }
}
