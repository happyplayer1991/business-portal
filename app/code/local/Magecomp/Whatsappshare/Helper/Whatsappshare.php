<?php
/**
 * Magento Whatsappshare extension
 *
 * @category   Magecomp
 * @package    Magecomp_Whatsappshare
 * @author     Magecomp
 */
class Magecomp_Whatsappshare_Helper_Whatsappshare extends Mage_Core_Helper_Abstract
{
	public function isEnabled()
	{
		if(Mage::getStoreConfig('whatsappshare/license_status_group/status', Mage::app()->getStore()) == '1') 
		{
			return true;
		}
		return false;
	}
	
	public function getSize()
	{
		return(Mage::getStoreConfig('whatsappshare/whatsappshare_options/size', Mage::app()->getStore()));
	}	
}



