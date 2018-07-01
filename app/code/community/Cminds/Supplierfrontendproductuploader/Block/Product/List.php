<?php
class Cminds_Supplierfrontendproductuploader_Block_Product_List extends Mage_Core_Block_Template
{
    public function _construct()
    {
        parent::_construct();
    }

    public function getItems()
    {
        $supplier_id = Mage::helper('supplierfrontendproductuploader')->getSupplierId();
        $status = $this->getRequest()->getParam('status');
        $name = $this->getRequest()->getParam('name', null);

        $collection = Mage::getResourceModel('supplierfrontendproductuploader/product_collection')
            ->filterBySupplier($supplier_id)
            ->filterByFrontendproductStatus($status)
            ->filterByName($name)
            ->setOrder('entity_id');

        $page = Mage::app()->getRequest()->getParam('p', 1);
        $collection->setPageSize(10)->setCurPage($page);

        return $collection;
    }

    public function getStatusLabel($status)
    {
        switch ($status) {
            case Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_PENDING:
                return $this->__('Pending');
                break;
            case Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_APPROVED:
                return $this->__('Approved');
                break;
            case Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_DISAPPROVED:
                return $this->__('Disapproved');
                break;
            case Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_NONACTIVE:
                return $this->__('Not Active');
                break;
            default:
                return $this->__('Unknown');
            break;
        }
    }
}
