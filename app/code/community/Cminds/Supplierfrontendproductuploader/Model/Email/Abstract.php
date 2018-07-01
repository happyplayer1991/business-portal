<?php
class Cminds_Supplierfrontendproductuploader_Model_Email_Abstract extends Varien_Object {
    public function send()
    {
        $this->sendHtml();
    }

    protected function sendText($receiverName, $receiverEmail, $title, $message, $item)
    {
        if ($message == '') {
            return false;
        }

        $emailHelper = Mage::helper("supplierfrontendproductuploader/email");

        $mail = new Zend_Mail("UTF-8");

        $mail
            ->setBodyHtml(nl2br($message))
            ->setFrom(
                $emailHelper->getGlobalSenderEmail(),
                $emailHelper->getGlobalSenderName()
            )
            ->addTo($receiverEmail, $receiverName)
            ->setSubject($title);

        try {
            $attachment = false;
            if ($emailHelper->canCreateAndAttachPdfConfirmation()) {
                $attachment = $this->generatePdf($item);
                $mail->createAttachment(
                    file_get_contents($attachment),
                    Zend_Mime::TYPE_OCTETSTREAM,
                    Zend_Mime::DISPOSITION_ATTACHMENT,
                    Zend_Mime::ENCODING_BASE64,
                    basename($attachment)
                );
            }

            $mail->send();

            if ($attachment) {
                unlink($attachment);
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }

        return $this;
    }

    protected function generatePdf($items = false)
    {
        $pdf = Mage::getModel("supplierfrontendproductuploader/sales_pdf_purchased")
            ->setOrder($this->getOrder())
            ->setVendor($this->getVendor());

        if ($items) {
            $pdf->setItems($items);
        }

        $pdf = $pdf->getPdf();
        $file = md5(time()).'.pdf';
        $pdf->save($file);
        return $file;
    }
}