<?php
class Cminds_Supplierfrontendproductuploader_Model_Config_Source_Register_Groups {

    public function toOptionArray()
    {
        $customerGroupCollection = Mage::getModel("customer/group")
            ->getCollection();
        $supplierHelper = Mage::helper("supplierfrontendproductuploader");
        $allowedGroups = $supplierHelper->getAllowedGroups();

        if ($allowedGroups) {
            $customerGroupCollection->addFieldToFilter('customer_group_id', array("in" => $allowedGroups));
        } else {
            $customerGroupCollection->addFieldToFilter('customer_group_id', 0);
        }
        $config = array();

        foreach ($customerGroupCollection as $group) {
            $config[] = array(
                "value" => $group->getCustomerGroupId(),
                "label" => $group->getCustomerGroupCode(),
            );
        }

        return $config;
    }
}
