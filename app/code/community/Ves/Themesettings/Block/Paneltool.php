<?php
class Ves_Themesettings_Block_Paneltool extends Mage_Core_Block_Template{

	public function __construct(){
		return parent::__construct();
	}

	public function getStoreSwitcherHtml(){
		$store_switcher = Mage::getSingleton('core/layout')->createBlock('page/switch', "store_switcher")->setTemplate('ves/themesettings/paneltool/stores.phtml')->toHtml();
		return $store_switcher;
	}

	public function _toHtml(){
		$ves = Mage::helper('themesettings');
		$enable_paneltool = $ves->getConfig('general/enable_paneltool');
		if(!$enable_paneltool){
			return;
		}
		return parent::_toHtml();
	}

	public function getSkins(){
		$package_name = Mage::getSingleton('core/design_package')->getPackageName();
		$theme = Mage::getSingleton('core/design_package')->getTheme('template');
		$output = array();
		$skinDir = Mage::getBaseDir('skin'). DS . 'frontend' . DS . $package_name . DS . $theme . DS . 'css' . DS .'skins' . DS;
		$skins = array_filter(glob($skinDir . '*'), 'is_dir');
		$ves_theme = Mage::helper('themesettings/theme');
		$isVenusTheme = $ves_theme->isVenusTheme($package_name,$theme);
		if($isVenusTheme){
			$output[] = array(
				'label' => 'Default',
				'value' => 'default'
				);
			foreach ($skins as $k => $v) {
				$output[] = array(
					'label' => ucfirst(str_replace($skinDir, "", $v)),
					'value' => str_replace($skinDir, "", $v)
					);
			}
		}
		return $output;
	}

	public function getHeaderLayouts(){
		$package_name = Mage::getSingleton('core/design_package')->getPackageName();
		$theme = Mage::getSingleton('core/design_package')->getTheme('template');
		$headerDir = Mage::getBaseDir('app') . DS . 'design' . DS . 'frontend' . DS . $package_name . DS . $theme . DS . 'template' . DS . 'common' . DS . 'header' . DS;
		$headers = glob($headerDir . '*.phtml');
		$output = array();
		$replacePattern = array(
			$headerDir => '',
			'.phtml' => '',
			);
		foreach ($headers as $k => $v) {
			$output[] = array(
				'label' => str_replace(array_keys($replacePattern),array_values($replacePattern),$v),
				'value' => str_replace(array_keys($replacePattern),array_values($replacePattern),$v)
				);
		}
		return $output;
	}

	public function getFormUrl(){
		return Mage::getUrl('themesettings/index/panel');
	}
}