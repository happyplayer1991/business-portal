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
class Ves_Themesettings_Model_Cssgen_Generator extends Mage_Core_Model_Abstract{

	public function __construct(){
		parent::__construct();
	} 

	public function generateCss($websiteCode, $storeCode){
		if ($websiteCode){
			if ($storeCode) {
				$this->_generateStoreCss($storeCode); 
			} else {
				$this->_generateWebsiteCss($websiteCode); 
			}
		}else{
			$websites = Mage::app()->getWebsites(false, true);
			foreach ($websites as $website) {
				$this->_generateWebsiteCss($website); 
			}
		} 
	}

	protected function _generateWebsiteCss($website) {
		$website = Mage::app()->getWebsite($website);
		foreach ($website->getStoreCodes() as $store){
			$this->_generateStoreCss($store);
		}
	}

	public function generateStoreCss($storeCode){
		$this->_generateStoreCss($storeCode);
	}

	protected function _generateStoreCss($storeCode){
		$store = Mage::app()->getStore($storeCode);
		$store_id = $store->getId();
		$package_name = Mage::getStoreConfig('design/package/name', $store_id);
		$theme = Mage::getStoreConfig('design/theme/defaults', $store_id);
		if($theme==''){
			$theme = 'default';
		}
		if (!$store->getIsActive()) 
			return;

		$cssFile = Mage::getBaseDir('skin') . DS . 'frontend' . DS . $package_name . DS . $theme . DS . 'themesettings' . DS . 'css' . DS . 'themesettings_'. $storeCode . '.css';

		$cssTemplate =  'ves/themesettings/themesettings_styles.phtml';
		Mage::register('ves_store', $store);
		try{ 
			$cssBlockHtml = Mage::app()->getLayout()->createBlock("core/template")->setData('area', 'frontend')->setTemplate($cssTemplate)->toHtml();
			if (empty($cssBlockHtml)) {
				throw new Exception( Mage::helper('themesettings')->__("The system has an issue when create css file") ); 
			}
			$file = new Varien_Io_File(); 
			$file->setAllowCreateFolders(true);
			$file->open(array( 'path' => Mage::getBaseDir('skin') . DS . 'frontend' . DS . $package_name . DS . $theme . DS . 'themesettings' . DS . 'css' ));
			$file->streamOpen($cssFile, 'w+', 0777);
			$file->streamLock(true);
			$file->streamWrite($cssBlockHtml);
			$file->streamUnlock();
			$file->streamClose();
		}catch (Exception $e){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('themesettings')->__('The system has an issue when create css file'). '<br/>Message: ' . $e->getMessage());
			Mage::logException($e);
		}
		Mage::unregister('ves_store'); 
	}
}