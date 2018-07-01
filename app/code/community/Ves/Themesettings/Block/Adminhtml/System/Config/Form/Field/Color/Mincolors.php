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
class Ves_Themesetting_Block_Adminhtml_System_Config_Form_Field_Color_Minicolors extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Add color picker
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $element->getElementHtml(); //Default HTML

		if(Mage::registry('colorPickerFirstUse') == false)
		{
			$html .= '
			<script type="text/javascript" src="'. $this->getJsUrl('ves/jquery/jquery-for-admin.min.js') .'"></script>
			<script type="text/javascript" src="'. $this->getJsUrl('ves/jquery/plugins/minicolors/jquery.minicolors.min.js') .'"></script>
			<script type="text/javascript">jQuery.noConflict();</script>
			<link type="text/css" rel="stylesheet" href="'. $this->getJsUrl('ves/jquery/plugins/minicolors/jquery.minicolors.css') .'" />
			';

			Mage::register('colorPickerFirstUse', 1);
		}

		$html .= '
		<script type="text/javascript">
			jQuery(function($){
				$("#'. $element->getHtmlId() .'").miniColors();
			});
</script>
';

return $html;
}
}
