<?php
class Cminds_Supplierfrontendproductuploader_Block_Product_Result extends Mage_Core_Block_Template
{
    protected $_template = 'supplierfrontendproductuploader/product/codes/result.phtml';
    protected $_product = null;

    protected function _getPhrase() {
        return Mage::registry('requested_phrase');
    }

    protected function _getProductName() {
        return Mage::registry('requested_name');
    }

    protected function _getProductSku() {
        return Mage::registry('requested_sku');
    }

    public function getItems() {
        $collection = Mage::getResourceModel('supplierfrontendproductuploader/product_collection')
            ->addAttributeToSelect('name')
            ->filterByName($this->_getProductName())
            ->filterBySupplierCode($this->_getPhrase(), false)
            ->filterBySku($this->_getProductSku());

        return $collection;
    }
}
