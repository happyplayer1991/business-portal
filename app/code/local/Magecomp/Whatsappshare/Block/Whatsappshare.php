<?php
/**
 * Magento Whatsappshare extension
 *
 * @category   Magecomp
 * @package    Magecomp_Whatsappshare
 * @author     Magecomp
 */
class Magecomp_Whatsappshare_Block_Whatsappshare extends Mage_Core_Block_Template
{
	const WSHARE_ONLY_MOBILE   = 'whatsappshare/license_status_group/onlymobile';
	
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
	public function getProductShareBtn()
	{
		$helper = Mage::helper('whatsappshare/data');
		$text = "";
		
		$url = 'https://web.whatsapp.com/';
		$mobile = Mage::helper('whatsappshare/mobile');
		if(Mage::getStoreConfig( self::WSHARE_ONLY_MOBILE))
		{
		  if(!($mobile->isMobile()||$mobile->isTablet()))
		  {
			  return $text ;
		  }	 
		}
		if($mobile->isMobile()||$mobile->isTablet())
		{ 
			$url = 'whatsapp://';	 
		}
		
		if($helper->isEnabled()):
			$text = Mage::getStoreConfig('whatsappshare/whatsappshare_options/custmessage',Mage::app()->getStore());
			if($text != "")
			{
				$text .= "%0a";
				$text .= "%0a";
			}
			$desc_length = Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_desc_length',Mage::app()->getStore());
			
			$currentUrl = Mage::helper('core/url')->getCurrentUrl();
			$newurl = '<a href='.$currentUrl.'>'.$currentUrl.'</a>';
			$currentproduct = Mage::registry('current_product');
			
			if(Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_name_enable'))
			{
				$text .= Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_name')." ". $currentproduct->getName(). " ";
				$text .= "%0a";
				$text .= "%0a";
			}
			
			if(Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_desc_enable')){
				$text .= Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_desc')." ". substr($currentproduct->getShortDescription(), 0, $desc_length)." ";
				$text .= "%0a";
				$text .= "%0a";
			}
			
			if(Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_price_enable')){
				$text .= Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_price')." " .Mage::helper('core')->currency($currentproduct->getPrice(), true, false)." ";
				$text .= "%0a";
				$text .= "%0a";
			}
			
			if($currentproduct->getSpecialPrice() > 0){
				if(Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_special_price_enable')){	
						$text .= Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_special_price')." ".Mage::helper('core')->currency($currentproduct->getSpecialPrice(), true, false)." ";
						$text .= "%0a";
						$text .= "%0a";
				}
				if(Mage::getStoreConfig('whatsappshare/whatsappshare_options/product_discount_enable')){
						$text .= "Discount ". Mage::helper('core')->currency(($currentproduct->getPrice() - $currentproduct->getSpecialPrice()), true, false);
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
			$text = html_entity_decode($text,ENT_XHTML);
		endif;
		
		
		$whatsappshareHelper = Mage::helper('whatsappshare/whatsappshare');
		$size = $whatsappshareHelper->getSize();
		
		$returntext = '';
		// UTM URL ENABLED OR NOT
		if(Mage::getStoreConfig('whatsappshare/whatsappshare_options/utmurl'))
		{
			$returntext .= "<a target='_blank' href='".$url."send?text=".$text."%0A%0A".$helper->getBitlyUrl($currentUrl."?utm_source=whatsappshare")."'";
		}
		else
		{
			$returntext .= "<a target='_blank' href='".$url."send?text=".$text."%0A%0A".$helper->getBitlyUrl($currentUrl)."'";
		}
		
		if($size == 'small')
		{
			$returntext .= "class='wa_btn wa_btn_s'>Share</a>";
		} 
		elseif($size=='medium')
		{
			$returntext .= "class='wa_btn wa_btn_m'>Share</a>";
		} 
		else
		{
			$returntext .= "class='wa_btn wa_btn_l'>Share</a>";
		} 
		
		return $returntext;
	}
}