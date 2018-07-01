<?php
class Cminds_Supplierfrontendproductuploader_Block_Product_Create extends Cminds_Supplierfrontendproductuploader_Block_Product
{
    private $_selectedCategories;
    protected $stockItem;
    protected $parentItems = false;

    public function _construct()
    {
        parent::_construct();
    }

    public function getProduct()
    {
        if (!$this->_product) {
            $virtualParams = Mage::registry('cminds_virtual_request');
            $configurableParams = Mage::registry('cminds_configurable_request');

            if ($virtualParams && isset($virtualParams['id'])) {
                $id = $virtualParams['id'];
            } elseif ($configurableParams && isset($configurableParams['id'])) {
                $id = $configurableParams['id'];
            } elseif (Mage::registry('supplier_product_id')) {
                $id = Mage::registry('supplier_product_id');
            } else {
                $id = false;
            }

            if ($id) {
                $this->_product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($id);
            } else {
                $this->_product = new Varien_Object();
            }
        }

        return $this->_product;
    }
    
    public function isEditMode()
    {
        $requestParams = Mage::registry('cminds_virtual_request');

        if (Mage::registry('supplier_product_id')) {
            return true;
        }

        if (isset($requestParams['id']) && !isset($requestParams['attribute_set_id'])) {
            return true;
        } elseif (!isset($requestParams['id']) && isset($requestParams['attribute_set_id'])) {
            return false;
        } else {
            if (Mage::registry('is_configurable')) {
                throw new Exception();
            } else {
                return false;
            }
        }
    }

    public function setSelectedCategories($categories) {
        $this->_selectedCategories = $categories;
    }

    public function getCategories() {
        $parent     = Mage::app()->getStore()->getRootCategoryId();
        $category = Mage::getModel('catalog/category');

        if (!$category->checkId($parent)) {
            return new Varien_Data_Collection();
        }

       $storeCategories = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('*');

        return $storeCategories;
    }

    public function getCategoryTree() {
        $store = Mage::app()->getStore()->getStoreId();
        $parentId = 1;

        $tree = Mage::getResourceSingleton('catalog/category_tree')
            ->load();

        $root = $tree->getNodeById($parentId);

        if ($root && $root->getId() == 1) {
            $root->setName(Mage::helper('catalog')->__('Root'));
        }

        $collection = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($store)
            ->addAttributeToSelect('name');
            

        $tree->addCollectionData($collection, true);

        return $this->nodeToArray($root);
    }
    
    public function getAvailableAttributeSets() {
        if($this->isMarketplaceEnabled()) {
            $s = Mage::getModel('eav/entity_attribute_set')->getCollection()->addFieldToFilter('available_for_supplier', 1);
        } else {
            $s = Mage::getModel('eav/entity_attribute_set')->getCollection()->addFieldToFilter('attribute_set_id', Mage::getStoreConfig('supplierfrontendproductuploader_products/supplierfrontendproductuploader_catalog_config/attribute_set'));
        }
        return $s;
    }
    
    public function getProductTypes()
    {
        $types = array(
            array('label' => $this->__('Simple Product'), 'value' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE),
        );

        if (Mage::helper('supplierfrontendproductuploader')->canCreateConfigurable()) {
            $types[] = array(
                'label' => $this->__('Configurable Product'),
                'value' => Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
            );
        }

        if (Mage::helper('supplierfrontendproductuploader')->canCreateVirtualProduct()) {
            $types[] = array(
                'label' => $this->__('Virtual Product'),
                'value' => Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL
            );
        }

        if (Mage::helper('supplierfrontendproductuploader')->canCreateDownloadableProduct()) {
            $types[] = array(
                'label' => $this->__('Downloadable Product'),
                'value' => Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
            );

        }
        return $types;
    }

    public function getProductTypeId()
    {
        $types = $this->getRequest()->getParams();
        return $types['type'];
    }

    public function getProductId() {
        $product = Mage::registry('product_object');
        return $product->getId();
    }

    public function getAttributeSetId() {
        if(!$this->isEditMode()) {
            if(Mage::registry('is_virtual')) {
                $requestParams = Mage::registry('cminds_virtual_request');
                return $requestParams['attribute_set_id'];
            } else {
                $params = Mage::app()->getFrontController()->getRequest()->getParams();

                if(!isset($params['attribute_set_id']) || !$params['attribute_set_id']) {
                    $configAttributeSet = Mage::getStoreConfig('supplierfrontendproductuploader_products/supplierfrontendproductuploader_catalog_config/attribute_set');
                } else {
                    $configAttributeSet = $params['attribute_set_id'];
                }

                return $configAttributeSet;
            }
        } else {
            $product = $this->getProduct();
            return $product->getAttributeSetId();
        }
    }


    public function isMarketplaceEnabled() {
        $cmindsCore = Mage::getModel("cminds/core");

        if($cmindsCore) {
            $cmindsCore->validateModule('Cminds_Marketplace');
        } else {
            throw new Mage_Exception('Cminds Core Module is disabled or removed');
        }
    }

