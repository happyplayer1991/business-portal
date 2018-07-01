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
class Ves_Themesettings_Block_Html_Header extends Mage_Page_Block_Html_Header
{
	public function _construct(){
		$ves_theme = Mage::helper('themesettings/theme');
		$store = Mage::app()->getStore();
		$package_name = Mage::getSingleton('core/design_package')->getPackageName();
		$theme = Mage::getSingleton('core/design_package')->getTheme('template');
		$isVenusTheme = $ves_theme->isVenusTheme($package_name,$theme);
		if($isVenusTheme){
			$header_template = Mage::helper('themesettings')->getConfig('header/layout');
			$header_template = 'common'. DS .'header' . DS . $header_template . '.phtml';
			$this->setTemplate($header_template);
		}else{
			parent::_construct();
		}
	}

	public function getLogoSrc()
	{
		if(Mage::helper('themesettings')->getConfig('header/enable_customlogo') && ($logo_src=Mage::helper('themesettings')->getConfig('header/custom_logo'))!=''){
			return Mage::getBaseUrl('media') . 'ves_themesettings/logo/' . $logo_src;
		}
		if (empty($this->_data['logo_src'])) {
			$this->_data['logo_src'] = Mage::getStoreConfig('design/header/logo_src');
		}
		return $this->getSkinUrl($this->_data['logo_src']);
	}

	public function getLogoSrcSmall()
	{
		if(Mage::helper('themesettings')->getConfig('header/enable_customlogo') && ($logo_src_small=Mage::helper('themesettings')->getConfig('header/custom_logo_small'))!=''){
			return Mage::getBaseUrl('media') . 'ves_themesettings/logo/' . $logo_src_small;
		}
		if (empty($this->_data['logo_src_small'])) {
			$this->_data['logo_src_small'] = Mage::getStoreConfig('design/header/logo_src_small');
		}
		return $this->getSkinUrl($this->_data['logo_src_small']);
	}
}