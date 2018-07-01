<?php
class Cminds_Supplierfrontendproductuploader_Model_Observer extends Mage_Core_Model_Abstract
{
    public function onOrderPlaced($observer)
    {
        $orderId = $observer->getEvent()->getOrder()->getId();
        $order = Mage::getModel('sales/order')->load($orderId);
        $items = $order->getAllItems();
        $datas = array();

        foreach ($items as $item) {
            $data = array();
            $product = Mage::getModel('catalog/product')->load($item->getProductId());

            if ($product->getData('creator_id') != null) {
                $data['name'] = $item->getName();
                $data['price'] = $item->getPrice();
                $data['sku'] = $item->getSku();
                $data['supplier_id'] = $product->getData('creator_id');
                $data['id'] = $item->getProductId();
                $data['qty'] = $item->getQtyToInvoice();
                $data['qty_ordered'] = $item->getQtyOrdered();

                if ($order->getShippingAddress()) {
                    $data['firstname'] = $order->getShippingAddress()->getFirstname();
                    $data['lastname'] = $order->getShippingAddress()->getLastname();
                    $data['street'] = $order->getShippingAddress()->getStreet();
                    $data['city'] = $order->getShippingAddress()->getCity();
                    $data['email'] = $order->getShippingAddress()->getEmail();
                    $data['postcode'] = $order->getShippingAddress()->getPostcode();
                    $data['region'] = $order->getShippingAddress()->getRegion();
                    $data['getCountryId'] = $order->getShippingAddress()->getCountryId();
                } else {
                    $data['firstname'] = null;
                    $data['lastname'] = null;
                    $data['street'] = null;
                    $data['city'] = null;
                    $data['email'] = null;
                    $data['postcode'] = null;
                    $data['region'] = null;
                    $data['getCountryId'] = null;
                }

                $data['order_id'] = $orderId;
                $datas[$product->getData('creator_id')][] = $data;
            }
        }

        Mage::unregister("order-supplier-products");
        Mage::register('order-supplier-products', $order);

        foreach ($datas as $vendor_id => $items) {
            $vendor = Mage::getModel('customer/customer')
                          ->load($vendor_id);

            Mage::getModel('supplierfrontendproductuploader/email_order')
                ->setVendor($vendor)
                ->setOrder($order)
                ->setItems($items)
                ->send();
        }
    }

    public function onCustomerSaveBefore($observer) {
        try {
            $customer = $observer->getCustomer();
            $postData = Mage::getSingleton('core/app')->getRequest()->getPost();

            if( isset($postData['group_id']) ) {
                $customer->setData( 'group_id', $postData['group_id'] );
            }
        } catch ( Exception $e ) {
            Mage::log( "Failed setting customer group id: " . $e->getMessage() );
        }
    }

    public function onAttributeSaveAfter($observer) {
        $attributeData = $observer->getEvent()->getDataObject()->getData();

        if(!isset($attributeData['frontend_label'])) return;
        if(!isset($attributeData['frontend_label_marketplace'])) return;

        $marketplaceLabel = $attributeData['frontend_label_marketplace'];
        $attributeId = $attributeData['attribute_id'];
        $attributeCode = $attributeData['attribute_code'];
        $marketplaceLabels = Mage::getModel('supplierfrontendproductuploader/labels')
            ->load($attributeId, 'attribute_id');
        $marketplaceLabels->setLabel($marketplaceLabel);

        if($marketplaceLabels->getId() == null) {
            $marketplaceLabels->setAttributeId($attributeId);
            $marketplaceLabels->setAttributeCode($attributeCode);
        }

        $marketplaceLabels->save();
    }

    protected function notifySup($items)
    {
        foreach ($items AS $vendor_id => $itemArray) {
        }
    }

    public function addFieldToAttributeEditForm($observer)
    {

        $fieldset = $observer->getForm()->getElement('base_fieldset');
        $fieldset->addField('available_for_supplier', 'select', array(
            'name' => 'available_for_supplier',
            'label' => Mage::helper('core')->__('Visible for Supplier'),
            'title' => Mage::helper('core')->__('Visible for Supplier'),
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
        ));

    }

