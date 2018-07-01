<?php

class Cminds_Supplierfrontendproductuploader_ProductController extends Cminds_Supplierfrontendproductuploader_Controller_Action {
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_getHelper()->validateModule();
        $hasAccess = $this->_getHelper()->hasAccess();

        if (!$hasAccess) {
            $this->getResponse()->setRedirect(Mage::helper('customer')->getLoginUrl());
        }
    }

    public function chooseTypeAction()
    {
        if($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if ($postData['type'] == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
                $this->getResponse()->setRedirect(
                    Mage::getUrl('supplier/product/create', array(
                        'attribute_set_id' => $postData['attribute_set_id'], 'type' => $postData['type']
                        ))
                );
            } elseif ($postData['type'] == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $this->getResponse()->setRedirect(
                    Mage::getUrl('supplier/product/createConfigurable', array(
                        'attribute_set_id' => $postData['attribute_set_id'], 'type' => $postData['type']
                    ))
                );
            } elseif ($postData['type'] == Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL) {
                $this->getResponse()->setRedirect(
                    Mage::getUrl('supplier/product/create', array(
                        'attribute_set_id' => $postData['attribute_set_id'], 'type' => $postData['type']
                    ))
                );
            } elseif ($postData['type'] == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
                $this->getResponse()->setRedirect(
                    Mage::getUrl('supplier/product/create', array(
                        'attribute_set_id' => $postData['attribute_set_id'], 'type' => $postData['type']
                    ))
                );
            } else {
                Mage::getSingleton('core/session')->addError($this->__("Unsupported Product Type"));
                $this->getResponse()->setRedirect(Mage::getUrl('supplier/product/chooseType'));
            }

        }
        $this->_renderBlocks(true);
    }

    public function createAction()
    {
        $params = $this->getRequest()->getParams();

        if (!isset($params['attribute_set_id'])) {
            $this->getResponse()->setRedirect(Mage::getUrl('supplier/product/chooseType'));
            Mage::getSingleton('core/session')->addError($this->__("Missing Attribute Set ID"));
            return;
        }


        $this->_renderBlocks(true, true);
    }

    public function editAction()
    {
        $id = $this->_request->getParam('id', null);

        if ($id == null) {
            throw new Exception('No product id');
        }

        $p = Mage::getModel('catalog/product')->load($id);

        if ($p->getData('creator_id') != $this->_getHelper()->getSupplierId()) {
            throw new Exception('No product');
        }

        Mage::register('supplier_product_id', $id);
        if ($p->getCreatedUsingCode()) {
            Mage::app()->getFrontController()->getResponse()->setRedirect(
                Mage::getUrl('supplier/codes/edit', array('product_id' => $id))
            );
            return;
        }

        $this->_renderBlocks(true, true);
    }

    public function listAction()
    {
        $this->_renderBlocks();
    }

    public function orderedAction()
    {
        $this->_renderBlocks(true);
    }

    public function viewAction()
    {
        $this->_renderBlocks();
    }

    public function cloneAction()
    {
        $id = $this->_request->getParam('id', null);
        Mage::register('cloning', true);
        if ($id == null) {
            throw new Exception('No product id');
        }

        $p = Mage::getModel('catalog/product')->load($id);

        if ($p->getData('creator_id') != $this->_getHelper()->getSupplierId()) {
            throw new Exception('No product');
        }

        Mage::register('supplier_product_id', $id);


        $this->_renderBlocks(true, true);
    }

    public function createConfigurableAction()
    {
        $params = $this->getRequest()->getParams();

        if (!isset($params['attribute_set_id'])) {
            $this->getResponse()->setRedirect(Mage::getUrl('supplier/product/chooseType'));
            Mage::getSingleton('core/session')->addError($this->__("Missing Attribute Set ID"));
            return;
        }

        Mage::register('is_configurable', false);
        Mage::register('cminds_configurable_request', $params);
        $this->_renderBlocks(true, true, false, true);
    }

    public function associatedProductsAction()
    {
        $id = $this->getRequest()->getParam('id');
        $product = Mage::getModel('catalog/product')->load($id);

        if (!$product->getId()) {
            throw new Exception($this->__('Super Product Not Found'));
        }

        Mage::register('product_object', $product);
        $this->_renderBlocks(true);
    }

    public function deleteAssociatedAction()
    {
        $id = $this->_request->getParam('id', null);

        try {
            if ($id == null) {
                throw new Exception('No product id');
            }

            $p = Mage::getModel('catalog/product')->load($id);
            $ids = Mage::getModel('catalog/product_type_configurable')
                ->getParentIdsByChild($p->getId());

            if (count($ids) == 0) {
                throw new Exception($this->__('Product is not associated to any configurable products'));
            }

            if ($p->getData('creator_id') != $this->_getHelper()->getSupplierId()) {
                throw new Exception('No product');
            }

            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

            $p->delete();
            Mage::getSingleton('core/session')->addSuccess(
                $this->__("Product %s was successfully deleted", $p->getName())
            );
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            Mage::logException($e);
        }
        $this->getResponse()->setRedirect(Mage::getUrl('supplier/product/list'));
    }

    public function saveAssociatedProductAction()
    {
        if($this->_request->isPost()) {
            $mode = $this->getRequest()->getPost("mode", "");
            switch($mode) {
                case 'update_qty':
                    $this->updateQty();
                    break;
                case 'unlink':
                    $this->changeAssociatedStatus();
                    break;
                case 'delete':
                    $this->deleteAssociated();
                    break;
                default:
                    $this->processProductSave();
                    break;
            }
        }
    }

    public function editConfigurableAction()
    {
        $params = $this->getRequest()->getParams();
        if (isset($params['id'])) {
            $product = Mage::getModel('catalog/product')->load($params['id']);

            if ($product->getData('creator_id') != $this->_getHelper()->getSupplierId()) {
                throw new Exception('No product');
            }

            Mage::register('cminds_configurable_request', $params);
            Mage::register('supplier_product_id', $params['id']);

        }
        $this->_renderBlocks(true, true, false, true);
    }

    public function saveAction()
    {
        if ($this->_request->isPost()) {
            $autoApprove = Mage::getStoreConfig(
                'supplierfrontendproductuploader_catalog/general/allow_auto_approval_products'
            );

            $postData = $this->_request->getPost();

            $editMode = false;
            $currentStoreId = Mage::app()->getStore()->getId();
            try {
                if (isset($postData['product_id']) && $postData['product_id'] != null) {
                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($currentStoreId)
                        ->load($postData['product_id']);

                    if (!$product->getId()) {
                        throw new Exception('Product does not exists');
                    }

                    if ($product->getData('creator_id') != $this->_getHelper()->getSupplierId()) {
                        throw new Exception('Product does not belongs to this supplier');
                    }
                    $editMode = true;
                } else {
                    $product = Mage::getModel('catalog/product')->setStoreId($currentStoreId);
                    if (!isset($postData['is_cloned'])
                        && $this->_getSupplierHelper()->isProductCodeEnabled()
                        && isset($postData['supplier_product_code'])
                        && $postData['supplier_product_code'] != ''
                    ) {
                        $checkIfAlreadyExist = Mage::getModel('supplierfrontendproductuploader/product')
                            ->checkIfAlreadyExist($postData['supplier_product_code']);
                        if ($checkIfAlreadyExist) {
                            throw new Exception('Product with the same Supplier Product Code already exists');
                        }
                    }
                }

                $productValidator = Mage::getModel('supplierfrontendproductuploader/product');
                $productValidator->setData($postData);
                $productValidator->validate();

                $product->setName($postData['name']);
                $product->setDescription($postData['description']);
                $product->setShortDescription($postData['short_description']);

                if ($postData['special_price'] != '' && number_format($postData['special_price']) != 0) {
                    $product->setSpecialPrice($postData['special_price']);

                    if ($postData['special_price_from_date'] != null && $postData['special_price_from_date'] != '') {
                        $product->setSpecialFromDate($postData['special_price_from_date']);
                        $product->setSpecialFromDateIsFormated(true);
                    }
                    if ($postData['special_price_to_date'] != null && $postData['special_price_to_date'] != '') {
                        $product->setSpecialToDate($postData['special_price_to_date']);
                        $product->setSpecialToDateIsFormated(true);
                    }
                }

                if (!$editMode) {
                    if (!isset($postData['sku']) || $postData['sku'] == null) {
                        $product->setSku($this->_getSupplierHelper()->generateSku());
                    } else {
                        $cProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $postData['sku']);

                        if ($cProduct) {
                            throw new Exception('Product with this SKU already exists in catalog');
                        }

                        $product->setSku($postData['sku']);
                    }
                    if (!isset($postData['attribute_set_id']) || empty($postData['attribute_set_id'])) {
                        throw new Exception('Missing Attribute Set ID');
                    }

                    $typeId = $this->getRequest()->getParams();

                    if ($typeId['type'] == 'simple') {
                        $product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);
                    } elseif ($postData['type'] == 'configurable') {
                        $product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE);
                    } elseif ($postData['type'] == 'virtual') {
                        $product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL);
                    } elseif ($postData['type'] == 'downloadable') {
                        $product->setTypeId(Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE);
                    }
                    $product->setAttributeSetId($postData['attribute_set_id']);
                    $product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
                    $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
                    $product->setTaxClassId(
                        Mage::getStoreConfig(
                            'supplierfrontendproductuploader_products/' .
                            'supplierfrontendproductuploader_catalog_config/tax_class_id'
                        )

                    );
                    $product->setData('admin_product_note', null);
                } else {
                    if (isset($postData['sku']) && $product->getSku() != $postData['sku']) {
                        $cProduct = Mage::getModel('catalog/product')->loadByAttribute(
                            'sku',
                            $postData['sku']
                        );

                        if ($cProduct) {
                            throw new Exception('Product with this SKU already exists in catalog');
                        }

                        $product->setSku($postData['sku']);
                    }
                }

                $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
                
                if (isset($postData['weight'])) {
                    $product->setWeight($postData['weight']);
                }

                $product->setPrice($postData['price']);

                if (isset($postData['manage_stock'])) {
                    $postStockData = array(
                        'manage_stock' => $postData['manage_stock']
                    );

                    if ($postData['manage_stock'] == 1
                        && $product->getTypeId() !== Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
                    ) {
                        $postStockData['is_in_stock'] = $postData['in_stock'];
                        $postStockData['qty'] = $postData['qty'];
                        $postStockData['min_qty'] = $postData['inventory_min_qty'];
                        $postStockData['is_qty_decimal'] = $postData['is_decimal_divided'];
                        $postStockData['backorders'] = $postData['backorders'];
                        $postStockData['notify_stock_qty'] = $postData['notify_stock_qty'];
                        $postStockData['enable_qty_increments'] = $postData['enable_qty_increments'];
                        $postStockData['min_sale_qty'] = isset($postData['inventory_min_sale_qty']) ? $postData['inventory_min_sale_qty'] : 1;
                        $postStockData['max_sale_qty'] = isset($postData['inventory_max_sale_qty']) ? $postData['inventory_max_sale_qty'] : 9999;
                        $postStockData['inventory_use_config_min_qty'] = 0;
                        $postStockData['inventory_use_config_min_sale_qty'] = 0;
                        $postStockData['inventory_use_config_min_sale_qty'] = 0;
                        $postStockData['inventory_use_config_qty_increments'] = 0;
                    } else {
                        $postStockData = array(
                            'use_config_manage_stock' => 0,
                            'is_in_stock' => $postData['in_stock'],
                            'qty' => 9999,
                            'manage_stock' => $postData['manage_stock'],
                            'use_config_notify_stock_qty' => 0,
                            'min_sale_qty' =>
                                isset($postData['inventory_min_sale_qty']) ? $postData['inventory_min_sale_qty'] : 0,
                            'max_sale_qty' =>
                                isset($postData['inventory_max_sale_qty']) ? $postData['inventory_max_sale_qty'] : 0
                        );
                    }

                    $product->setStockData($postStockData);
                }

                if (isset($postData['category'])) {
                    $product->setCategoryIds($postData['category']);
                }

                $product->setWebsiteIDs(array(Mage::app()->getStore()->getWebsiteId()));
                $product->setCreatedAt(strtotime('now'));
                $newAttributes = array();
                if (isset($postData['attributes'])) {
                    foreach ($postData['attributes'] as $attrCode) {
                        $super_attribute = Mage::getModel('eav/entity_attribute')
                            ->loadByCode('catalog_product', $attrCode);
                        $configurableAtt = Mage::getModel('catalog/product_type_configurable_attribute')
                            ->setProductAttribute($super_attribute);

                        $newAttributes[] = array(
                            'id'             => $configurableAtt->getId(),
                            'label'          => $configurableAtt->getLabel(),
                            'position'       => $super_attribute->getPosition(),
                            'values'         => $configurableAtt->getPrices() ? $product->getPrices() : array(),
                            'attribute_id'   => $super_attribute->getId(),
                            'attribute_code' => $super_attribute->getAttributeCode(),
                            'frontend_label' => $super_attribute->getFrontend()->getLabel(),
                        );
                    }
                }

                if (!$editMode && $postData['type'] == 'configurable' && count($newAttributes) == 0) {
                    throw new Exception('Missing configurable attributes data.');
                }

                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

                if (isset($newAttributes) && is_array($newAttributes) && count($newAttributes) > 0) {
                    $product->setCanSaveConfigurableAttributes(true);
                    $product->setConfigurableAttributesData($newAttributes);
                }

                $ret = $this->uploadFile();
                if ($ret != null) {
                    $product->setLinksTitle('Test Product');
                    $product->setLinksPurchasedSeparately('0');
                    $downloadData = array();


                    $downloadData['link'][0] = array(
                        'link_id' => '',
                        'title' => $postData['downloadable_title'],
                        'price' => $product->getPrice(),
                        'number_of_downloads' => $product->getNumberOfDownloads(),
                        'is_shareable' => $product->getLinkId(),
                        'file' => $product->getLinkFile(),
                        'type' => 'file',
                        'link_url' => Mage::getBaseUrl('media') . $ret,
                        'sort_order' => $product->getSortOrder(),
                        'is_delete' => 0
                    );

                    $product->setDownloadableData($downloadData);
                } elseif (isset($postData['file_url']) && $postData['file_url']) {
                    $linkPurchasedItems = Mage::getModel('downloadable/link')->getCollection()
                        ->addFieldToFilter('product_id', $product->getId())->load();

                    if (!$linkPurchasedItems || !$linkPurchasedItems->getItems()) {
                        $downloadData = array();

                        $downloadData['link'][0] = array(
                            'link_id' => '',
                            'title' => $postData['downloadable_title'],
                            'price' => $product->getPrice(),
                            'number_of_downloads' => $product->getNumberOfDownloads(),
                            'is_shareable' => $product->getLinkId(),
                            'file' => $product->getLinkFile(),
                            'type' => 'url',
                            'link_url' => $postData['file_url'],
                            'sort_order' => $product->getSortOrder(),
                            'is_delete' => 0
                        );

                        $product->setDownloadableData($downloadData);
                    } else {
                        $currentPurchasedItemsT = $linkPurchasedItems->getItems();
                        foreach ($currentPurchasedItemsT as $c) {
                            $c->setLinkUrl($postData['file_url']);
                            $c->save();
                        }
                    }
                }

                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

                $product->save();

                unset(
                    $postData['name'],
                    $postData['description'],
                    $postData['short_description'],
                    $postData['sku'],
                    $postData['weight'],
                    $postData['price'],
                    $postData['qty'],
                    $postData['category']
                );

                $product = Mage::getModel('catalog/product')->load($product->getId())->setStoreId($currentStoreId);

                if (!isset($postData['image'])) {
                    $postData['image'] = array();
                }

                $existingImages = array();

                if ($product->getId() && $editMode) {
                    $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
                    $mediaGalleryAttribute = Mage::getModel('catalog/resource_eav_attribute')
                        ->loadByCode($product->getEntityTypeId(), 'media_gallery');
                    $gallery = $product->getMediaGalleryImages();

                    foreach ($gallery as $image) {
                        if (!in_array($image->getFile(), $postData['image'])) {
                            $mediaApi->remove($product->getId(), $image->getFile());
                            $mediaGalleryAttribute->getBackend()->removeImage($product, $image->getFile());

                        } else {
                            $existingImages[] = $image->getFile();

                            if ($postData['main_photo'] == $image->getFile()) {
                                
                                Mage::getSingleton('catalog/product_action')->updateAttributes(
                                    array($product->getId()),
                                    array(
                                        'image'=> $image->getFile(),
                                        'small_image' => $image->getFile(),
                                        'thumbnail' => $image->getFile()
                                    ),
                                    $currentStoreId
                                );
                                
                                /**
                                 * set base image
                                 */
                                
                                /*
                                $mediaAttribute = array (
                                    'image',
                                    'thumbnail',
                                    'small_image'                  
                                );
                                $filepath_to_image = $image->getFile();
                                $product->addImageToMediaGallery($filepath_to_image, $mediaAttribute, true, false);
                                */
                            }
                        }
                    }
                }

                $onlyOneImage = false;

                if (count($postData['image']) == 1) {
                    $onlyOneImage = true;
                }

                foreach ($postData['image'] as $image) {
                    if ($image != '' && $image && $image != null && !in_array($image, $existingImages)) {
                        $attrs = null;

                        if ($image == $postData['main_photo'] || $onlyOneImage) {
                            $attrs = array('image','small_image','thumbnail');
                        }

                        if (isset($postData['is_cloned'])) {
                            $product->addImageToMediaGallery(
                                $this->_getSupplierHelper()->getImageDir($postData) . $image,
                                $attrs,
                                false,
                                false
                            );
                        } else {
                            $product->addImageToMediaGallery(
                                $this->_getSupplierHelper()->getImageCacheDir($postData) . $image,
                                $attrs,
                                true,
                                false
                            );
                        }
                    }
                }

                $ommitIndex = array(
                    'submit',
                    'main_photo',
                    'image',
                    'product_id',
                    'special_price',
                    'special_price_to_date',
                    'special_price_from_date',
                    'notify_admin_about_change'
                );

                foreach ($postData as $index => $value) {
                    if (!in_array($index, $ommitIndex)) { //&& $value != '') {
                        $product->setData($index, $value);
                    }
                }

                if ($editMode) {
                    $product->setSmallImage($postData['main_photo']);
                    $product->setImage($postData['main_photo']);
                    $product->setThumbnail($postData['main_photo']);
                    $this->updateStatus($product, true);
                } else {
                    $product->setData(
                        'frontendproduct_product_status',
                        Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_PENDING
                    );
                    $product->setData('creator_id', $this->_getHelper()->getSupplierId());
                    if ($autoApprove) {
                        $p = $product;
                        $p->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
                        $p->setData(
                            'frontendproduct_product_status',
                            Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_APPROVED
                        );
                        $p->setStockData(array(
                            'is_in_stock' => $postData['in_stock']
                        ));
                    }
                }

                $product->save();
                Mage::app()->setCurrentStore($currentStoreId);
                if (!$editMode) {
                    Mage::log($this->_getHelper()->__(
                        'Supplier '. $this->_getHelper()->getSupplierId() .' created product : ' . $product->getId()
                    ));
                    $this->_getHelper('supplierfrontendproductuploader/email')->notifyOnSupplierAddNew($product);
                } else {
                    if (isset($postData['notify_admin_about_change']) && $postData['notify_admin_about_change'] == 1) {
                        $this->_getHelper('supplierfrontendproductuploader/email')
                            ->notifyAdminOnProductChange($product);
                    }
                }

                Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('supplier/product/list'));
            } catch (Exception $ex) {
                Mage::getSingleton('core/session')->addError($ex->getMessage());
                Mage::log($ex->getMessage());
                Mage::getSingleton("supplierfrontendproductuploader/session")->setProductData($postData);

                if ($postData['type'] == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                    $redirectAction = 'configurable';
                } else {
                    $redirectAction = '';
                }
                if ($editMode) {
                    Mage::app()->getFrontController()->getResponse()->setRedirect(
                        Mage::getUrl(
                            'supplier/product/edit' . $redirectAction . '/',
                            array('id' =>  $postData['product_id'], 'type' => $postData['type'])
                        )
                    );
                } else {
                    Mage::app()->getFrontController()->getResponse()->setRedirect(
                        Mage::getUrl(
                            'supplier/product/create' . $redirectAction . '/',
                            array('attribute_set_id' =>  $postData['attribute_set_id'], 'type' => $postData['type'])
                        )
                    );
                }
            }
        }
    }

    public function uploadAction()
    {
        $ret = array();
        if (isset($_FILES['file_upload']['name']) && ($_FILES['file_upload']['tmp_name'] != null)) {
            $uploader = new Varien_File_Uploader('file_upload');
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);

            $path = $this->_getHelper()->getImageCacheDir(null);

            try {
                $uploader->save($path, $_FILES['file_upload']['name']);

                $image = new Varien_Image($path . $uploader->getUploadedFileName());
                $image->resize(171);
                $image->save($path . DS . 'resized/' . $uploader->getUploadedFileName());

                $imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)
                    . 'upload/resized'
                    . $uploader->getUploadedFileName();

                $ret = array('success' => true, 'url' => $imageUrl, 'name' => $uploader->getUploadedFileName());
            } catch (Exception $e) {
                $ret = array('success' => false, 'message' => $e->getMessage());
            }
        }
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json');
        $response->setBody(Mage::helper('core')->jsonEncode($ret));

        return $this;
    }

    public function uploadFile() {
        if(isset($_FILES['downloadable_upload']['name']) && ($_FILES['downloadable_upload']['tmp_name'] != NULL))
        {

            try {
                $uploader = new Varien_File_Uploader('downloadable_upload');
                $uploader->setAllowedExtensions($this->_getHelper()->getAvailableExtensions());
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);

                $path = Mage::getBaseDir('media');
                $uploader->save($path , $_FILES['downloadable_upload']['name']);
                $file = $_FILES['downloadable_upload']['name'];
                $fileUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $uploader->getUploadedFileName() ;
                $ret = array('success' => true, 'url' => $fileUrl, 'name' => $uploader->getUploadedFileName());
                return $ret['name'];
            } catch(Exception $e) {
                Mage::throwException($e->getMessage());
            }
        }
    }

    public function activeAction() {
        $id = $this->_request->getParam('id', null);

        if($id == null) {
            throw new Exception('No product id');
        }

        $p = Mage::getModel('catalog/product')->load($id);

        if($p->getData('creator_id') != $this->_getHelper()->getSupplierId()) {
            throw new Exception('No product');
        }

        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        if(!in_array($p->getData('frontendproduct_product_status'), array(Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_PENDING, Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_DISAPPROVED))) {
            $p->setSupplierActivedProduct(1);
            $p->getResource()->saveAttribute($p, 'supplier_actived_product');
            $p->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
            $p->setFrontendproductProductStatus(Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_APPROVED);
            $p->getResource()->saveAttribute($p, 'frontendproduct_product_status');

            $p->setStockData(array(
                'is_in_stock' => 1
            ));

            $p->save();
        }

        Mage::dispatchEvent('supplierfrontendproductuploader_catalog_product_supplier_active', array('product' => $p));
        $this->getResponse()->setRedirect(Mage::getBaseUrl().'supplierfrontendproductuploader/product/list/');
    }

    public function deactiveAction() {
        $id = $this->_request->getParam('id', null);

        if($id == null) {
            throw new Exception('No product id');
        }

        $p = Mage::getModel('catalog/product')->load($id);

        if($p->getData('creator_id') != $this->_getHelper()->getSupplierId()) {
            throw new Exception('No product');
        }

        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        if(!in_array($p->getData('frontendproduct_product_status'), array(Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_PENDING, Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_DISAPPROVED))) {
            $p->setSupplierActivedProduct(0);
            $p->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
            $p->getResource()->saveAttribute($p, 'supplier_actived_product');

            $p->setFrontendproductProductStatus(Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_NONACTIVE);
            $p->getResource()->saveAttribute($p, 'frontendproduct_product_status');

            $p->setStockData(array(
                'is_in_stock' => 0
            ));

            $p->save();
        }

        Mage::dispatchEvent('supplierfrontendproductuploader_catalog_product_supplier_deactive', array('product' => $p));
        $this->getResponse()->setRedirect(Mage::getBaseUrl().'supplierfrontendproductuploader/product/list/');
    }

    public function exportCsvAction() {
        $supplier_id = Mage::helper('supplierfrontendproductuploader')->getSupplierId();
        $status = $this->getRequest()->getParam('status');
        $name = $this->getRequest()->getParam('name', null);

        $collection = Mage::getResourceModel('supplierfrontendproductuploader/product_collection')
            ->filterBySupplier($supplier_id)
            ->filterByFrontendproductStatus($status)
            ->filterByName($name);

        $productCsv = array();

        foreach($collection AS $item) {
            $product = Mage::getModel('catalog/product')->load($item->getId());
            $stocklevel = (int)Mage::getModel('cataloginventory/stock_item')
                ->loadByProduct($product)->getQty();
            $productCsv[] = array(
                'SKU' => $product->getSku(),
                'Id' => $product->getId(),
                'Name' => $product->getName(),
                'Quantity' => $stocklevel,
                'Price' => $product->getPrice(),
                'creator_id' => $product->getCreatorId(),
                'special_price' => $product->getSpecialPrice(),
                'special_from_date' => $product->getSpecialFromDate(),
                'special_to_date' => $product->getSpecialToDate(),
            );
        }

        Mage::helper('supplierfrontendproductuploader')->prepareCsvHeaders("product_export_" . date("Y-m-d") . ".csv");
        $productCsv = Mage::helper('supplierfrontendproductuploader')->array2Csv($productCsv);
        $this->getResponse()->clearHeaders()->setHeader('Content-type','text/csv',true);
        $this->getResponse()->setBody($productCsv);
    }

    public function deleteAction()
    {
        if (!Mage::helper('supplierfrontendproductuploader')->canDeleteProducts()) {
            $this->norouteAction();
            return;
        }

        $id = $this->_request->getParam('id', null);

        if ($id == null) {
            $this->norouteAction();
            return;
        }

        $p = Mage::getModel('catalog/product')->load($id);

        if ($p->getData('creator_id') != $this->_getHelper()->getSupplierId()) {
            $this->norouteAction();
            return;
        }

        $currentStore = Mage::app()->getStore()->getId();
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        $result = Mage::dispatchEvent(
            'supplier_product_delete_before',
            array(
                'product' => $p
            )
        );

        if (!$result) {
            $this->getResponse()->setRedirect(Mage::getBaseUrl().'supplier/product/list/');
            return;
        }

        $p->delete();

        Mage::dispatchEvent(
            'supplier_product_delete_after',
            array(
                'product' => $p
            )
        );

        Mage::app()->setCurrentStore($currentStore);
        $this->getResponse()->setRedirect(Mage::getBaseUrl().'supplier/product/list/');
    }

    public function updateStatus(&$product, $edited = false)
    {
        $helper = Mage::helper('supplierfrontendproductuploader');

        if ($edited && $helper->reApproveNeeded()) {
            $product->setData(
                'frontendproduct_product_status',
                Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_PENDING
            );
            $product->setVisibility(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
            );
        }
    }

    protected function updateQty()
    {
        $postData = $this->getRequest()->getPost();
        $qtyData = $postData['qty'];

        if ($qtyData === null) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('supplierfrontendproductuploader')->__('Please select items.')
            );
            return $this->getResponse()->setRedirect($this->getAssociatedProductUrl());
        }

        try {
            foreach ($qtyData as $product_id => $qty) {
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id);
                if ($stockItem->getId() > 0 && $stockItem->getManageStock()) {
                    $stockItem->setQty($qty);
                    $stockItem->setIsInStock((int)($qty > 0));
                    $stockItem->save();
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            Mage::logException($e);
        }
        $this->getResponse()->setRedirect($this->getAssociatedProductUrl());
    }

    protected function getAssociatedProductUrl()
    {
        return Mage::getUrl(
            'supplier/product/associatedProducts',
            array('id' => $this->getRequest()->getPost('super_product_id'))
        );
    }

    protected function changeAssociatedStatus()
    {
        $product_ids = $this->getRequest()->getPost('selectedProduct', null);

        if ($product_ids === null) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('supplierfrontendproductuploader')->__('Please select items.')
            );
            return $this->getResponse()->setRedirect($this->getAssociatedProductUrl());
        }

        try {
            foreach ($product_ids as $product_id) {
                $this->unlinkProduct($product_id);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        $this->getResponse()->setRedirect($this->getAssociatedProductUrl());
    }

    protected function unlinkProduct($id)
    {
        $configurableId = $this->_request->getPost('super_product_id', null);

        if ($id == null) {
            throw new Exception($this->__('No product id'));
        }

        if ($configurableId == null) {
            throw new Exception($this->__('No product id'));
        }

        $configurableProduct = Mage::getModel('catalog/product')->load($configurableId);
        $product = Mage::getModel('catalog/product')->load($id);

        $configurableModel = Mage::getModel('supplierfrontendproductuploader/product_configurable');
        $configurableModel->setProduct($configurableProduct);
        $configurableProductsData = $configurableModel->getConfigurableProductValues();

        $additionalPrice = 0;

        if ($this->_request->getPost('status') == 'true') {
            $configurableProductsData[$product->getId()][] = array(
                'is_percent' => '0',
            );

            $configurableProduct->setCanSaveConfigurableAttributes(true);
            $product->setPrice($configurableProduct->getPrice() + $additionalPrice);
        } else {
            if (isset($configurableProductsData[$product->getId()])) {
                unset($configurableProductsData[$product->getId()]);
            }
        }

        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        $configurableProduct->setConfigurableProductsData($configurableProductsData);
        $configurableProduct->save();
    }

    protected function processProductSave()
    {
        $post = $this->_request->getPost();
        $dataHelper = Mage::helper("supplierfrontendproductuploader");
        $currentStoreId = Mage::app()->getStore()->getId();
        try {
            $transaction         = Mage::getModel('core/resource_transaction');
            $configurableProduct = Mage::getModel('catalog/product')
                ->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)
                ->load($post['super_product_id']);

            if (!$configurableProduct->isConfigurable()) {
                $this->_redirect('*/*/');
                return;
            }
            $transaction->addObject($configurableProduct);
            if (!isset($post['product_id']) || $post['product_id'] == 0) {
                $product = Mage::getModel('catalog/product')
                    ->setStoreId(0)
                    ->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
                    ->setAttributeSetId($configurableProduct->getAttributeSetId());

                $transaction->addObject($product);
                $inStock = false;

                if (isset($post['in_stock'])) {
                    $inStock = $post['in_stock'];
                } else {
                    $inStock = $post['new_qty'] > 0;
                }
                $product->setStockData(array(
                    'is_in_stock' => $inStock,
                    'qty' => $post['new_qty']
                ));

                foreach ($product->getTypeInstance()->getEditableAttributes() as $attribute) {
                    if ($attribute->getIsUnique()
                        || $attribute->getAttributeCode() == 'url_key'
                        || $attribute->getFrontend()->getInputType() == 'gallery'
                        || $attribute->getFrontend()->getInputType() == 'media_image'
                        || !$attribute->getIsVisible()
                    ) {
                        continue;
                    }

                    $product->setData(
                        $attribute->getAttributeCode(),
                        $configurableProduct->getData($attribute->getAttributeCode())
                    );
                }

                $product->addData($this->getRequest()->getPost());
                $product->setWebsiteIds($configurableProduct->getWebsiteIds());

                $result['attributes'] = array();

                foreach ($configurableProduct->getTypeInstance()->getConfigurableAttributes() as $attribute) {
                    $value = $product->getAttributeText($attribute->getProductAttribute()->getAttributeCode());
                    $result['attributes'][] = array(
                        'label' => $value,
                        'value_index' => $product->getData($attribute->getProductAttribute()->getAttributeCode()),
                        'attribute_id' => $attribute->getProductAttribute()->getId()
                    );
                }
                $values = array();
                foreach ($post['options'] as $index => $option) {
                    $values[] = $post[$index];
                }

                $productUrlHelper = Mage::getModel('catalog/product_url');
                $id = Mage::getModel('catalog/product')
                    ->getResource()
                    ->getIdBySku(
                        $configurableProduct->getSku() . '-' . $productUrlHelper->formatUrlKey(implode('-', $values))
                    );

                if ($id) {
                    throw new Exception($dataHelper->__('Associated Product with same configuration already exists.'));
                }

                foreach ($post as $name => $value) {
                    $product->setData($name, $value);
                }

                $product->setName($post['name']);

                if (isset($post['sku']) && $post['sku'] && $dataHelper->canGenerateSku()) {
                    $product->setSku($post['sku']);
                } else {
                    $generatedValues = Mage::getModel('catalog/product_url')
                        ->formatUrlKey(implode('-', $values));

                    $product->setSku(
                        $configurableProduct->getSku() . '-' . $generatedValues
                    );
                }

                $product->validate();
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
                $product->save();
            } else {
                $product = Mage::getModel('catalog/product')->load($post['product_id']);

                if (!$product->getId()) {
                    throw new Exception($this->__("Product doesn't not exists"));
                }

                if (!Mage::helper('supplierfrontendproductuploader')->isOwner($product->getId())) {
                    throw new Exception($this->__("Product doesn't belongs to you"));
                }
            }
            $configurableModel = Mage::getModel('supplierfrontendproductuploader/product_configurable');
            $configurableModel->setProduct($configurableProduct);
            $configurableProductsData = $configurableModel->getConfigurableProductValues();

            $additionalPrice = 0;

            if (!isset($post['product_id']) || $post['product_id'] == 0) {
                if (!$this->validateValues($configurableProductsData, $post)) {
                    throw new Exception($dataHelper->__("Simple product with this options is already created."));
                }

                $configurableAttributesData = $configurableProduct
                    ->getTypeInstance()
                    ->getConfigurableAttributesAsArray();
                $i = 0;
                foreach ($post['options'] as $index => $option) {
                    if (!isset($post[$index]) || $post[$index] == '') {
                        continue;
                    }

                    $productData = array(
                        'attribute_id' => $option['attribute_id'],
                        'value_index' => (int)$post[$index],
                        'is_percent' => '0',
                        'pricing_value' => $option['price']
                    );

                    $configurableProductsData[$product->getId()][] = $productData;
                    $configurableAttributesData[$i]['values'][] = $productData;
                    $additionalPrice = $additionalPrice + $option['price'];
                    $i++;
                }
            } else {
                $superAttributes = $configurableModel->getSuperAttributes();
//                $i = 0;
                foreach ($superAttributes as $attribute) {
                    $simpleProductData = $product->getData($attribute['attribute_code']);
                    $configurableProductsData[$product->getId()][] = array(
                        'attribute_id' => $attribute['attribute_id'],
                        'value_index' => $simpleProductData,
                        'is_percent' => '0',
                        'pricing_value' => $product->getPrice()
                    );
//                    $configurableAttributesData[$i]['values'][] = $configurableProductsData;
//                    $i++;
                }

            }

            $configurableProduct->setCanSaveConfigurableAttributes(true);
            $product->setPrice($configurableProduct->getPrice() + $additionalPrice);

            $configurableProduct->setConfigurableProductsData($configurableProductsData);
            if (isset($configurableAttributesData)) {
                $configurableProduct->setConfigurableAttributesData($configurableAttributesData);
            }
            $configurableProduct->setCanSaveConfigurableAttributes(true);
            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
            $configurableProduct->save();

            $p = Mage::getModel('catalog/product')->load($product->getId());
            $p->setPrice($configurableProduct->getPrice() + $additionalPrice);

            $autoApprove = Mage::getStoreConfig(
                'supplierfrontendproductuploader_catalog/general/allow_auto_approval_products'
            );
            if ($autoApprove) {
                $p->setData(
                    'frontendproduct_product_status',
                    Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_APPROVED
                );
            } else {
                $p->setData(
                    'frontendproduct_product_status',
                    Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_PENDING
                );
                $p->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
            }
            $p->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)->save();
            Mage::app()->setCurrentStore($currentStoreId);
            Mage::app()->getFrontController()->getResponse()->setRedirect(
                Mage::getUrl('supplier/product/associatedProducts', array('id' => $post['super_product_id']))
            );
        } catch (Exception $e) {
            if (!isset($post['product_id']) || $post['product_id'] == 0) {
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
                $product->delete();
            }
            Mage::getSingleton('core/session')->addError($e->getMessage());
            Mage::logException($e);
            $this->getResponse()->setRedirect($this->getAssociatedProductUrl());
        }
    }



    protected function deleteAssociated()
    {
        $postSelectedData = $this->getRequest()->getPost('selectedProduct');

        if ($postSelectedData === null) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('supplierfrontendproductuploader')->__('Please select items.')
            );
            return $this->getResponse()->setRedirect($this->getAssociatedProductUrl());
        }

        try {
            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
            $i = 0;

            foreach ($postSelectedData as $product_id) {
                $this->removeProduct($product_id);
                $i++;
            }

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('supplierfrontendproductuploader')->__("Successfully deleted %s products.", $i)
            );
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            Mage::logException($e);
        }

        $this->getResponse()->setRedirect($this->getAssociatedProductUrl());
    }

    /**
     * Remove associated product.
     *
     * @param $id
     * @throws Exception
     *
     * @return $this
     */
    protected function removeProduct($id)
    {
        $helper = Mage::helper('supplierfrontendproductuploader');
        if($id == null) {
            throw new Exception(
                $helper->__('No product id selected.')
            );
        }

        $product = Mage::getModel('catalog/product')->load($id);
        $ids = Mage::getModel('catalog/product_type_configurable')
            ->getParentIdsByChild( $product->getId() );

        if(count($ids) == 0) {
            throw new Exception(
                $helper->__('Product is not associated to any configurable products.')
            );
        }

        $supplierId = $this->_getHelper()->getSupplierId();
        $creatorId = $product->getData('creator_id');
        if($creatorId != $supplierId) {
            throw new Exception(
                $helper->__('Product not belong to supplier.')
            );
        }

        $product->delete();

        return $this;
    }

    protected function validateValues($configurable_values, $values_selected) {
        $isValid = true;

        foreach($values_selected['options'] AS $index => $value) {
            foreach($configurable_values as $product) {
                $matchedProductValues = 0;
                $countValues = count($product);

                foreach($product AS $confValue) {
                    if($confValue['attribute_id'] == $value['attribute_id'] &&
                       $confValue['value_index'] == $values_selected[$index]) {
                        $matchedProductValues++;
                    }
                }

                if($matchedProductValues >= $countValues) {
                    $isValid = false;
                }
            }
        }

        return $isValid;
    }
}
