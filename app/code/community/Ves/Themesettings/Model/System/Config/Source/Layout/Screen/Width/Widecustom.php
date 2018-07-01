<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_Themesettings
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Themesettings Extension
 *
 * @category   Ves
 * @package    Ves_Themesettings
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Themesettings_Model_System_Config_Source_Layout_Screen_Width_WideCustom
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'default',		'label' => Mage::helper('themesettings')->__('Default')),
			array('value' => '960',		'label' => Mage::helper('themesettings')->__('1024 px')),
			array('value' => '1280',	'label' => Mage::helper('themesettings')->__('1280 px')),
			array('value' => '1360',	'label' => Mage::helper('themesettings')->__('1360 px')),
			array('value' => '1440',	'label' => Mage::helper('themesettings')->__('1440 px')),
			array('value' => '1680',	'label' => Mage::helper('themesettings')->__('1680 px')),
			array('value' => 'custom',	'label' => Mage::helper('themesettings')->__('Custom width...'))
			);
	}
}
