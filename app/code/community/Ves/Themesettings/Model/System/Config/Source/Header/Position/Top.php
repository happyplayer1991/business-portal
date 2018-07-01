<?php

class Ves_Themesettings_Model_System_Config_Source_Header_Position_Top
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'topLeft','label' => Mage::helper('themesettings')->__('Top, Left')),
			array('value' => 'topRight','label' => Mage::helper('themesettings')->__('Top, Right')),
			);
	}
}