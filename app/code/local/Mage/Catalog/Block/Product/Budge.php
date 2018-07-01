<?php

class Mage_Catalog_Block_Product_Budge extends Mage_Catalog_Block_Product_Abstract
{
    public function getCompanyName()
    {
        $product = Mage::registry('product');
        $supplier_id = $product->getCreatorId();
        $supplier = Mage::getModel('customer/customer')->load($supplier_id);
        $address_id = $supplier->getDefaultBilling();
        $address = Mage::getModel('customer/address')->load($address_id);
        $company = $address->getCompany();
        return $company;
    }
    
    // public function getInfo()
    // {
    //     // return Mage::getModel('ves_brand/group')->getCompanyId('Company2');
    //     return Mage::getModel('ves_brand/brand')->getAllBudges(1);
    // }

    // public function getBudge()
    // {
        
    // }
}