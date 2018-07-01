<?php
class Cminds_Supplierfrontendproductuploader_Model_Email_Order
    extends Cminds_Supplierfrontendproductuploader_Model_Email_Abstract {

    const XML_PATH_NOTIFICATION = 'supplier_new_purchase';

    public function send()
    {
        $emailHelper = Mage::helper("supplierfrontendproductuploader/email");

        if ($emailHelper->canSendHtml()) {
            $this->sendHtml();
        } else {
            foreach ($this->getItems() as $item) {
                $data = $this->prepareTextData($item);
                $this->sendText(
                    $this->getVendor()->getName(),
                    $this->getVendor()->getEmail(),
                    $data[0],
                    $data[1],
                    $item
                );
            }
        }
    }

    protected function sendHtml()
    {
        /**
         * @var Cminds_Supplierfrontendproductuploader_Helper_Email $emailHelper
         */
        $emailHelper = Mage::helper("supplierfrontendproductuploader/email");
        $emailTemplate  = Mage::getModel('core/email_template')
            ->loadDefault(self::XML_PATH_NOTIFICATION);

        $emailTemplateVariables = array();

        try {
            $paymentBlock = Mage::helper('payment')
                ->getInfoBlock($this->getOrder()->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore(Mage::app()->getStore()->getId());
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            throw $exception;
        }

        $emailTemplateVariables['order'] = $this->getOrder();
        $emailTemplateVariables['vendor'] = $this->getVendor();
        $emailTemplateVariables['payment_html'] = $paymentBlockHtml;

        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);

        $emailTemplate->setSenderName($emailHelper->getGlobalSenderName());
        $emailTemplate->setSenderEmail($emailHelper->getGlobalSenderEmail());

        $attachment = '';
        if ($emailHelper->canCreateAndAttachPdfConfirmation()) {
            $attachment = $this->generatePdf();
            $emailTemplate
                ->getMail()
                ->createAttachment(
                    file_get_contents($attachment),
                    Zend_Mime::TYPE_OCTETSTREAM,
                    Zend_Mime::DISPOSITION_ATTACHMENT,
                    Zend_Mime::ENCODING_BASE64,
                    basename($attachment)
                );
        }

        $emailTemplate->send(
            $this->getVendor()->getEmail(),
            $this->getVendor()->getName(),
            $emailTemplateVariables
        );

        if ($attachment) {
            unlink($attachment);
        }

        return $this;
    }

    protected function prepareTextData($item)
    {
        $emailHelper = Mage::helper("supplierfrontendproductuploader/email");

        if ($emailHelper->canSendPurchasedItemNotification()
            && $this->getVendor()->getData('notification_product_ordered')) {
            $topic  = $emailHelper->getPurchasedItemsTextTitle();
            $message = $emailHelper->getPurchasedItemsTextBody();

            $product = Mage::getModel("catalog/product")->load($item['id']);

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
            $productLink = Mage::getUrl($product->getUrlPath());

            if (is_array($item['street'])) {
                $street = join(' ', $item['street']);
            } else {
                $street = $item['street'];
            }

            $replaces = array(
                $this->getVendor()->getName(),
                $product->getName(),
                $productLink,
                $item['qty_ordered'],
                $item['price'],
                $item['sku'],
                $item['firstname'],
                $item['lastname'],
                $street,
                $item['city'],
                $item['email'],
                $item['postcode'],
                $item['region'],
                $item['getCountryId'],
                $this->getOrder()->getIncrementId()
            );

            $rMessage = str_replace($replacements, $replaces, $message);
            $rTopic = str_replace($replacements, $replaces, $topic);
            return array($rTopic, $rMessage);
        }
    }
}