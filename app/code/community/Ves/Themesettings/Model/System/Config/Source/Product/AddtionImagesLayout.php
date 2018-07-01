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
class Ves_Themesettings_Model_System_Config_Source_Product_AddtionImagesLayout
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'horizontal_bottom', 'label' => Mage::helper('themesettings')->__('Horizontal Bottom')),
			array('value' => 'horizontal_top', 'label' => Mage::helper('themesettings')->__('Horizontal Top')),
			array('value' => 'vertical_left', 'label' => Mage::helper('themesettings')->__('Vertical Left')),
			array('value' => 'vertical_right', 'label' => Mage::helper('themesettings')->__('Vertical Right')),
			);
	}
}