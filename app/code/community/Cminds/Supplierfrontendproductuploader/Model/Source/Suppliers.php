<?php
class Cminds_Supplierfrontendproductuploader_Model_Source_Suppliers extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {
    public function getAllOptions()
    {
        if ($this->_options === null) {

            /**
             * @var Cminds_Supplierfrontendproductuploader_Helper_Data $supplierHelper
             */
            $supplierHelper = Mage::helper("supplierfrontendproductuploader");
            $allowedGroups = $supplierHelper->getAllowedGroups();

            $collection = Mage::getModel('customer/customer')
                ->getCollection();
//                ->addAttributeToSelect('*');

            if ($allowedGroups) {
                $collection->addFieldToFilter('group_id', array("in" => $allowedGroups));
            } else {
                $collection->addFieldToFilter('group_id', 0);
            }

            $this->_options[] = array('label' => null, 'value' => null);

            foreach ($collection as $customer) {
                $fullName = $supplierHelper->getSupplierName($customer);

                $this->_options[] = array(
                    'label'=> $fullName,
                    'value' => $customer->getId()
                );
            }
        }

        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}