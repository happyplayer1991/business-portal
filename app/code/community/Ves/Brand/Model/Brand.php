<?php
/*------------------------------------------------------------------------
 # VenusTheme Brand Module 
 # ------------------------------------------------------------------------
 # author:    VenusTheme.Com
 # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
 # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://www.venustheme.com
 # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/

class Ves_Brand_Model_Brand extends Mage_Core_Model_Abstract
{
	const CACHE_BLOCK_SCROLL_TAG = "ves_brand_scroll";
	const CACHE_BLOCK_LIST_TAG = "ves_brand_scroll";
	const CACHE_WIDGET_SCROLL_TAG = "ves_brand_widget_scroll";
	const CACHE_WIDGET_LIST_TAG = "ves_brand_widget_scroll";

    protected function _construct() {	
        $this->_init('ves_brand/brand');
    }
	
	public function getLink(){
		if($isSecure = Mage::app()->getStore()->isCurrentlySecure()) {
			$base_url = Mage::getBaseUrl( Mage_Core_Model_Store::URL_TYPE_LINK, true );
		} else {
			$base_url = Mage::getBaseUrl();
		}
		$store_id = Mage::app()->getStore()->getId();
		$request_path = "";
		if($store_id) {
			$request_path = Mage::getModel('core/url_rewrite')->loadByIdPath('venusbrand/brand/'.$this->getId()."/store_id/".$store_id)->getRequestPath();
		} 
		if($request_path){
			return $base_url.$request_path;
		}
		else {
			return $base_url.Mage::getModel('core/url_rewrite')->loadByIdPath('venusbrand/brand/'.$this->getId())->getRequestPath();
		}
	}
	
	public function getImageUrl($type='l') {
		$tmp = explode("/", $this->getFile());
		$imageName = $type."-".$tmp[count($tmp)-1];
		return Mage::getBaseUrl( Mage_Core_Model_Store::URL_TYPE_MEDIA)."resized/".$imageName;
	}
	
	public function getIconUrl( ) {
		return Mage::getBaseUrl( Mage_Core_Model_Store::URL_TYPE_MEDIA)."/".$this->getIcon();
	}
	
	public function getFileUrl(){
		return Mage::getBaseUrl( Mage_Core_Model_Store::URL_TYPE_MEDIA)."/".$this->getFile();
	}
	public function getDescription() {
		$description = $this->getData("description");

		Mage::getSingleton('core/session', array('name'=>'adminhtml'));
		if (! is_null(Mage::registry("_singleton/admin/session"))) {
			if(Mage::getSingleton('admin/session')->isLoggedIn()){ /*Is admin*/
			  //do stuff
				return $description;
			}
		}

		$processor = Mage::helper('cms')->getPageTemplateProcessor();
		return $processor->filter($description);
	}
}