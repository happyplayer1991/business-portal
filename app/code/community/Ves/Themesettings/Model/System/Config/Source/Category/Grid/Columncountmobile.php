<?php

class Ves_Themesettings_Model_System_Config_Source_Category_Grid_Columncountmobile
{
    public function toOptionArray()
    {
        return array(
			array('value' => 1, 'label' => Mage::helper('themesettings')->__('1')),
			array('value' => 2, 'label' => Mage::helper('themesettings')->__('2')),
			array('value' => 3, 'label' => Mage::helper('themesettings')->__('3')),
        );
    }
}