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
class Ves_Themesettings_Block_Adminhtml_Install_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preparing form
	 *
	 * @return Mage_Adminhtml_Block_Widget_Form
	 */
	protected function _prepareForm()
	{
		$ves_theme = Mage::helper('themesettings/theme')->getVenusTheme();

		$themes_item = '';
		foreach ($ves_theme as $key => $val) {
			$themes_item[] = '"'.$key.'"';
		}

		$thems = 'var themes = ['.implode(',', $themes_item).'];';
		$form = new Varien_Data_Form(
			array(
				'id'		=> 'edit_form',
				'method'	=> 'post',
				'enctype'	=> 'multipart/form-data'
				)
			);

		$fieldset = $form->addFieldset('display', array(
			'legend'	=> Mage::helper('themesettings')->__('Import settings'),
			'class'		=> 'fieldset-wide',
			));

		$fieldStores = $fieldset->addField('theme_folder', 'select', array(
			'name'		=> 'theme_folder',
			'label'		=> Mage::helper('themesettings')->__('Theme Folder'),
			'title'		=> Mage::helper('themesettings')->__('Theme Folder'),
			'values'	=> Mage::getModel('themesettings/system_config_source_install_themes')->toOptionArray(),
			'onchange'	=> 'onchangeTheme()',
			'note'		=> 'Get list all file .json in skin/frontend/<b>Folder_Theme</b>/import',
			'after_element_html' => '<script type="text/javascript">
			'.$thems.'
			function onchangeTheme(){
				for	(i = 0; i < themes.length; i++) {
					$(themes[i]).parentNode.parentNode.hide();
				}
				var folder_name = $(\'theme_folder\').value;
				$(folder_name).parentNode.parentNode.show();
			}
			Event.observe(window,\'load\',onchangeTheme);
		</script>'
		));

		foreach ($ves_theme as $key => $val) {
			$fieldPreset = $fieldset->addField($key, 'select', array(
				'name'		=> $key,
				'label'		=> Mage::helper('themesettings')->__('Select Configuration to Import'),
				'title'		=> Mage::helper('themesettings')->__('Select Configuration to Import'),
				'values'	=> Mage::getModel('themesettings/system_config_source_install_importFiles')
				->toOptionArray($key),
				'onchange' => "onchangeFile('".trim($key)."')",
				));
		}

		$fieldDataImportFile = $fieldset->addField('data_import_file', 'file', array(
			'name'		=> 'data_import_file',
			'label'		=> Mage::helper('themesettings')->__('Select File With Saved Configuration to Import'),
			'title'		=> Mage::helper('themesettings')->__('Select File With Saved Configuration to Import'),
			'after_element_html' => '
			<script type="text/javascript">
				$(\'data_import_file\').parentNode.parentNode.hide();
				function onchangeFile(element){
					var data_import_file = $(element).value;
					if(data_import_file == "data_import_file"){
						$(\'data_import_file\').parentNode.parentNode.show();
					}else{
						$(\'data_import_file\').parentNode.parentNode.hide();
					}
				}
				Event.observe(window,\'load\',onchangeFile);
			</script>
			'
			));

		$fieldPreset = $fieldset->addField('overwrite_blocks', 'select', array(
			'name'		=> 'overwrite_blocks',
			'label'		=> Mage::helper('themesettings')->__('Overwrite Existing Blocks'),
			'title'		=> Mage::helper('themesettings')->__('Overwrite Existing Blocks'),
			'values'	=> Mage::getModel('adminhtml/system_config_source_yesno')
			->toOptionArray(),
			'note'		=> Mage::helper('themesettings')->__("<span>- If set to <b>Yes</b>, the import data will override exist data. Check exits data according to the field <b>URL Key</b> of <b>Cms Pages</b> and the field <b>Identifier</b> of <b>Static Block</b>.<br/>- If set to <b>No</b>, the function import will empty data of all table of <b>CMS Page</b> and <b>Static Block</b>, then insert import data.</span>
				")
			));

		$fieldStores = $fieldset->addField('store_id', 'select', array(
			'name'		=> 'stores',
			'label'		=> Mage::helper('cms')->__('Configuration Scope'),
			'title'		=> Mage::helper('cms')->__('Configuration Scope'),
			'title'		=> Mage::helper('themesettings')->__('Configuration Scope'),
			'note'		=> Mage::helper('themesettings')->__("Imported configuration settings will be applied to selected scope (selected store view or website). If you're not sure what is 'scope' in Magento system configuration, it is highly recommended to leave the default scope <strong>'Default Config'</strong>. In this case imported configuration will be applied to all existing store views."),
			'required'	=> true,
			'values'	=> Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			));
		$renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
		$fieldStores->setRenderer($renderer);

		$renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
		$fieldStores->setRenderer($renderer);
		$fieldset->addField('action_type', 'hidden', array(
			'name'  => 'action_type',
			'value' => 'import',
			));

		$actionUrl = $this->getUrl('*/*/import');
		$form->setAction($actionUrl);
		$form->setUseContainer(true);

		$this->setForm($form);
		return parent::_prepareForm();
	}
}