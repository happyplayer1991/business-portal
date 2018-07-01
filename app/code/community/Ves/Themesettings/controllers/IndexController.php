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
class Ves_Themesettings_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
    {
		$this->loadLayout();
        $this->renderLayout();
    }

    public function panelAction(){
		$data = $this->getRequest()->getParams();
		$cookie = Mage::getModel('core/cookie');
		$ves = Mage::helper('themesettings');
		$enable_paneltool = $ves->getConfig('general/enable_paneltool');
		if(!empty($data) && $data['vespanel'] && !$data['vesreset'] && $enable_paneltool){
			$options = $data['userparams']?$data['userparams']:array();
			if(isset($options['store'])){
				$store = Mage::getModel('core/store')->load($options['store']);
				Mage::app()->setCurrentStore($store->getId());
			}
			$cookie->delete('vespaneltool');
			$cookie->set('vespaneltool', serialize($options));
		}
		if($data['vesreset']){
			$cookie->delete('vespaneltool');
		}
		$this->_redirectReferer();
	}
}