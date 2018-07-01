<?php
class Cminds_Supplierfrontendproductuploader_Model_Sales_Pdf_Purchased
    extends Mage_Sales_Model_Order_Pdf_Invoice {

    public function getPdf()
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        $order = $this->getOrder();

        if ($this->getOrder()->getStoreId()) {
            Mage::app()->getLocale()->emulate($order->getStoreId());
            Mage::app()->setCurrentStore($order->getStoreId());
        }
        $page = $this->newPage();
        $this->insertLogo($page, $order->getStore());
        $this->insertAddress($page, $order->getStore());
        $this->insertOrder(
            $page,
            $order,
            Mage::getStoreConfigFlag(
                self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                $order->getStoreId()
            )
        );
        $this->_drawHeader($page);
        foreach ($order->getAllItems() as $item) {
            $vendor_id = Mage::helper("supplierfrontendproductuploader")
                ->getProductSupplierId($item->getProduct());

            if ((int) $vendor_id !== (int) $this->getVendor()->getId()) {
                continue;
            }
            $items = $this->getItems();

            if ($items) {
                if ($item->getProductId() !== $items['id']) {
                    continue;
                }
            }

            if ($item->getParentItem()) {
                continue;
            }
            $this->_drawItem($item, $page, $order);
            $page = end($pdf->pages);
        }

        $this->insertTotals($page, $order);

        if ($order->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        $this->_afterGetPdf();

        return $pdf;
    }
    protected function _drawItem(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order)
    {
        $type = $item->getProductType();
        $renderer = $this->_getRenderer($type);
        $item->setQtyInvoiced($item->getQtyOrdered());
        $item->setQty($item->getQtyOrdered());
        $item->setOrderItem($item);


        $this->renderItem($item, $page, $order, $renderer);

        $transportObject = new Varien_Object(array('renderer_type_list' => array()));
        Mage::dispatchEvent('pdf_item_draw_after', array(
            'transport_object' => $transportObject,
            'entity_item'      => $item
        ));

        foreach ($transportObject->getRendererTypeList() as $type) {
            $renderer = $this->_getRenderer($type);
            if ($renderer) {
                $this->renderItem($item, $page, $order, $renderer);
            }
        }

        return $renderer->getPage();
    }

    protected function insertTotals($page, $source)
    {
        $totals = $this->_getTotalsList($source);
        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );
        foreach ($totals as $total) {
            $total->setOrder($source)
                ->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);

                if (!$total) {
                    continue;
                }

                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $lineBlock['lines'][] = array(
                        array(
                            'text'      => $totalData['label'],
                            'feed'      => 475,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
                        array(
                            'text'      => $totalData['amount'],
                            'feed'      => 565,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
                    );
                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }
}