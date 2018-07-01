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
class Ves_Themesettings_Block_Adminhtml_Install_Export_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preparing form
	 *
	 * @return Mage_Adminhtml_Block_Widget_Form
	 */
	protected function _prepareForm()
	{

		$form = new Varien_Data_Form(
			array(
				'id'		=> 'edit_form',
				'method'	=> 'post',
				)
			);

		$fieldset = $form->addFieldset('display', array(
			'legend'	=> Mage::helper('themesettings')->__('Export settings'),
			'class'		=> 'fieldset-wide'
			));

		$fieldset->addField('file_name', 'text', array(
			'name'		=> 'file_name',
			'label'		=> Mage::helper('themesettings')->__('File Name'),
			'title'		=> Mage::helper('themesettings')->__('File Name'),
			'note'		=> Mage::helper('themesettings')->__('This will be the name of the file in which configuration will be saved. You can enter any name you want.'),
			'required'	=> true,
			));

		$fieldPreset = $fieldset->addField('isdowload', 'select', array(
			'name'		=> 'isdowload',
			'label'		=> Mage::helper('themesettings')->__('Download File'),
			'title'		=> Mage::helper('themesettings')->__('Download File'),
			'values'	=> Mage::getModel('adminhtml/system_config_source_yesno')
			->toOptionArray(),
			));

		$fieldStores = $fieldset->addField('folder', 'select', array(
			'name'		=> 'folder',
			'label'		=> Mage::helper('themesettings')->__('Folder'),
			'title'		=> Mage::helper('themesettings')->__('Folder'),
			'values'	=> Mage::getModel('themesettings/system_config_source_install_themes')->toOptionArray()
			));

		$fieldset->addField('modules', 'multiselect', array(
			'name'		=> 'modules',
			'label'		=> Mage::helper('themesettings')->__('Select Modules'),
			'title'		=> Mage::helper('themesettings')->__('Select Modules'),
			'values'	=> Mage::getModel('themesettings/system_config_source_install_packageModules')->toOptionArray()
			));
		
		$fieldStores = $fieldset->addField('store_id', 'select', array(
			'name'		=> 'stores',
			'label'		=> Mage::helper('themesettings')->__('Configuration Scope'),
			'title'		=> Mage::helper('themesettings')->__('Configuration Scope'),
			'note'		=> Mage::helper('themesettings')->__('Configuration of selected store will be saved in a file. Apply for all system config of modules'),
			'values'	=> Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			));

		$renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
		$fieldStores->setRenderer($renderer);

		$fieldset->addField('cmspages', 'multiselect', array(
			'name'		=> 'cmspages',
			'label'		=> Mage::helper('themesettings')->__('Select CMS Pages'),
			'title'		=> Mage::helper('themesettings')->__('Select CMS Pages'),
			'values'	=> Mage::getModel('themesettings/system_config_source_install_cmsPages')
			->toOptionArray($this->getRequest()->getParam('package'))
			));

		$fieldset->addField('staticblocks', 'multiselect', array(
			'name'		=> 'staticblocks',
			'label'		=> Mage::helper('themesettings')->__('Select Static Blocks to Export'),
			'title'		=> Mage::helper('themesettings')->__('Select Static Blocks to Export'),
			'values'	=> Mage::getModel('themesettings/system_config_source_install_staticBlocks')
			->toOptionArray($this->getRequest()->getParam('package'))
			));

		$fieldset->addField('widgets', 'multiselect', array(
			'name'		=> 'widgets',
			'label'		=> Mage::helper('themesettings')->__('Select Widgets'),
			'title'		=> Mage::helper('themesettings')->__('Select Widgets'),
			'values'	=> Mage::getModel('themesettings/system_config_source_install_widgets')
			->toOptionArray($this->getRequest()->getParam('package'))
			));

		$fieldset->addField('action_type', 'hidden', array(
			'name'  => 'action_type',
			'value' => 'export',
			));

		//Set action and other parameters
		$actionUrl = $this->getUrl('*/*/export');
		$form->setAction($actionUrl);
		$form->setUseContainer(true);

		$this->setForm($form);
		return parent::_prepareForm();
	}
}