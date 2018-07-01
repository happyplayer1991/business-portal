<?php

class Ves_Themesettings_Model_System_Config_Source_Header_Usermenu_Position
{
    public function toOptionArray()
    {
		return array(
			array('value' => '1',	'label' => Mage::helper('themesettings')->__('Before Cart Drop-Down Block')),
			array('value' => '2',	'label' => Mage::helper('themesettings')->__('Before Compare Block')),
			array('value' => '3',	'label' => Mage::helper('themesettings')->__('Before Top Links')),
			array('value' => '4',	'label' => Mage::helper('themesettings')->__('After Top Links')),
        );
    }
}