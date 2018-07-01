<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Regular License.
 * You may not use any part of the code in whole or part in any other software
 * or product or website.
 *
 * @author		Infortis
 * @copyright	Copyright (c) 2014 Infortis
 * @license		Regular License http://themeforest.net/licenses/regular 
 */

class Ves_Themesettings_Block_Adminhtml_System_Config_Form_Field_Install
	extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
	/**
	 * Render element html
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		die('aaaaâ');
		$elementData = $element->getOriginalData();

		$url1 = $this->getUrl('themesettings/adminhtml_install/import');
		$url2 = $this->getUrl('themesettings/adminhtml_install/export');

		//Start base HTML
		$html = '';
		$html .= sprintf('<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5"><h4 id="%s">%s</h4></td></tr>',
			$element->getHtmlId(), $element->getHtmlId(), $element->getLabel()
		);

		//Open row
		$html .= sprintf('<tr class="" id="row_%s_content">',
			$element->getHtmlId()
		);

		//Add label cell
		$html .= sprintf('<td class="label"><label>%s</label></td>',
			$elementData['sublabel']
		);

		//Open main cell
		$html .= '<td class="value">';

		//Buttons
		$html .= $this->getLayout()->createBlock('adminhtml/widget_button')
			->setType('button')
			->setClass('go-to-page')
			->setLabel('Import')
			->setOnClick("setLocation('{$url1}')")
			->toHtml();
		$html .= '&nbsp;';
		$html .= $this->getLayout()->createBlock('adminhtml/widget_button')
			->setType('button')
			->setClass('go-to-page')
			->setLabel('Export')
			->setOnClick("setLocation('{$url2}')")
			->toHtml();

		//Close all wrappers: cell and row
		$html .= '</td>';
		$html .= '</tr>';

		return $html;
	}
}
