<?php

class Ves_Themesettings_Model_System_Config_Source_Header_Position_All
{
    public function toOptionArray()
    {
    	/**
    	 * @deprecated
    	 */
		return array(
			array('value' => 'primLeftCol',			'label' => Mage::helper('themesettings')->__('Left Column')),
			array('value' => 'primCentralCol',		'label' => Mage::helper('themesettings')->__('Central Column')),
			array('value' => 'primRightCol',		'label' => Mage::helper('themesettings')->__('Right Column')),
			array('value' => 'userMenu',			'label' => Mage::helper('themesettings')->__('Inside User Menu...')),
        );
    }
}