<?php
class Cminds_Supplierfrontendproductuploader_Block_Sales_Order_Email_Items extends Mage_Sales_Block_Order_Email_Items {
    public function getAllItems()
    {
        $items = $this->getOrder()->getAllItems();
        $vendorItems = array();

        foreach ($items as $item) {
            $vendor_id = Mage::helper("supplierfrontendproductuploader")
                ->getProductSupplierId($item->getProduct());
            if ((int) $vendor_id === (int) $this->getVendor()->getId()) {
                $vendorItems[] = $item;
            }
        }

        return $vendorItems;
    }
}
