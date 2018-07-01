<?php

class Ves_Themesettings_Model_System_Config_Source_Design_Icon_Color_Bwhover
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'b',		'label' => Mage::helper('themesettings')->__('Black')),
            array('value' => 'w',		'label' => Mage::helper('themesettings')->__('White')),
            array('value' => 'bw',		'label' => Mage::helper('themesettings')->__('Black | White on hover')),
            array('value' => 'wb',		'label' => Mage::helper('themesettings')->__('White | Black on hover')),
        );
    }
}