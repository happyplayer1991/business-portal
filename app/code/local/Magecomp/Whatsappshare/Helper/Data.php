<?php
/**
 * Magento Whatsappshare extension
 *
 * @category   Magecomp
 * @package    Magecomp_Whatsappshare
 * @author     Magecomp
 */
class Magecomp_Whatsappshare_Helper_Data extends Mage_Core_Helper_Abstract
{
	const WSHARE_ONLY_MOBILE   = 'whatsappshare/license_status_group/onlymobile';
	
	public function isEnabled()
	{
		if(Mage::getStoreConfig('whatsappshare/license_status_group/status', Mage::app()->getStore()) == '1') {
				return true;
		}
		return false;
			
	}
	
   public function getCategoryConfiguration()
   {
		$display = 1;
		if(Mage::getStoreConfig('whatsappshare/license_status_group/category_specific') == 1)
		{
		  $display = 0;
		  $_cat = Mage::getModel('catalog/category')->load(Mage::registry('current_category')->getId());
		  $display =  $_cat->getData('whatsapp_category');
		}
		return $display;
   }
	
   public function getWhatsappShareHtmlForCategory($_product)
   {
	  $returnValue = "";
	  $mobile = Mage::helper('whatsappshare/mobile');
	  
	  $url = 'https://web.whatsapp.com/';
	  
	  if(Mage::getStoreConfig( self::WSHARE_ONLY_MOBILE))
	  {
		if(!($mobile->isMobile()||$mobile->isTablet()))
		{
			return $returnValue ;
		}	 
	  }
	  if($mobile->isMobile()||$mobile->isTablet())
	  { 
		  $url = 'whatsapp://';	 
	  }
	  
	  if($this->isEnabled() && $this->getCategoryConfiguration())
		{
			$returnValue .= '<div class="list-whatsapp">';
            $text = "";
			
			$text = Mage::getStoreConfig('whatsappshare/whatsappshare_options/custmessage',Mage::app()->getStore());
			if($text != "")
			{
				$text .= "%0a";
				$text .= "%0a";
			}
			$desc_length = Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_desc_length',Mage::app()->getStore());
			
			if(Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_name_enable'))
			{
				$text .= Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_name')." ". $_product->getName(). " ";
				$text .= "%0a";
				$text .= "%0a";
			}
			if(Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_desc_enable'))
			{
				$text .= Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_desc')." ".$_product->getShortDescription()." ";
				$text .= "%0a";
				$text .= "%0a";
			}
			if(Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_price_enable'))
			{
				$text .= Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_price')." " .Mage::helper('core')->currency($_product->getPrice(), true, false)." ";
				$text .= "%0a";
				$text .= "%0a";
			}
							
			if($_product->getSpecialPrice() > 0)
			{
				if(Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_special_price_enable'))
				{	
					$text .= Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_special_price')." ".Mage::helper('core')->currency($_product->getSpecialPrice(), true, false)." ";
					$text .= "%0a";
					$text .= "%0a";
				}
				if(Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_discount_enable'))
				{
						$text .= "Discount ". Mage::helper('core')->currency(($_product->getPrice() - $_product->getSpecialPrice()), true, false);
						$text .= "%0a";
						$text .= "%0a";
				}
			}
			
			if(Mage::getStoreConfig('whatsappshare/whatsappshare_options/codeenable'))
			{
				$text .= Mage::getStoreConfig('whatsappshare/whatsappshare_options/coupancode_label')." ";
				$codes = array('{{coupancode}}','{{discountper}}');
		        $accurate = array(Mage::getStoreConfig('whatsappshare/whatsappshare_options/coupancode'),
							   Mage::getStoreConfig('whatsappshare/whatsappshare_options/discount_per').'Per');

				$text.= str_replace($codes,$accurate,Mage::getStoreConfig('whatsappshare/whatsappshare_options/message'));
				
				$text .= "%0a";
				$text .= "%0a";
			}
			
			// UTM URL ENABLED OR NOT
			if(Mage::getStoreConfig('whatsappshare/whatsappshare_options/utmurl'))
			{
				$returnValue .=	'<a  target="_blank" href="'.$url.'send?text='.$text.'%0A%0A'.$this->getBitlyUrl($_product->getProductUrl().'?utm_source=whatsappshare').'" class="wa_btn wa_btn_s list_wa_btn_s">Share</a>';
			}
			else
			{
				$returnValue .=	'<a  target="_blank" href="'.$url.'send?text='.$text.'%0A%0A'.$this->getBitlyUrl($_product->getProductUrl()).'" class="wa_btn wa_btn_s list_wa_btn_s">Share</a>';
			}
			$returnValue .= ' </div>';
		}
		return  $returnValue;	
   }
   
	public function getBitlyUrl($url)
	{
		$connectURL = $url;
		if(Mage::getStoreConfig('whatsappshare/whatsappshare_bitly/enable'))
		{	
			$login = Mage::getStoreConfig('whatsappshare/whatsappshare_bitly/loginname');
			$appkey = Mage::getStoreConfig('whatsappshare/whatsappshare_bitly/apikey');
			$format = Mage::getStoreConfig('whatsappshare/whatsappshare_bitly/format');
			$connectURL = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;
			return $this->curl_get_result($connectURL);
		}
		return $url;
	}
	
	public function curl_get_result($url)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}