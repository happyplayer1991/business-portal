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
class Ves_Themesettings_Block_Adminhtml_System_Config_Form_Field_Text extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	/**
	 * @deprecated
	 *
     * Add texture preview
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$elementOriginalData = $element->getOriginalData();
		$texPath = '';
		if (isset($elementOriginalData['tex_path']))
		{
			$texPath = $elementOriginalData['tex_path'];
		}
		else
		{
			return 'Error: Texture path not specified in config.';
		}


		$html = $element->getElementHtml(); //Default HTML
		$jsUrl = $this->getJsUrl('ves/jquery/jquery-1.7.2.min.js');
		//$texUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . Mage::helper('themesettings')->getTexPath();
		$texUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $texPath;

		//Recreate ID of the background color picker which is related with this pattern
			//From the texture picker ID get the suffix beginning with '_tex'
		$fieldIdSuffix = strstr($element->getHtmlId(), '_pattern');
			//Replace the suffix with suffix appropriate for the background color picker in the current options group
		$bgcPickerId = str_replace($fieldIdSuffix, '_bg_color', $element->getHtmlId());

		//Create ID of the pattern preview box
		$previewId = $element->getHtmlId() . '-tex-preview';

		if (Mage::registry('jqueryLoaded') == false)
		{
			$html .= '
			<script type="text/javascript" src="'. $jsUrl .'"></script>
			<script type="text/javascript">jQuery.noConflict();</script>
			';
			Mage::register('jqueryLoaded', 1);
		}

		$html .= '
		<br/><div id="'. $previewId .'" style="width:280px; height:150px; margin:10px 0; background-color:transparent;"></div>
		<script type="text/javascript">
			jQuery(function($){
				var tex		= $("#'. $element->getHtmlId()	.'");
				var bgc		= $("#'. $bgcPickerId			.'");
				var preview	= $("#'. $previewId				.'");
	

				preview.css("background-color", bgc.attr("value"));
				bgc.change(function(){
					preview.css({
						"background-color": bgc.css("background-color"),
						"background-image": "url('. $texUrl .'" + tex.val() + ".png)"
					});
				});
				tex.change(function() {
				if(tex.val()!=0){
					var bgColor = bgc.css("background-color");
					preview.css({
						"background-color": bgColor,
						"background-image": "url('. $texUrl .'" + tex.val() + ".png)"
					});
				}else{
					preview.css({
						"background": "none"
					});
				}
				}).change();
			});
		</script>';

return $html;
}
}
