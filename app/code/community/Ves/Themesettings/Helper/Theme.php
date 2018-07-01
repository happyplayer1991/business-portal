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
class Ves_Themesettings_Helper_Theme extends Mage_Core_Helper_Abstract
{	
	protected $_isVesTheme;
	protected $vesTheme = array();

	/**
	 * Prepare paths
	 */
	public function __construct()
	{
		$this->_vesTheme = $this->getVenusTheme();
	}
	/*
	 * List all vesnus theme
	 */
	public function getVenusTheme(){
		$skinPath = Mage::getBaseDir('skin') . DS . 'frontend' . DS;
		$configFilePaths = glob($skinPath . '*' . DS . '*' . DS . 'config.xml');
		$vesTheme = array();
		foreach ($configFilePaths as $key => $filePath) {
			$config = simplexml_load_file($filePath);
			$theme_info = str_replace($skinPath, "", $filePath);
			$theme_info = str_replace(DS."config.xml", "", $theme_info);
			$info = explode(DS, $theme_info);
			$theme_info = str_replace(DS, "/", $theme_info);
			$vesTheme[$theme_info] = array(
				'package' => $info[0],
				'theme' => $info[1],
				'config' => $config
				);
		}
		return $vesTheme;
	}

	public function getTheme($package_name, $theme){
		$themeInfo = array();
		$themePath = $package_name.'/'.$theme;
		$vesTheme = $this->_vesTheme;
		if(isset($vesTheme[$themePath])){
			$themeInfo = $vesTheme[$themePath];
		}
		return $themeInfo;
	}

	public function isVenusTheme($package,$theme = ''){
		$isVenusTheme = false;
		$vesTheme = $this->_vesTheme;
		if($theme==''){
			$theme = 'default';
		}
		$themePath = $package.'/'.$theme;
		if(isset($vesTheme[$themePath])){
			$isVenusTheme = true;
		}
		return $isVenusTheme;
	}

	public function getCurrenTheme(){
		$store = Mage::app()->getStore();
		$package_name = Mage::getSingleton('core/design_package')->getPackageName();
		$theme = Mage::getSingleton('core/design_package')->getTheme('template');
		if($theme == ''){
			$theme = 'default';
		}
		$vesTheme = $this->_vesTheme;
		$themeInfo = array();
		$themePath = $package_name.'/'.$theme;
		if(isset($vesTheme[$themePath])){
			$themeInfo = $vesTheme[$themePath];
		}
		return $themeInfo;
	}

}