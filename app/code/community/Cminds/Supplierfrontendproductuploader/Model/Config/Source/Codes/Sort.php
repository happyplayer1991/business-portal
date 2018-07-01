<?php
class Cminds_Supplierfrontendproductuploader_Model_Config_Source_Codes_Sort {
    const LOWER_PRICE = 0;
    const SUPPLIER_RATINGS = 1;
    const SORT_LEVEL = 2;

    public function toOptionArray() {
        if(Mage::getConfig()->getModuleConfig('Cminds_Marketplace')->is('active', 'true')) {
            $options = array(
                array('value' => self::LOWER_PRICE, 'label' => Mage::helper('supplierfrontendproductuploader')->__('Lower Price')),
                array('value' => self::SUPPLIER_RATINGS, 'label' => Mage::helper('supplierfrontendproductuploader')->__('Supplier Ratings')),
                array('value' => self::SORT_LEVEL, 'label' => Mage::helper('supplierfrontendproductuploader')->__('Sort Level'))
            );
        } else {
            $options = array(
                array('value' => self::LOWER_PRICE, 'label' => Mage::helper('supplierfrontendproductuploader')->__('Lower Price')),
                array('value' => self::SORT_LEVEL, 'label' => Mage::helper('supplierfrontendproductuploader')->__('Sort Level'))
            );
        }
        return $options;
    }
}