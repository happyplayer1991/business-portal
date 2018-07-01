<?php

class Ves_Themesettings_Model_System_Config_Source_Header_Position_PrimaryMenu
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'menuContainer',		'label' => Mage::helper('themesettings')->__('Full Width Menu Container')),
			array('value' => 'primLeftCol',			'label' => Mage::helper('themesettings')->__('Primary, Left Column')),
			array('value' => 'primCentralCol',		'label' => Mage::helper('themesettings')->__('Primary, Central Column')),
			array('value' => 'primRightCol',		'label' => Mage::helper('themesettings')->__('Primary, Right Column')),
        );
    }
}