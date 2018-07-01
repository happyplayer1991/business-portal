<?php

class Ves_Themesettings_Model_System_Config_Source_Header_Position_PrimaryTop
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'topLeft_1',			'label' => Mage::helper('themesettings')->__('Top, Left')),
			array('value' => 'topRight_1',			'label' => Mage::helper('themesettings')->__('Top, Right')),
			array('value' => 'primLeftCol',			'label' => Mage::helper('themesettings')->__('Primary, Left Column')),
			array('value' => 'primCentralCol',		'label' => Mage::helper('themesettings')->__('Primary, Central Column')),
			array('value' => 'primRightCol',		'label' => Mage::helper('themesettings')->__('Primary, Right Column')),
        );
    }
}