    public function getNodes($categories) {
        $str = '';

        foreach($categories AS $category) {
            $cat = Mage::getModel('catalog/category')->load($category->getEntityId());

            if($cat->getData('available_for_supplier') === 0) continue;

            $str .= $this->_renderCategory($cat);
        }

        return $str;
    }

    protected function _renderCategory($cat) {
        $str = '<li class="level-'.$cat->getLevel().'" style="margin-left:' . (15*$cat->getLevel()).'px">';
        $str .= '<input type="checkbox" name="category[]" value="'.$cat->getId().'" '.(in_array($cat->getId(), $this->_selectedCategories) ? ' checked' : '') .'/> ';
        $str .= $cat->getName();
        $str .= '</li>';
        return $str;
    }

    private function nodeToArray(Varien_Data_Tree_Node $node)
    {
        $result = array();
        $category = Mage::getModel('catalog/category')->load($node->getId());

        if($category->getAvailableForSupplier() == 1) {
            $result['category_id'] = $node->getId();
            $result['parent_id'] = $node->getParentId();
            $result['name'] = $node->getName();
            $result['is_active'] = $node->getIsActive();
            $result['position'] = $node->getPosition();
            $result['level'] = $node->getLevel();
        }

            $result['children'] = array();

            foreach ($node->getChildren() as $child) {
                $result['children'][] = $this->nodeToArray($child);
            }
        return $result;
    }

    public function getAttributes()
    {
        $configAttributeSet = Mage::getStoreConfig('supplierfrontendproductuploader_products/supplierfrontendproductuploader_catalog_config/attribute_set');

        $attributesCollection = Mage::getModel('catalog/product_attribute_api')->items($configAttributeSet);
        
        return $attributesCollection;
    }

    public function listCategory($categories)
    {
        $categoryList = '<ul>';

        foreach ($categories as $category) {
            $categoryList .= '<li>';

            if (isset($category['category_id']) && $category['name'] != '' && $category['category_id'] != 1) {
                $categoryList .= '<input type="checkbox" name="category[]" value="'.$category['category_id'].'"/>' . $category['name'];
            }

            if (count($category['children'])) {
                $this->listCategory($category['children']);
            }
            $categoryList .= '</li>';
        }

        $categoryList .= '</ul>';
        return $categoryList;
    }

    public function getAttributeHtml($attribute, $data = null)
    {
        $frontend = $attribute->getFrontend();

        switch ($frontend->getInputType()) {
            case 'text':
                return $this->_getTextField($attribute, $data);
            break;
            case 'textarea':
                return $this->_getTextareaField($attribute, $data);
            break;
            case 'price':
                return $this->_getPriceField($attribute, $data);
            break;
            case 'date':
                return $this->_getDateField($attribute, $data);
            break;
            case 'select':
                return $this->_getSelectField($attribute, $data);
            break;
            case 'multiselect':
                return $this->_getSelectField($attribute, $data, true);
            break;
            break;
            case 'boolean':
                return $this->_getBooleanField($attribute, $data, true);
            break;
            default:
                return $frontend->getInputType();
                break;
        }
    }

    private function _getTextField($attribute, $data) {
        $value = isset($data[$attribute->getAttributeCode()]) ? $data[$attribute->getAttributeCode()] : null;
        return '<input type="text" value="'.$value.'" name="' . $attribute->getAttributeCode() . '" class="' . $attribute->getFrontend()->getClass() . ' form-control"' . (($this->isCloneWithCode() || $this->createdUsingCode()) ? ' readonly' : '') . '>';
    }

    private function _getTextareaField($attribute, $data)
    {
        $value = isset($data[$attribute->getAttributeCode()]) ? $data[$attribute->getAttributeCode()] : null;
        return '<textarea class="form-control ' . $attribute->getFrontend()->getClass() . '" name="' . $attribute->getAttributeCode() . '">' . $value . '</textarea>';
    }

    private function _getPriceField($attribute, $data) {
        $value = isset($data[$attribute->getAttributeCode()]) ? $data[$attribute->getAttributeCode()] : null;
        return '<input type="text" value="'.$value.'" name="' . $attribute->getAttributeCode() . '" class="form-control ' . $attribute->getFrontend()->getClass() . '">';
    }

    private function _getDateField($attribute, $data) {
        $value = isset($data[$attribute->getAttributeCode()]) ? $data[$attribute->getAttributeCode()] : null;
        return '<input type="text" value="'.$value.'" name="' . $attribute->getAttributeCode() . '" value="'.$value.'" class="datepicker ' . $attribute->getFrontend()->getClass() . '">';
    }

