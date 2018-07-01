<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ves_Themesettings_Block_Html_FooterCenter extends Mage_Core_Block_Template{
	public function _construct(){
		parent::_construct();
		$ves = Mage::helper('themesettings');
		$enable_footer_center = $ves->getConfig('footer/enable_footer_center');
		if(Mage::helper("themesettings")->checkModuleInstalled("Ves_BlockBuilder") && $enable_footer_center) {
			$blockId = $ves->getConfig('footer/center_layout');
			$blockBuilder = Mage::getModel("ves_blockbuilder/block")->load($blockId);
			$processor = Mage::helper('cms')->getPageTemplateProcessor();
			$html = $processor->filter($blockBuilder->getShortCode());
			$this->assign('blockBuilderHtml',$html);
		}
	}
}