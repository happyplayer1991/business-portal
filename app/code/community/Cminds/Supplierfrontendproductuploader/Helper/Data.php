<?php
class Cminds_Supplierfrontendproductuploader_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function validateModule()
    {
        if (!$this->isEnabled()) {
            Mage::app()->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
            Mage::app()->getResponse()->setHeader('Status', '404 File not found');

            Mage::app()->getResponse()->setRedirect('defaultNoRoute');

            $pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE);
            if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
                Mage::app()->getResponse()->_forward('defaultNoRoute');
            }
            return $this;
        }
    }
    
    public function isEnabled()
    {
        return Mage::getStoreConfig('supplierfrontendproductuploader_catalog/general/module_enabled') == 1;
    }

    public function getSupplierLoginPage()
    {
        $useSeparated = Mage::getStoreConfig('supplierfrontendproductuploader_catalog/login/use_separated_login') == 1;

        if ($useSeparated) {
            return Mage::getUrl('supplier/login/index');
        } else {
            return Mage::helper('customer')->getLoginUrl();
        }
    }

    public function canRegister()
    {
        return Mage::getStoreConfig('supplierfrontendproductuploader_catalog/login/register_enable') == 1;
    }
    
    public function noAccessInformation()
    {
        $loggedUser = Mage::getSingleton('customer/session', array('name' => 'frontend'));

        if ($loggedUser->isLoggedIn()) {
            return !$this->hasAccess();
        }
    }

    public function hasAccess()
    {
        $cmindsCore = Mage::getModel("cminds/core");

        if ($cmindsCore) {
            $validate = $cmindsCore->validateModule('Cminds_Supplierfrontendproductuploader');

            if (!$validate) {
                return false;
            }
        } else {
//            throw new Mage_Exception('Cminds Core Module is disabled or removed');
        }

        return $this->validateLoggedInUser();
    }

    /**
     * Validate if logged in user is supplier.
     *
     * @return bool
     */
    public function validateLoggedInUser()
    {
        $loggedUser = Mage::getSingleton('customer/session', array('name' => 'frontend'));
        if (!$loggedUser->isLoggedIn()) {
            return false;
        }

        $isSupplier = $this->isSupplier($loggedUser->getCustomer());
        if ($this->isMarketplaceEnabled() && $isSupplier) {
            $customerModel = Mage::getModel('customer/customer')->load($loggedUser->getId());
            Mage::helper('marketplace')->checkSupplierProfileFully($customerModel);
        }

        if ($isSupplier && $this->forceTermsToRegister()) {
            $this->validateTermsAgreed($loggedUser->getCustomer());
        }

        return (bool) $isSupplier;
    }

    public function canEditProducts()
    {
        $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

        $editorGroupConfig = Mage::getStoreConfig('supplierfrontendproductuploader_catalog/supplierfrontendproductuploader_supplier_config/editor_group_id');
        $allowedGroups = array();

        if ($editorGroupConfig != null) {
            $allowedGroups = array_merge($allowedGroups, explode(',', $editorGroupConfig));
        }

        return in_array($groupId, $allowedGroups);
    }

    public function getSupplierId()
    {
        if ($this->hasAccess()) {
            $loggedUser = Mage::getSingleton('customer/session', array('name' => 'frontend'));
            $customer = $loggedUser->getCustomer();

            return $customer->getId();
        }

        return false;
    }

    public function generateSku() {
        $sku = (int) Mage::getStoreConfig('supplierfrontendproductuploader_products/supplierfrontendproductuploader_catalog_config/sku_schema');
        
        while (true) {
            $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

            if (!$product) {
                break;
            }
            
            $sku++;
        }
        
        $coreConfig = new Mage_Core_Model_Config();
        $coreConfig->saveConfig('supplierfrontendproductuploader_products/supplierfrontendproductuploader_catalog_config/sku_schema', $sku);
        return $sku;
    }

    public function getImageCacheDir($postData)
    {
        $path = Mage::getBaseDir('upload');
        return $path;
    }

    public function getImageDir($postData)
    {
        $path = Mage::getBaseDir('media').'/catalog/product';
            return $path;
    }

    public function isMarketplaceEnabled()
    {
        return Mage::getConfig()->getModuleConfig('Cminds_Marketplace')->is('active', 'true');
    }

    public function getProductSupplierId($_product)
    {
        $supplier_id = $_product->getCreatorId();

        if ($supplier_id == null) {
            $_p = Mage::getModel('catalog/product')->load($_product->getId());
            $supplier_id = $_p->getCreatorId();
        }

        return $supplier_id;
    }

    public function clearString($string)
    {
        $polishLetters = array(
            "\xc4\x85"=>"a", "\xc4\x84"=>"A",
            "\xc4\x87"=>"c", "\xc4\x86"=>"C",
            "\xc4\x99"=>"e", "\xc4\x98"=>"E",
            "\xc5\x82"=>"l", "\xc5\x81"=>"L",
            "\xc3\xb3"=>"o", "\xc3\x93"=>"O",
            "\xc5\x9b"=>"s", "\xc5\x9a"=>"S",
            "\xc5\xbc"=>"z", "\xc5\xbb"=>"Z",
            "\xc5\xba"=>"z", "\xc5\xb9"=>"Z",
            "\xc5\x84"=>"n", "\xc5\x83"=>"N");
        $tmp = strtr($string, $polishLetters);
        $tmp = preg_replace("/[^a-z0-9-.]/", "-", strtolower($tmp));
        $tmp = iconv('UTF-8', 'ASCII//TRANSLIT', $tmp);
        return $tmp;
    }
    
    public function getSupplierName($supplier_id)
    {
        if (is_numeric($supplier_id)) {
            $customer = Mage::getModel('customer/customer')->load($supplier_id);
        } elseif (is_object($supplier_id) && $supplier_id instanceof Varien_Object) {
            $customer = $supplier_id;
        } else {
            Mage::throwException("Unexpected variable");
        }

        if (!$customer->getId()) {
            return false;
        }

        if ($customer->getSupplierName()) {
            return $customer->getSupplierName();
        } else {
            return $customer->getName();
        }
    }

    public function canCreateVirtualProduct()
    {
        return (int) Mage::getStoreConfig(
            'supplierfrontendproductuploader_products/supplierfrontendproductuploader_catalog_config/can_create_virtual'
        );
    }

    public function canCreateDownloadableProduct()
    {
        return (int) Mage::getStoreConfig(
            'supplierfrontendproductuploader_products/supplierfrontendproductuploader_catalog_config/can_create_downloadable'
        );
    }

    public function canDeleteProducts()
    {
        return (int) Mage::getStoreConfig(
            'supplierfrontendproductuploader_products/supplierfrontendproductuploader_catalog_config/can_delete_products'
        );
    }

    public function canSetMinOrderQty()
    {
        return (int) Mage::getStoreConfig(
            'supplierfrontendproductuploader_products/supplierfrontendproductuploader_catalog_config/can_set_min_ordered_qty'
        );
    }

    public function getAvailableExtensions()
    {
        return explode(
            ',',
            Mage::getStoreConfig(
                'supplierfrontendproductuploader_products/supplierfrontendproductuploader_catalog_downloadable/types'
            )
        );
    }

    /**
     * Validate if customer is supplier.
     *
     * @param Mage_Customer_Model_Customer|int $customer
     *
     * @return bool
     */
    public function isSupplier($customer)
    {
        if (is_numeric($customer)) {
            $customer = Mage::getModel('customer/customer')->load($customer);
        }

        if (!$customer || !$customer->getId()) {
            return false;
        }

        $groupId = $customer->getGroupId();
        $allowedGroups = $this->getAllowedGroups();

        return in_array($groupId, $allowedGroups);
    }

    public function getAllowedGroups()
    {
        $customerGroupConfig = Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/supplierfrontendproductuploader_supplier_config/supplier_group_id'
        );
        $editorGroupConfig = Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/supplierfrontendproductuploader_supplier_config/editor_group_id'
        );

        $allowedGroups = array();

        if ($customerGroupConfig != null) {
            $allowedGroups = array_merge($allowedGroups, explode(',', $customerGroupConfig));
        }
        if ($editorGroupConfig != null) {
            $allowedGroups = array_merge($allowedGroups, explode(',', $editorGroupConfig));
        }

        return array_unique($allowedGroups);
    }

    public function getLoggedSupplier()
    {
        $loggedUser = Mage::getSingleton('customer/session', array('name' => 'frontend'));
        $c = $loggedUser->getCustomer();
        $customer = Mage::getModel('customer/customer')->load($c->getId());

        return $customer;
    }

    public function isSupplierNeedsToBeApproved()
    {
        return Mage::getStoreConfig('supplierfrontendproductuploader_catalog/general/supplier_needs_to_be_approved');
    }

    public function array2Csv($array)
    {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }

    public function prepareCsvHeaders($filename)
    {
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }

    public function isProductCodeEnabled()
    {
        return Mage::getStoreConfig(
            'supplierfrontendproductuploader_multiple_products/general/supplier_product_code_enabled'
        );
    }

    public function sortClonedProductsBy()
    {
        return Mage::getStoreConfig(
            'supplierfrontendproductuploader_multiple_products/general/supplier_product_code_sort_order'
        );
    }

    public function setVisibilities($supplierCode)
    {
        $collection = Mage::getResourceModel('supplierfrontendproductuploader/product_collection')
            ->filterBySupplierCode($supplierCode, true);

        return $collection->setVisibilities();
    }

    public function reApproveNeeded()
    {
        $autoApprovals = Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/general/allow_auto_approval_products'
        );

        $reAppvovalNeeded = Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/general/edited_needs_reapproved'
        );

        if (!$autoApprovals && $reAppvovalNeeded) {
            return true;
        }

        return false;
    }

    public function bindAttributeSets()
    {
        return Mage::getStoreConfig(
            'supplierfrontendproductuploader_products/' .
            'supplierfrontendproductuploader_catalog_config/' .
            'allow_bind_attribute'
        );
    }

    /**
     * Get store config value.
     *
     * @return bool
     */
    public function canCreateConfigurable()
    {
        return Mage::getStoreConfig(
            'supplierfrontendproductuploader_products/' .
            'supplierfrontendproductuploader_catalog_config/' .
            'can_create_configurable'
        );
    }

    public function isOwner($_product, $supplier_id = false)
    {
        if (!$supplier_id) {
            $supplier_id = $this->getSupplierId();
        }

        $owner_id = $this->getSupplierIdByProductId($_product);

        return $supplier_id == $owner_id;
    }

    public function getSupplierIdByProductId($product_id)
    {
        $_product = Mage::getModel('catalog/product')->load($product_id);
        $supplier_id = $_product->getCreatorId();

        return $supplier_id;
    }

    public function csvImportEnabled()
    {
        return Mage::getStoreConfig('supplierfrontendproductuploader_catalog/csv_import/csv_import_enabled');
    }

    public function getMaxImages()
    {
        $imagesCount = Mage::getStoreConfig(
            'supplierfrontendproductuploader_products/' .
            'supplierfrontendproductuploader_catalog_config/' .
            'images_count'
        );

        if($imagesCount === NULL || $imagesCount === '') {
            $imagesCount = 0;
        }

        $maxProducts = Mage::getStoreConfig('supplierfrontendproductuploader_catalog/csv_import/product_limit');

        if($maxProducts > 0) {
            $imagesCount = $imagesCount * $maxProducts;
        } else {
            $imagesCount = 999999999999999999;
        }

        return $imagesCount;
    }

    public function getMatchCategoryCsvAttribute()
    {
        $attribute = 'name';
        $config = Mage::getStoreConfig('supplierfrontendproductuploader_catalog/csv_import/match_catogries_by');

        if ($config == Cminds_Supplierfrontendproductuploader_Model_Config_Source_Import_Categories::BY_IDS) {
            $attribute = 'entity_id';
        }

        return $attribute;
    }

    /**
     * Get default supplier tax id from store configuration.
     *
     * @return int
     */
    public function getDefaultTaxId()
    {
        $taxId = Mage::getStoreConfig(
            'supplierfrontendproductuploader_products/' .
            'supplierfrontendproductuploader_catalog_config/' .
            'tax_class_id'
        );

        return (int) $taxId;
    }

    /**
     * Get can define sku value from store configuration.
     *
     * @return int
     */
    public function getCanDefineSku()
    {
        $canDefineSku = Mage::getStoreConfig(
            'supplierfrontendproductuploader_products/' .
            'supplierfrontendproductuploader_catalog_config/' .
            'can_define_sku'
        );

        return (int) $canDefineSku;
    }

    /**
     * Get allow auto approval configuration.
     *
     * @return int
     */
    public function getAllowAutoApprovalConfig()
    {
        $configValue = Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/' .
            'general/' .
            'allow_auto_approval_products'
        );

        return $configValue;
    }

    /**
     * Get default supplier attribute set id.
     *
     * @return int
     */
    public function getDefaultAttributeSetId()
    {
        $attributeSetId = Mage::getStoreConfig(
            'supplierfrontendproductuploader_products/' .
            'supplierfrontendproductuploader_catalog_config/' .
            'attribute_set'
        );

        return (int) $attributeSetId;
    }

    public function canGenerateSku()
    {
        $configValue = Mage::getStoreConfig(
            'supplierfrontendproductuploader_products/supplierfrontendproductuploader_catalog_config/can_define_sku'
        );

        return $configValue;
    }

    /**
     * Get register page attributes from module configuration.
     *
     * @return array
     */
    public function getRegistrationAttributes()
    {
        $configValue = Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/login/register_attributes'
        );

        return explode(',', $configValue);
    }

    /**
     * Get config value of forcing suppliers to agree terms at registration.
     *
     * @return int
     */
    public function forceTermsToRegister()
    {
        $configValue = Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/login/terms_force'
        );

        return (int)$configValue;
    }

    /**
     * Get terms and conditions page id.
     *
     * @return int
     */
    public function getTermsPageId()
    {
        $configValue = Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/login/terms_page'
        );

        return (int)$configValue;
    }

    /**
     * Get terms and conditions page url.
     *
     * @return string
     */
    public function getTermsPageUrl()
    {
        return Mage::helper('cms/page')->getPageUrl($this->getTermsPageId());
    }

    public function validateTermsAgreed($customer)
    {
        $currentAction  =  Mage::app()->getFrontController()->getRequest()->getActionName();
        $currentController  =  Mage::app()->getFrontController()->getRequest()->getControllerName();
        $currentModule  =  Mage::app()->getFrontController()->getRequest()->getModuleName();

        $termsPage = false;
        if ($currentAction === 'terms'
            && $currentController === 'index'
            && $currentModule === 'supplier'
        ) {
            $termsPage = true;
        }

        if ($termsPage === true) {
            return $this;
        }

        if (!$customer->getTermsConditionsAgreed()) {
            Mage::getSingleton('core/session')->addError(
                $this->__('Terms of Service Update.')
            );
            session_write_close();
            Mage::app()->getFrontController()->getResponse()->setRedirect(
                Mage::getUrl('supplier/index/terms')
            );
        }
    }

    /**
     * Get configuration of admin notification of csv import by supplier.
     *
     * @return int
     */
    public function getNotifyImportAdminConfig()
    {
        $config = Mage::getStoreConfig(
            'supplierfrontendproductuploader_catalog/csv_import/notify_admin_on_uploading_products'
        );

        return (int)$config;
    }
}
