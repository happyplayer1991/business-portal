<?php
class Cminds_Supplierfrontendproductuploader_Helper_Email extends Mage_Core_Helper_Abstract
{
    public function getGlobalSenderName()
    {
        return Mage::getStoreConfig('trans_email/ident_general/name');
    }

    public function getGlobalSenderEmail()
    {
        return Mage::getStoreConfig('trans_email/ident_general/email');
    }

    public function canSendPurchasedItemNotification()
    {
        return Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/'
            . 'supplierfrontendproductuploader_supplier_notification_config/'
            . 'notify_on_product_was_ordered'
        );
    }

    public function canCreateAndAttachPdfConfirmation()
    {
        return Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/'
            . 'supplierfrontendproductuploader_supplier_notification_config/'
            . 'attach_pdf'
        );
    }
    public function getPurchasedItemsTextTitle()
    {
        return Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/'
            . 'supplierfrontendproductuploader_supplier_notification_config/'
            . 'email_title_on_product_was_ordered'
        );
    }

    public function getPurchasedItemsTextBody()
    {
        return Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/'
            . 'supplierfrontendproductuploader_supplier_notification_config/'
            . 'email_text_on_product_was_ordered'
        );
    }

    public function canSendHtml()
    {
        return Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/'
            . 'supplierfrontendproductuploader_supplier_notification_config/'
            . 'email_type'
        );
    }

    private function _isConfigEnabled($slug)
    {
        return $this->getConfig($slug);
    }

    private function getConfig($slug)
    {
        return Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/'
            . 'supplierfrontendproductuploader_supplier_notification_config/'
            . $slug
        );
    }
    
    public function _sendEmail($receiverName, $receiverEmail, $title, $message)
    {
        if ($message == '') {
            return false;
        }
        
        $senderName = Mage::getStoreConfig('trans_email/ident_general/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');

        try {
            /** @var Mage_Core_Model_Email_Template $emailTemplate */
            $emailTemplate  = Mage::getModel('core/email_template')->loadDefault('cminds_email_placeholder');

            $emailTemplate
                ->setSenderName($senderName)
                ->setSenderEmail($senderEmail)
                ->setTemplateSubject($title)
                ->setQueue(true);

            $emailTemplate->send($receiverEmail, $receiverName, array('message' => nl2br($message)));
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }
    }

    public function productApproved($customerData, $productData)
    {
        $isEnabled = $this->_isConfigEnabled('notify_on_product_was_approved');
        if ($isEnabled == 1 && $customerData->getData('notification_product_approved') == 1) {
            $topic  = $this->getConfig('email_title_on_product_approved');
            $message = $this->getConfig('email_text_on_product_approved');
            
            $replacements = array('{{supplierName}}', '{{productName}}', '{{productLink}}', '{{productQty}}');
            $customerFullName = $customerData->getFirstname() .' '. $customerData->getLastname();
            $websites = $productData->getWebsiteIds();

            if ($websites) {
                $iDefaultStoreId = Mage::app()
                    ->getWebsite($websites[0])
                    ->getDefaultGroup()
                    ->getDefaultStoreId();
                $productLink = Mage::getModel('catalog/product')
                    ->setStoreId($iDefaultStoreId)
                    ->load($productData->getId())
                    ->getProductUrl();
            } else {
                $productLink = $productData->getProductUrl();
            }

            $replaces = array(
                $customerFullName,
                $productData->getName(),
                $productLink,
                Mage::getModel('cataloginventory/stock_item')->loadByProduct($productData)->getQty()
            );
            
            $rMessage = str_replace($replacements, $replaces, $message);
            $rTopic = str_replace($replacements, $replaces, $topic);
            $this->_sendEmail($customerFullName, $customerData->getEmail(), $rTopic, $rMessage);
        }
    }

    public function productPurchased($customerData, $productData, $itemData)
    {
        $isEnabled = $this->_isConfigEnabled('notify_on_product_was_ordered');
        $notified = false;
        $order = Mage::registry('order-supplier-products');

        if ($isEnabled == 1 && $customerData->getData('notification_product_ordered') == 1) {
            $topic  = $this->getConfig('email_title_on_product_was_ordered');
            $message = $this->getConfig('email_text_on_product_was_ordered');
            $Incrementid = $order->getIncrementId();
            $replacements = array(
                '{{supplierName}}',
                '{{productName}}',
                '{{productLink}}',
                '{{productQty}}',
                '{{price}}',
                '{{sku}}',
                '{{firstname}}',
                '{{lastname}}',
                '{{street}}',
                '{{city}}',
                '{{email}}',
                '{{postcode}}',
                '{{region}}',
                '{{getCountryId}}',
                '{{order_id}}'
            );
            $customerFullName = $customerData->getFirstname() .' '. $customerData->getLastname();
            $productLink = Mage::getUrl($productData->getUrlPath());

            if (is_array($itemData['street'])) {
                $street = join(' ', $itemData['street']);
            } else {
                $street = $itemData['street'];
            }

            $replaces = array(
                $customerFullName,
                $productData->getName(),
                $productLink,
                $itemData['qty_ordered'],
                $itemData['price'],
                $itemData['sku'],
                $itemData['firstname'],
                $itemData['lastname'],
                $street,
                $itemData['city'],
                $itemData['email'],
                $itemData['postcode'],
                $itemData['region'],
                $itemData['getCountryId'],
                $Incrementid
            );
            
            $rMessage = str_replace($replacements, $replaces, $message);
            $rTopic = str_replace($replacements, $replaces, $topic);
            $this->_sendEmail($customerFullName, $customerData->getEmail(), $rTopic, $rMessage);
            $notified = true;
        }

        $this->addOrderComment($order, $customerData, $notified);
        $order->save();
    }
    

    public function notifyOnSupplierAddNew($product)
    {
        $adminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
        $adminName = Mage::getStoreConfig('trans_email/ident_general/name');

        $this->_sendEmail(
            $adminName,
            $adminEmail,
            'New product added by supplier',
            'Product ' . $product->getId() . ' has been added, please check and approve it.'
        );
    }

    public function notifyAdminOnProductChange($product)
    {
        $adminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
        $adminName = Mage::getStoreConfig('trans_email/ident_general/name');

        $this->_sendEmail(
            $adminName,
            $adminEmail,
            'Remarked product was changed by supplier',
            'Product '.$product->getName().' (SKU: '.$product->getSku().', ID#'.$product->getId().') was remarked by you and changed by supplier.'
        );
    }

    public function notifyAdminOnUploadingProducts($customer, $products_count)
    {
        if (!Mage::helper('supplierfrontendproductuploader')->getNotifyImportAdminConfig()) {
            return false;
        }

        $adminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
        $adminName = Mage::getStoreConfig('trans_email/ident_general/name');
        $supplierName = $customer->getFirstname() .' '. $customer->getLastname();

        $this->_sendEmail(
            $adminName,
            $adminEmail,
            'Supplier uploaded products',
            'Supplier '.$supplierName.' uploaded ' . $products_count .' new product(s)'
        );
    }

    public function notifyAdminOnSupplierRegister($supplier)
    {
        $adminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
        $adminName = Mage::getStoreConfig('trans_email/ident_general/name');

        if (Mage::helper('supplierfrontendproductuploader')->isSupplierNeedsToBeApproved()) {
            $message = 'Supplier '.$supplier->getFirstname(). ' ' . $supplier->getLastname() . ' (Ip: ' . $supplier->getId() . ', Email: ' . $supplier->getEmail() . ') has registered and need to be approved.';
        } else {
            $message = 'Supplier '.$supplier->getFirstname(). ' ' . $supplier->getLastname() . ' (Ip: ' . $supplier->getId() . ', Email: ' . $supplier->getEmail() . ') has registered.';
        }
        $this->_sendEmail($adminName, $adminEmail, 'New supplier has registered', $message);
    }

    public function notifySupplierNeedApprove($supplier)
    {
        $emailTo = Mage::getModel('core/email_template');
        $emailTo->loadDefault('supplier_new_registered');
        $emailTo->setTemplateSubject(
            'Supplier confirmation for ' . $supplier->getFirstname() . ' ' . $supplier->getLastname()
        );

        $emailTo->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
        $emailTo->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));

        $emailToVariables['email'] = $supplier->getData('email');
        $emailToVariables['first_name'] = $supplier->getData('firstname');
        $emailToVariables['last_name'] = $supplier->getData('lastname');

        try {
            $emailTo->send(
                $supplier->getData('email'),
                $supplier->getData('firstname') . ' ' . $supplier->getData('lastname'),
                $emailToVariables
            );
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }
    }

    public function notifySupplierWhenApproved($supplierId)
    {
        $supplier = Mage::getModel('customer/customer')->load($supplierId);

        $emailToVariables['email'] = $supplier->getData('email');
        $emailToVariables['first_name'] = $supplier->getData('firstname');
        $emailToVariables['last_name'] = $supplier->getData('lastname');

        $from_email = Mage::getStoreConfig('trans_email/ident_general/email');
        $from_name = Mage::getStoreConfig('trans_email/ident_general/name');
        $sender = array('name'  => $from_name, 'email' => $from_email);

        $websiteId = $supplier->getWebsiteId();
        $website = Mage::getModel('core/website')->load($websiteId);
        $storeIds = $website->getStoreIds();

        try {
            Mage::getModel('core/email_template')
                ->sendTransactional(
                    'supplier_approved_by_admin',
                    $sender,
                    $supplier->getData('email'),
                    $supplier->getData('firstname'),
                    $emailToVariables,
                    $storeIds[0]
                );
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }
    }

    /**
     * Add comment to order about Supplier notification status.
     *
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Customer_Model_Customer $customerData
     * @param boolean $notified
     */
    public function addOrderComment(&$order, $customerData, $notified)
    {
        $supplierName = Mage::helper('supplierfrontendproductuploader')->getSupplierName(
            $customerData->getId()
        );

        $status = 'not sent';
        if ($notified) {
            $status = 'sent';
        }

        $order->addStatusHistoryComment($this->__(
            'The confirmation for vendor %s was %s',
            $supplierName,
            $status
        ));
    }
}
