<?php

class Cminds_Supplierfrontendproductuploader_ImportController extends Cminds_Supplierfrontendproductuploader_Controller_Action
{
    protected $setMainPhoto = false;
    protected $usedImagesPaths = array();
    protected $storeId = 1;
    protected $websiteId = 1;

    public function preDispatch()
    {
        parent::preDispatch();

        $hasAccess = $this->_getHelper()->hasAccess();
        if (!$hasAccess) {
            $this->getResponse()
                ->setRedirect(
                    $this
                        ->_getHelper('supplierfrontendproductuploader')
                        ->getSupplierLoginPage()
                );
        }
    }

    public function indexAction()
    {
        $this->_renderBlocks();
    }

    public function productsAction()
    {
        if (Mage::getStoreConfig('supplierfrontendproductuploader_catalog/csv_import/csv_import_enabled') == 1) {
            $this->_handleUpload();
            $this->_renderBlocks(false, false, true);
        } else {
            $this->force404();
        }
    }

    public function downloadProductCsvAction()
    {
        $helper = Mage::helper('supplierfrontendproductuploader');
        $avoidAttributes = array(
            'created_at',
            'updated_at',
            'sku_type',
            'price_type',
            'weight_type',
            'shipment_type',
            'links_purchased_separately',
            'links_title',
            'price_view',
            'url_key',
            'url_path',
            'creator_id',
            'tax_class_id',
            'visibility',
            'status',
            'admin_product_note',
            'supplier_actived_product',
            'frontendproduct_product_status',
        );


        $attributeSetId = $this->getRequest()->getParam('attributeSetId');

        $api = Mage::getModel('catalog/product_attribute_api');
        $attributes = $api->items($attributeSetId);
        $attributesCollection = array();
        $attributesCollection[] = 'ID';

        foreach ($attributes as $_attribute) {
            if (in_array($_attribute['code'], $avoidAttributes)) {
                continue;
            }

            if ($_attribute['code'] == 'sku') {
                if ($helper->getCanDefineSku()
                    !== Cminds_Supplierfrontendproductuploader_Model_Config_Source_Availbility_Sku::ALL
                ) {
                    continue;
                }
            }

            if ($_attribute['required'] == 1) {
                $str = trim($_attribute['code']);
                $str .= ($_attribute['required'] == 1) ? ' (*)' : '';

                $attributesCollection[] = $str;
            } else {
                try {
                    $model = Mage::getResourceModel('catalog/eav_attribute')
                        ->setEntityTypeId(Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId())
                        ->load($_attribute['code'], 'attribute_code');

                    if ($model->getData('is_user_defined')
                        && (strstr($model->getData('apply_to'), 'simple') || !$model->getData('apply_to'))
                    ) {
                        $str = trim($_attribute['code']);
                        $attributesCollection[] = $str;
                    }
                } catch (Exception $e) {

                }
            }
        }
        $attributesCollection[] = 'category (*)';
        $attributesCollection[] = 'qty (*)';

        $imagesCountConfig = Mage::getStoreConfig(
            'supplierfrontendproductuploader_products/supplierfrontendproductuploader_catalog_config/images_count'
        );
        for ($i = 0; $i < $imagesCountConfig; $i++) {
            $attributesCollection[] = 'image';
        }

        $stringAttributesCollection = implode(',', $attributesCollection);
        $response = $this->getResponse();
        $response
            ->clearHeaders()
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment' . '; filename=products_schema.csv')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0');
        $response
            ->setBody($stringAttributesCollection);
    }

    private function _handleUpload()
    {
        if (isset($_FILES['file']['name']) && ($_FILES['file']['tmp_name'] != null)) {
            if (!$this->_validateSalt()) {
                return false;
            }
            $this->storeId = Mage::app()->getStore()->getId();
            $importResponse = array();
            $successCount = 0;
            $i = 0;
            $headers = array();
            if (($handle = fopen($_FILES['file']['tmp_name'], "r")) !== false) {
                if (is_int(Mage::getStoreConfig('supplierfrontendproductuploader_catalog/csv_import/product_limit')) &&
                    Mage::getStoreConfig('supplierfrontendproductuploader_catalog/csv_import/product_limit') > 0 &&
                    count(file($_FILES['file']['tmp_name'])) > Mage::getStoreConfig('supplierfrontendproductuploader_catalog/csv_import/product_limit') + 1
                ) {
                    Mage::getSingleton('core/session')->addError(Mage::helper('supplierfrontendproductuploader')->__("Too many products added to import."));
                } else {
                    $this->storeId = Mage::app()->getStore()->getId();
                    $this->websiteId = Mage::app()->getStore()->getWebsiteId();
                    while (($data = fgetcsv($handle)) !== false) {
                        if ($i != 0) {
                            $res = $this->_parseCsv($data, $headers);
                            if ($res['success']) {
                                $successCount++;
                            }
                            $res['line'] = $i;
                            $importResponse[] = $res;
                        } else {
                            $s = $this->validateHeaders($data);
                            if (count($s) > 0) {
                                Mage::getSingleton('core/session')->addError(Mage::helper('supplierfrontendproductuploader')->__("Attributes doesn't match all required attributes. Missing attribute : " . $s[0]));
                                break;
                            }
                            $headers = $data;
                        }
                        $i++;
                    }
                    fclose($handle);
                }
                Mage::app()->setCurrentStore($this->storeId);
            }
            $this->_removeUsedImages();
            Mage::register('import_data', $importResponse);
            $customer = Mage::getModel('customer/customer')->load(Mage::helper('supplierfrontendproductuploader')->getSupplierId());

            $this->_getHelper('supplierfrontendproductuploader/email')->notifyAdminOnUploadingProducts($customer, $successCount);

            Mage::register('upload_done', true);
            $attributeSetId = $this->getRequest()->getParam('attributeSetId');
            Mage::register('attributeSetId', $attributeSetId);
            Mage::app()->setCurrentStore($this->storeId);
        }
    }

    private function _parseCsv($line, $headers)
    {
        /** @var Cminds_Supplierfrontendproductuploader_Helper_Data $helper */
        $helper = Mage::helper('supplierfrontendproductuploader');
        Mage::app()->setCurrentStore($this->storeId);

        try {
            $newAttributes = array();
            $isConfigurable = false;
            $parentProduct = false;
            $this->setMainPhoto = false;
            $productModel = $this->_findProduct($headers, $line);
            $isNew = false;
            if (!$productModel) {
                $isNew = true;
                $productModel = Mage::getModel("catalog/product");
                $productModel->setTypeId('simple');
                $productModel->setWebsiteIDs(array($this->websiteId));

                $attributeSetId = $this->getRequest()->getParam('attributeSetId');
                $productModel->setAttributeSetId($attributeSetId);
                $productModel->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
                $productModel->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
                $productModel->setTaxClassId($helper->getDefaultTaxId());
                $productModel->setData(
                    'frontendproduct_product_status',
                    Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_PENDING
                );
                $productModel->setData('creator_id', Mage::helper('supplierfrontendproductuploader')->getSupplierId());

                if ($helper->getCanDefineSku()
                    !== Cminds_Supplierfrontendproductuploader_Model_Config_Source_Availbility_Sku::ALL
                ) {
                    $productModel->setSku($helper->generateSku());
                }
            }
            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

            $quantity = 0;
            $foundCategories = false;

            foreach ($headers as $i => $header) {
                $missLine = false;
                $attributeCode = trim($this->_prepareHeader($header));

                if (isset($line[$i])) {
                    if (strtolower($attributeCode) == 'category' && $line[$i] != "") {
                        $foundCategories = true;
                        $missLine = true;
                        $categories = $this->_validateCategories($line[$i]);
                        $productModel->setCategoryIds($categories);
                    }

                    if (strtolower($attributeCode) == 'type') {
                        $productModel->setTypeId($line[$i]);

                        if ($line[$i] == 'configurable') {
                            if (!Mage::helper('supplierfrontendproductuploader')->canCreateConfigurable()) {
                                throw new Exception("Admin doesn't allow to create configurable products");
                            }
                            $isConfigurable = true;
                        }
                    }

                    $value = $this->_validateAttributeValue($attributeCode, $line[$i], $isConfigurable);

                    if (strtolower($attributeCode) == 'qty') {
                        $productModel->setStockData(array(
                            'is_in_stock' => ($line[$i] > 0) ? 1 : 0,
                            'qty' => $line[$i]
                        ));

                        $quantity = $line[$i];
                    }

                    if (strtolower($attributeCode) == 'image') {
                        $key = $this->_findImageFileName($line[$i]);
                        $path = $this->_uploadImage($key);

                        if ($path && file_exists($path)) {
                            $attrs = null;

                            if (!$this->setMainPhoto) {
                                $attrs = array('image', 'small_image', 'thumbnail');
                                $this->setMainPhoto = true;
                            }
                            $productModel->addImageToMediaGallery($path, $attrs, false, false);
                        }
                    }
                    $super_attribute = Mage::getModel('eav/entity_attribute')
                        ->loadByCode('catalog_product', $attributeCode);

                    if ($super_attribute->getFrontendInput() == 'select' && $super_attribute->getIsConfigurable()) {
                        $configurableAtt = Mage::getModel('catalog/product_type_configurable_attribute')
                            ->setProductAttribute($super_attribute);

                        $attributesCollection = Mage::getModel('catalog/product_attribute_api')
                            ->items($this->getRequest()->getParam('attributeSetId'));

                        foreach ($attributesCollection as $attributeCollection) {
                            if ($attributeCollection['code'] == $super_attribute->getAttributeCode()) {
                                $newAttributes[] = array(
                                    'id' => $configurableAtt->getId(),
                                    'label' => $configurableAtt->getLabel(),
                                    'position' => $super_attribute->getPosition(),
                                    'values' => array(),
                                    'attribute_id' => $super_attribute->getId(),
                                    'attribute_code' => $super_attribute->getAttributeCode(),
                                    'frontend_label' => $super_attribute->getFrontend()->getLabel(),
                                );
                            }
                        }
                    }

                    if ($isConfigurable && count($newAttributes)) {
                        $productModel->setCanSaveConfigurableAttributes(true);
                        $productModel->setConfigurableAttributesData($newAttributes);
                    }

                    if (strtolower($attributeCode) == 'configurable_sku') {
                        $parentProduct = $this->_getConfigurable($line[$i]);
                    }

                    if (!$missLine) {
                        if ($value) {
                            $productModel->setData($attributeCode, $value);
                        } else {
                            $productModel->setData($attributeCode, $line[$i]);
                        }
                    }
                } else {
                    if ($this->_isRequired($attributeCode)) {
                        throw new Exception($this->__("Value for attribute : %s is not valid", $attributeCode));
                    }
                }
            }

            if (!$foundCategories) {
                throw new Exception($this->__('No categories found'));
            }
            $autoApprove = $helper->getAllowAutoApprovalConfig();
            if ($autoApprove) {
                $p = $productModel;
                $p->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
                $p->setData(
                    'frontendproduct_product_status',
                    Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_APPROVED
                );
            }
            $productModel->save();

            if ($parentProduct && $parentProduct->getId()) {
                if ($parentProduct->getCreatorId() != $helper->getSupplierId()) {
                    throw new Exception($this->__("Configurable products does not exists"));
                }

                $configurableModel = Mage::getModel('supplierfrontendproductuploader/product_configurable');
                $configurableModel->setProduct($parentProduct);
                $configurableProductsData = $configurableModel->getConfigurableProductValues();

                $configurableProductsData[$productModel->getId()][] = array(
                    'is_percent' => '0',
                );

                $parentProduct->setCanSaveConfigurableAttributes(true);
                $parentProduct->setConfigurableProductsData($configurableProductsData);
                $parentProduct->save();

                $p = Mage::getModel('catalog/product')->load($productModel->getId());
                $p->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE)->save();
            }

            if ($isNew) {
                $mediaGallery = $productModel->getMediaGallery();
                if (isset($mediaGallery['images'])) {
                    foreach ($mediaGallery['images'] as $image) {
                        Mage::getSingleton('catalog/product_action')->updateAttributes(
                            array($productModel->getId()),
                            array('image' => $image['file']),
                            0
                        );
                        break;
                    }
                }
            }

            $isInStock = ($quantity > 0) ? 1:0;

            Mage::getModel('cataloginventory/stock_item')
                ->loadByProduct($productModel->getId())
                ->setData('is_in_stock', $isInStock)
                ->save();

            Mage::getSingleton('index/indexer')->getProcessByCode('cataloginventory_stock');

            return array(
                'success' => true,
                'product_id' => $productModel->getId(),
                'sku' => $productModel->getSku(),
                'product_name' => $productModel->getName()
            );
        } catch (Exception $e) {
            Mage::log($line, null, 'marketplace_import_data.log');

            if (method_exists($e, 'getAttributeCode')) {
                return array(
                    'success' => false,
                    'message' => $e->getMessage(),
                    'attribute_code' => $e->getAttributeCode()
                );
            } else {
                return array('success' => false, 'message' => $e->getMessage(), 'attribute_code' => 'unknown');
            }
        }
    }

    protected function _findProduct($headers, $line)
    {
        $foundIdValue = false;
        foreach ($headers as $i => $header) {
            if (strtolower($header) === 'id') {
                $foundIdValue = $line[$i];
                break;
            }
        }

        if (!$foundIdValue || !is_numeric($foundIdValue)) {
            return false;
        }
        $product = Mage::getModel('catalog/product')->load($foundIdValue);

        if (!$product->getId()) {
            throw new Exception($this->__("Product does not exists"));
        }

        if ($product->getCreatorId() !== Mage::helper('supplierfrontendproductuploader')->getSupplierId()) {
            throw new Exception($this->__("Product does not exists"));
        }

        return $product;
    }

    private function _validateCategories($categories_ids)
    {
        $categories = explode(';', $categories_ids);
        $validCategoriesIds = array();

        $isValid = false;
        foreach ($categories as $category) {
            $matchingAttribute = Mage::helper('supplierfrontendproductuploader')->getMatchCategoryCsvAttribute();
            $categoryModel = Mage::getModel('catalog/category')->loadByAttribute($matchingAttribute, $category);
            if ($categoryModel && $categoryModel->getId()) {
                $isValid = true;
                $validCategoriesIds[] = $categoryModel->getId();
            }
        }

        if (!$isValid) {
            throw new Exception($this->__('No valid category'));
        }

        return $validCategoriesIds;
    }

    private function _prepareHeader($header)
    {
        return str_replace(' (*)', '', $header);
    }

    private function _isRequired($attribute_code)
    {
        $attributeModel = Mage::getSingleton("eav/config")->getAttribute('catalog_product', $attribute_code);
        return $attributeModel->getIsRequired();
    }

    private function _validateAttributeValue($attribute_code, $value, $isConfigurable = false)
    {
        if ($isConfigurable) {
            return false;
        }
        $attributeModel = Mage::getSingleton("eav/config")->getAttribute('catalog_product', $attribute_code);

        if ($attributeModel->getIsRequired() && $value == '') {
            throw new Exception("Attribute " . $attribute_code . " is required");
        }

        if ($attributeModel->getFrontendInput() == 'select') {
            if ($value != '') {
                $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeModel->getId());
                $attributeOptions = $attribute->getSource()->getAllOptions(false);
                $availableLabels = array();

                foreach ($attributeOptions as $attributeOption) {
                    $availableLabels[strtolower($attributeOption['label'])] = $attributeOption['value'];
                }

                if (count($availableLabels) > 0) {
                    if (!in_array(strtolower($value), array_keys($availableLabels))) {
                        throw new Exception(
                            "Value of attribute " . $attribute_code . " is not valid . Value : " . $value
                        );
                    }
                }

                return $availableLabels[strtolower($value)];
            }
        }

        if ($attributeModel->getBackendType() == 'decimal') {
            if (!is_numeric($value)) {
                throw new Exception("Value of attribute " . $attribute_code . " is not valid. Should be numeric.");
            }
        }

        return false;
    }

    public function validateHeaders($headers)
    {
        $helper = Mage::helper('supplierfrontendproductuploader');
        $attributes = Mage::getModel('catalog/product_attribute_api')->items($helper->getDefaultAttributeSetId());

        $required = array();

        /**
         * Internal
         */
        $headers[] = 'created_at';
        $headers[] = 'sku';
        $headers[] = 'sku_type';
        $headers[] = 'status';
        $headers[] = 'tax_class_id';
        $headers[] = 'updated_at';
        $headers[] = 'visibility';
        $headers[] = 'shipment_type';
        $headers[] = 'weight_type';
        $headers[] = 'price_type';
        $headers[] = 'price_view';
        $headers[] = 'weight_type';
        $headers[] = 'links_purchased_separately';
        $headers[] = 'links_title';

        foreach ($attributes as $attribute) {
            if ($attribute['required']) {
                $required[] = $attribute['code'];
            }
        }

        foreach ($headers as $k => $header) {
            $headers[$k] = $this->_prepareHeader($header);
        }

        return array_values(array_diff($required, $headers));
    }

    private function downloadImage($url)
    {
        set_time_limit(0);
        $dir = $this->_getHelper('supplierfrontendproductuploader')->getImageCacheDir();
        $lfile = fopen($dir . '/' . basename($url), "w");

        $ch = curl_init($url);

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_BINARYTRANSFER => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FILE => $lfile,
            CURLOPT_TIMEOUT => 50,
            CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)'
        ));

        $results = curl_exec($ch);
        if ($results) {
            return $dir . '/' . basename($url);
        }
        return false;
    }

    private function _uploadImage($key)
    {
        if (count($_FILES['files']['name']) == 0 || $key === false) {
            return false;
        }

        if (isset($this->usedImagesPaths[$key])) {
            return $this->usedImagesPaths[$key];
        }

        $file = array(
            'name' => $_FILES['files']['name'][$key],
            'type' => $_FILES['files']['type'][$key],
            'tmp_name' => $_FILES['files']['tmp_name'][$key],
            'error' => $_FILES['files']['error'][$key],
            'size' => $_FILES['files']['size'][$key]
        );

        $path = $this->_getHelper('supplierfrontendproductuploader')->getImageCacheDir(null);

        try {
            $uploader = new Varien_File_Uploader($file);
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $res = $uploader->save($path, $file['name']);
            $this->usedImagesPaths[$key] = $path . DS . $res['file'];

            return $path . DS . $res['file'];
        } catch (Exception $e) {
            return false;
        }
    }

    private function _removeUsedImages()
    {
        foreach ($this->usedImagesPaths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    private function _findImageFileName($name)
    {
        foreach ($_FILES['files']['name'] as $key => $file) {
            if ($name == $file) {
                return $key;
            }
        }

        return false;
    }

    private function _validateSalt()
    {
        $salt = $this->getRequest()->getPost('salt');
        $sessionSalt = Mage::getSingleton('core/session')->getMarketplaceImportSalt();

        if ($salt != $sessionSalt) {
            Mage::getSingleton('core/session')->setMarketplaceImportSalt($salt);
            return true;
        }
        return false;
    }

    private function _getConfigurable($sku)
    {
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

        if (!$product || !$product->getId()) {
            return false;
        }

        if ($product->isConfigurable()) {
            return $product;
        } else {
            return false;
        }
    }
}