    protected function _getSelectField($attribute, $data, $isMultiple = false) {
        $value = isset($data[$attribute->getAttributeCode()]) ? $data[$attribute->getAttributeCode()] : null;

        if(strstr($value, ', ')) {
            $value = explode(', ', $value);
        }

        $isMultiple = ($isMultiple) ? " multiple" : "";
        $isMultipleStyle = ($isMultiple) ? " height:150px;" : "";
        $name = $attribute->getAttributeCode();
        $name .= ($isMultiple) ? "[]" : "";

        $html = '<select name="' . $name . '" style="'.$isMultipleStyle.'" class="form-control '. $attribute->getFrontend()->getClass() . '"'.$isMultiple.'>';
        $allOptions = $attribute->getSource()->getAllOptions(false);
        $html .= '<option value="">----------------</option>';
        
        foreach($allOptions AS $option) {
            if($option['value'] == '') continue;

            $selected = (($value == $option['value'] ||
                    (is_array($value) && in_array($option['value'], $value))) ||
                ($value == $option['label'] || (is_array($value) && in_array($option['label'], $value))));

            $html .= '<option value="' . $option['value'] . '" '.($selected ? '  selected="selected"' : '').'>'.$option['label'].'</option>';
        }

        $html .= '</select>';
        return $html;
    }

    private function _getBooleanField($attribute, $data)
    {
        $value = isset($data[$attribute->getAttributeCode()]) ? $data[$attribute->getAttributeCode()] : $attribute->getDefaultValue();
        $html = '<select name="' . $attribute->getAttributeCode() . '" class="form-control '. $attribute->getFrontend()->getClass() . '">';
        $allOptions = $attribute->getSource()->getAllOptions();

        foreach ($allOptions as $option) {
            $selected = ($value == $option['label']);
            $html .= '<option value="'.$option['value'].'" '.(($selected) ? ' selected="selected"' : '').'>'.$option['label'].'</option>';
        }

        $html .= '</select>';
        return $html;
    }

    public function canAddSku() {
        $canAddSku = Mage::helper("supplierfrontendproductuploader")->canGenerateSku();

        return ($canAddSku == 2);
    }

    public function canManageStock()
    {
        return Mage::getStoreConfig(
            'supplierfrontendproductuploader_products/supplierfrontendproductuploader_catalog_config/can_manage_stock'
        );
    }

    public function getMaxImagesCount()
    {
        $imagesCount = Mage::getStoreConfig(
            'supplierfrontendproductuploader_products/'
            . 'supplierfrontendproductuploader_catalog_config/'
            . 'images_count'
        );

        return $imagesCount;
    }

    public function getLabel($attribute, $force = '', $loaded = true)
    {
        $helper = Mage::helper('supplierfrontendproductuploader');

        if (!$loaded) {
            $attribute = Mage::getModel('eav/entity_attribute')
                ->loadByCode(4, $attribute);
        }

        if (!is_object($attribute)) {
            return $force;
        }

        $label = Mage::getModel('supplierfrontendproductuploader/labels')
            ->load($attribute->getAttributeCode(), 'attribute_code');

        if($label->getId() == null) {
            if($force != '' && $force != null) {
                return $helper->__($force);
            } else {
                return $attribute->getStoreLabel();
            }
        } else {
            if($label->getLabel() == '' || $label->getLabel() == NULL) {
                if($force != '' && $force != null) {
                    return $helper->__($force);
                } else {
                    return $attribute->getStoreLabel();
                }
            } else {
                return $label->getLabel();
            }
        }
    }

    public function getLinks() {
        $links = Mage::getModel('downloadable/link')
            ->getCollection()
            ->addFieldToFilter(
                'product_id',
                array(
                    'eq' => $this->getProduct()->getId()
                )
            );

        return $links;
    }

    public function isCloneWithCode() {
        return Mage::registry('clone_with_supplier_code');
    }

    public function createdUsingCode() {
        return Mage::registry('created_using_code');
    }

    public function getAttributesBind() {
        $customerId       = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $bindsSupplier    = Mage::getModel('supplierfrontendproductuploader/attributesets')->getCollection()->addFieldToFilter('supplier_id',
            $customerId);
        $attributesSetsId = array();
        foreach ($bindsSupplier as $bind) {
            $attributesSetsId[] = $bind->getAttributeSetId();
        }

        return $attributesSetsId;
    }
    public function canShowWeightField() {
        $typeId = $this->getRequest()->getParams('type');
        return ($typeId['type'] == 'simple' || $typeId['type'] == 'configurable');
    }

    public function isDownloadable() {
        $typeId = $this->getRequest()->getParams('type');
        return ($typeId['type'] == 'downloadable');
    }

    public function getCategoriesListHtml() {
        return $this->getChildHtml(
            'cminds_supplierfrontendproductuploader.product_create_categories',
            false
        );
    }

    public function getFormAction() {
        return Mage::getUrl('supplier/product/save');
    }

    public function canSetMinQty() {
        $dataHelper = Mage::helper("supplierfrontendproductuploader");
        return $dataHelper->canSetMinOrderQty();
    }

    public function getStockItem() {
        if(!$this->stockItem) {
            $this->stockItem = Mage::getModel("cataloginventory/stock_item")
                ->loadByProduct($this->getProduct()->getId());
        }
        return $this->stockItem;
    }

    public function isAssigned() {
        if ( $this->parentItems === false ) {
            $this->parentItems = Mage::getModel( 'catalog/product_type_configurable' )
                                     ->getParentIdsByChild( $this->getProduct()->getId() );
        }

        return count($this->parentItems) > 0;
    }

    public function isConfigurable()
    {
        return $this->getProductTypeId() === "configurable";
    }
}