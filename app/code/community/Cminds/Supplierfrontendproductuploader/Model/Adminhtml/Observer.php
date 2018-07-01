<?php

/**
 * Class Cminds_Supplierfrontendproductuploader_Model_Adminhtml_Observer
 */
class Cminds_Supplierfrontendproductuploader_Model_Adminhtml_Observer
{

    /**
     * @param $observer
     */
    public function onAttributeSetSave($observer)
    {
        $attributeSet = $observer->getObject();
        $postData = Mage::app()->getRequest()->getPost();
        if (is_array($postData)) {
            $data = (object)$postData;
        } else {
            $data = json_decode($postData['data']);
        }

        if (isset($data->data)) {
            $data = json_decode($data->data);
        }

        $attributeSet->setData('available_for_supplier', $data->available_for_supplier);
    }

    /**
     * @param $observer
     * @return bool
     */
    public function onCustomerSave($observer)
    {
        $request = $observer->getRequest();
        $customer = $observer->getCustomer();
        $postData = $request->getPost();

        if (!Mage::helper('supplierfrontendproductuploader')->isSupplier($customer->getId())) {
            return false;
        }

        try {
            $this->updateBindAttributes($postData, $customer->getId());
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            Mage::log($e->getMessage());
        }
    }

    /**
     * Save data from customer tab supplierfrontendproductuploader/adminhtml_customer_edit_tab_attributesets.
     *
     * @param $postData
     * @param $supplierId
     */
    public function updateBindAttributes($postData, $supplierId)
    {
        if (isset($postData['all_attributes_ids']) && is_array($postData['all_attributes_ids'])) {
            foreach ($postData['all_attributes_ids'] as $attribute) {
                Mage::getModel('supplierfrontendproductuploader/attributesets')
                    ->getCollection()
                    ->addFilter('supplier_id', $supplierId)
                    ->addFilter('attribute_set_id', $attribute)
                    ->getFirstItem()
                    ->delete();

                if (!is_array($postData['attributes_ids'])
                    || (is_array($postData['attributes_ids']) && in_array($attribute, $postData['attributes_ids']))
                ) {
                    Mage::getModel('supplierfrontendproductuploader/attributesets')
                        ->setData('supplier_id', $supplierId)
                        ->setData('attribute_set_id', $attribute)
                        ->save();
                }
            }
        }
    }
}
