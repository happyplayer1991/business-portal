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

class Ves_Themesettings_Model_System_Config_Source_Header_HeaderLayout{
	public function toOptionArray()
	{
		$store_id = '';
		    if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) // store level
		    {
		    	$store_id = Mage::getModel('core/store')->load($code)->getId();
            }elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) // website level
            {
            	$website_id = Mage::getModel('core/website')->load($code)->getId();
            	$store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
            }else // default level
            {
            	$store_id = 0;
            }
            $package_name = Mage::getStoreConfig('design/package/name', $store_id);
            $theme = Mage::getStoreConfig('design/theme/defaults', $store_id);
            if($theme==''){
            	$theme = 'default';
            }
            if($store_id == 0){
            	$resource = Mage::getSingleton('core/resource');
            	$readConnection = $resource->getConnection('core_read');
            	$query = 'SELECT * FROM ' . $resource->getTableName('core_config_data').' WHERE scope ="default" AND path="design/package/name" ';
            	$dataDb = $readConnection->fetchAll($query);
            	$package_name = isset($dataDb[0])?$dataDb[0]['value']:'default';
            }

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
    }