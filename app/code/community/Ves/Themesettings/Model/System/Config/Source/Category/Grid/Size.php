<?php

class Ves_Themesettings_Model_System_Config_Source_Category_Grid_Size
{
	public function toOptionArray()
	{
		return array(
			array('value' => '',	'label' => Mage::helper('themesettings')->__('Default')),
			array('value' => 's',	'label' => Mage::helper('themesettings')->__('Size S')),
			array('value' => 'xs',	'label' => Mage::helper('themesettings')->__('Size XS')),
		);
	}
}