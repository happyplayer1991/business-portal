<?php

class Ves_Themesettings_Model_System_Config_Backend_Design_Product_BorderStyle
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'none', 'label' => Mage::helper('themesettings')->__('None')),
			array('value' => 'solid', 'label' => Mage::helper('themesettings')->__('Solid')),
			array('value' => 'dashed', 'label' => Mage::helper('themesettings')->__('Dashed')),
			array('value' => 'dotted', 'label' => Mage::helper('themesettings')->__('Dotted')),
			array('value' => 'double', 'label' => Mage::helper('themesettings')->__('Double')),
			array('value' => 'groove', 'label' => Mage::helper('themesettings')->__('Groove')),
			array('value' => 'inset', 'label' => Mage::helper('themesettings')->__('Inset')),
			array('value' => 'outset', 'label' => Mage::helper('themesettings')->__('Outset')),
			array('value' => 'ridge', 'label' => Mage::helper('themesettings')->__('Ridge')),
			);
	}
}