    public function checkPath()
    {
        $supplierUrlCheck = strpos(Mage::helper('core/url')->getCurrentUrl(), 'supplierfrontendproductuploader/');
        if ($supplierUrlCheck !== false) {
            $currentUrl = Mage::helper('core/url')->getCurrentUrl();
            $currentUrl = str_replace('supplierfrontendproductuploader/', 'supplier/', $currentUrl);
            $response = Mage::app()->getFrontController()->getResponse();
            $response->setRedirect($currentUrl);
            $response->sendResponse();
            Mage::app()
                ->getFrontController()
                ->getAction()
                ->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);

            return $this;
        }
    }

    public function onCustomerLogin(Varien_Event_Observer $observer)
    {
        $needApprove = Mage::getStoreConfig('supplierfrontendproductuploader_catalog/general/supplier_needs_to_be_approved');

        $customer = $observer->getEvent()->getCustomer();
        $session = Mage::getSingleton('customer/session');
        /*
         * If logged user isn't supplier go away.
         */
        if (!Mage::helper('supplierfrontendproductuploader')->isSupplier($customer)) {
            return;
        }

        /*
         * If supplier is'nt approved logout him.
         */
        if ($needApprove) {
            $approved = (bool)$customer->getSupplierApprove();
            if (!$approved) {
                $session->setId(null)
                    ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
                    ->getCookie()->delete('customer');

                Mage::getSingleton('customer/session')->addError(
                    Mage::helper('supplierfrontendproductuploader')->__('Your account isn\'t approved yet.')
                );

                Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl().'supplierfrontendproductuploader/login/');
                throw new Exception('', 1022);
                return;
            }
        }

    }

    public function onChangeSortCondition()
    {
        if(Mage::helper('supplierfrontendproductuploader')->isProductCodeEnabled()) {
            $collection = Mage::getResourceModel('supplierfrontendproductuploader/product_collection')
                ->filterBySupplierCode();
            $collection->getSelect()->group('supplier_product_code');
            foreach($collection as $product) {
                Mage::helper('supplierfrontendproductuploader')
                    ->setVisibilities($product->getSupplierProductCode());
            }
        }

        return;
    }

    public function onProductSaveAfter(Varien_Event_Observer $observer)
    {
        if(Mage::helper('supplierfrontendproductuploader')->isProductCodeEnabled()) {
            $product = $observer->getEvent()->getProduct();
            if(!$product->getSupplierProductCode() || $product->getSupplierProductCode() == '') {
                return;
            }
            Mage::helper('supplierfrontendproductuploader')
                ->setVisibilities($product->getSupplierProductCode());
        }
        return;
    }

    public function onCartPageLoad(Varien_Event_Observer $observer)
    {
        $creatorIds = array();
        $cart = Mage::getModel('checkout/cart')->getQuote();

        foreach ($cart->getAllItems() as $item) {
            $creatorIds[] = Mage::getModel('catalog/product')->load(
                $item->getProduct()->getId()
            )->getCreatorId();
        }

        $creatorIds = array_unique($creatorIds);

        if (count($creatorIds) > 1) {
            Mage::register("is_multivendor_cart", true);
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function cmsPageSaveBefore(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('supplierfrontendproductuploader');
        $cmsPage = $observer->getEvent()->getDataObject();

        if ((int)$cmsPage->getId() !== $helper->getTermsPageId()) {
            return $this;
        }

        if ($cmsPage->dataHasChangedFor('content')) {
            $this->forceTermsAgree();
        }
    }

    /**
     * Set force vendors to agree update terms and conditions.
     */
    public function forceTermsAgree()
    {
        $vendors = Mage::getModel('customer/customer')
            ->getCollection()
            ->addAttributeToSelect('terms_conditions_agreed')
            ->addFieldToFilter(
                'group_id',
                array('in', Mage::helper('supplierfrontendproductuploader')->getAllowedGroups())
            );

        foreach ($vendors as $vendor) {
            if ($vendor->getData('terms_conditions_agreed')) {
                $vendor->setData('terms_conditions_agreed', 0);
                $vendor->getResource()->saveAttribute($vendor, 'terms_conditions_agreed');
            }
        }
    }
}
