<?php

class Cminds_Supplierfrontendproductuploader_CodesController extends Cminds_Supplierfrontendproductuploader_Controller_Action {

    public function findAction()  {
        $params = $this->getRequest()->getParams();
        Mage::register('requested_phrase', $params['p']);
        Mage::register('requested_name', $params['n']);
        Mage::register('requested_sku', $params['s']);
        $block = $this->getLayout()->createBlock('supplierfrontendproductuploader/product_result');
        $this->getResponse()
            ->setHeader('Content-Type', 'text/html')
            ->setBody($block->toHtml());
    }

    public function cloneAction() {
        $id = $this->_request->getParam('product_id', null);

        if($id == null) {
            throw new Exception('No product id');
        }

        $p = Mage::getModel('catalog/product')->load($id);

        if(!$p->getId()) {
            throw new Exception('No product');
        }
        $attributeSetId = $p->getAttributeSetId();
        Mage::register('supplier_product_id', $id);
        Mage::register('clone_with_supplier_code', true);
        Mage::register('attribute_set_id', $attributeSetId);
        $this->_renderBlocks(true);
    }

    public function editAction() {
        $id = $this->_request->getParam('product_id', null);

        if($id == null) {
            throw new Exception('No product id');
        }

        $p = Mage::getModel('catalog/product')->load($id);

        if($p->getData('creator_id') != $this->_getHelper()->getSupplierId()) {
            throw new Exception('No product');
        }

        if(!$p->getId()) {
            $this->getResponse()->setRedirect(Mage::getUrl('supplier/product/list'));

            throw new Exception('No product');
        }
        $attributeSetId = $p->getAttributeSetId();
        Mage::register('supplier_product_id', $id);
        Mage::register('clone_with_supplier_code', true);
        Mage::register('attribute_set_id', $attributeSetId);
        $this->_renderBlocks(true);
    }

    public function saveAction()
    {
        if ($this->_request->isPost()) {
            $editMode = false;
            $postData = $this->_request->getPost();
            $currentStoreId = Mage::app()->getStore()->getId();
            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
            try {
                if (isset($postData['product_id']) && $postData['product_id'] != NULL) {
                    $product = Mage::getModel('catalog/product')->load($postData['product_id']);
                    if (isset($postData['edited_product_id']) && $postData['edited_product_id'] != NULL) {
                        $editMode = true;
                    }
                    if(!$editMode) {
                        $duplicated = $product->duplicate();
                        $duplicated = $duplicated->load($duplicated->getId());
                        $product = $duplicated;
                    }
                    $product->setStoreId($currentStoreId);
                    $product->setPrice($postData['price']);
                    if(isset($postData['qty'])) {
                        $product->setStockData(array(
                            'is_in_stock' => ($postData['qty'] > 0) ? 1 : 0,
                            'qty' => $postData['qty']
                        ));
                    }
                    if($postData['special_price'] != '' && number_format($postData['special_price']) != 0) {
                        $product->setSpecialPrice($postData['special_price']);

                        if($postData['special_price_from_date'] != NULL && $postData['special_price_from_date'] != '') {
                            $product->setSpecialFromDate($postData['special_price_from_date']);
                            $product->setSpecialFromDateIsFormated(true);
                        }
                        if($postData['special_price_to_date'] != NULL && $postData['special_price_to_date'] != '') {
                            $product->setSpecialToDate($postData['special_price_to_date']);
                            $product->setSpecialToDateIsFormated(true);
                        }
                    }
                    if(!$editMode) {
                        $product->setData('creator_id', $this->_getHelper()->getSupplierId());
                        $product->setCreatedUsingCode(1);
                        $product->setSku($this->_getHelper()->generateSku());
                        $product->setData('frontendproduct_product_status', Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_PENDING);
                    }

                    $product->save();

                    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
                    $product = Mage::getModel('catalog/product')->load($product->getId());
                    if(!$editMode) {
                        $product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
                        $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
                        $product->save();
                    }

                    $product->setStoreId($currentStoreId)->setStockData(array(
                        'is_in_stock' => ($postData['qty'] > 0) ? 1 : 0,
                        'qty' => $postData['qty']
                    ));
                    $product->save();
                    if(!$editMode) {
                        Mage::getSingleton('core/session')->addSuccess($this->__("Product %s was successfully created", $product->getName()));
                    } else {
                        Mage::getSingleton('core/session')->addSuccess($this->__("Product %s was successfully edited", $product->getName()));
                    }
                    $this->getResponse()->setRedirect(Mage::getUrl('supplier/product/list'));
                }
            }
            catch(Exception $ex) {
                Mage::getSingleton('core/session')->addError($ex->getMessage());
                Mage::log($ex->getMessage());
                Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('supplier/product/choosetype/'));
            }
        }
    }
}
