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
class Ves_Themesettings_Model_Observer
{
	/**
	 * After any system config is saved
	 */
	public function hookTo_controllerActionPostdispatchAdminhtmlSystemConfigSave()
	{
		$section = Mage::app()->getRequest()->getParam('section');
		if ($section == 'themesettings_layout' || $section == 'themesettings_design' || $section == 'themesettings'){
			$websiteCode = Mage::app()->getRequest()->getParam('website');
			$storeCode = Mage::app()->getRequest()->getParam('store');
			$generator = Mage::getSingleton('themesettings/cssgen_generator');
			$generator->generateCss($websiteCode,$storeCode);
		}
	}

	/**
	 * After store view is saved
	 */
	public function hookTo_storeEdit(Varien_Event_Observer $observer)
	{
		$store = $observer->getEvent()->getStore();
		if ($store->getIsActive())
		{
			$this->_onStoreChange($store);
		}
	}

	/**
	 * After store view is added
	 */
	public function hookTo_storeAdd(Varien_Event_Observer $observer)
	{
		$store = $observer->getEvent()->getStore();
		if ($store->getIsActive())
		{
			$this->_onStoreChange($store);
		}
	}

	/**
	 * On store view changed
	 */
	protected function _onStoreChange($store)
	{
		$storeCode = $store->getCode();
		$websiteCode = $store->getWebsite()->getCode();
		$generator = Mage::getSingleton('themesettings/cssgen_generator');
		$generator->generateCss($websiteCode, $storeCode);
	}

	/**
	 * After config import
	 */
	public function hookTo_DataporterCfgporterImportAfter(Varien_Event_Observer $observer)
	{
		$event = $observer->getEvent();
		$websiteCode 	= '';
		$storeCode 		= '';
		$scope = $event->getData('portScope');
		$scopeId = $event->getData('portScopeId');
		switch ($scope) {
			case 'websites':
			$websiteCode 	= Mage::app()->getWebsite($scopeId)->getCode();
			break;
			case 'stores':
			$storeCode 		= Mage::app()->getStore($scopeId)->getCode();
			$websiteCode 	= Mage::app()->getStore($scopeId)->getWebsite()->getCode();
			break;
		}

		Mage::app()->getConfig()->reinit();
		$cg = Mage::getSingleton('themesettings/cssgen_generator');
		$cg->generateCss('grid',   $websiteCode, $storeCode);
		$cg->generateCss('layout', $websiteCode, $storeCode);
		$cg->generateCss('design', $websiteCode, $storeCode);
	}
}
