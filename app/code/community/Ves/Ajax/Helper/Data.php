<?php
class Ves_Ajax_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getAddToCartUrl($product, $additional = array())
	{
		return $this->helper('checkout/cart')->getAddUrl($product, $additional);
	}

	public function getQuickviewUrl(Mage_Catalog_Model_Product $_product){
		return Mage::getUrl('ajax/quickview/view',array('id'=>$_product->getId()));
	}

    /**
     * Get value of the extension's configuration
     *
     * @return string
     */
    public function getConfig($key, $default = "", $package = "ajax", $storeCode = NULL) {
    	$return = "";
    	$_session_config = Mage::registry("ajax");

    	if ($_session_config && isset($_session_config[$package."/".$key])) {
    		$return = $_session_config[$package."/".$key];
    	}else {
    		$return = Mage::getStoreConfig("{$package}/{$key}", $storeCode);
    	}
    	if ($return == "" && $default) {
    		$return = $default;
    	}
    	return $return;
    }
}