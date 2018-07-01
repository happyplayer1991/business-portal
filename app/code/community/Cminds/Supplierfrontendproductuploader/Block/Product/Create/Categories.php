<?php
class Cminds_Supplierfrontendproductuploader_Block_Product_Create_Categories extends Cminds_Supplierfrontendproductuploader_Block_Product
{
    private $_selectedCategories;
    private $_allowedCategories = false;

    public function setSelectedCategories($categories) {
        $this->_selectedCategories = $categories;
    }

    public function getRootCategory() {
        $parentId     = Mage::app()->getStore()->getRootCategoryId();
        return $parentId;
    }

    public function getCategory($subCatid) {
        return Mage::getModel('catalog/category')->load($subCatid);
    }

    public function getChildCategories($parentId) {
        $parentCat = Mage::getModel('catalog/category')->load($parentId);
        $subcats = $parentCat->getChildren();
        if($subcats != '') {
            return explode(',',$subcats);
        }
        else {
            return false;
        }
    }

    public function listCategory($categories) {
        $string = '';
        foreach($categories AS $category) {
            $cat = Mage::getModel('catalog/category')->load($category);

            if(Mage::getConfig()->getModuleConfig('Cminds_Marketplace')->is('active', 'true')) {
                $allowedCategories = $this->getAllowedCategories();
                if(in_array($cat->getId(), $allowedCategories)) continue;
            }
            
            if($cat->getData('available_for_supplier') == 0) continue;

            $string .= '<li class="category-sublist checkbox-group required">';

            if($cat->getId() && $cat->getName() != '' && $cat->getId() != 1) {
                $string .= '<input type="checkbox" name="category[]" value="'.$cat->getId().'"' . (in_array($cat->getId(), $this->_selectedCategories) ? ' checked' : '') . '/>' . '<a style="text-decoration: none">' .$cat->getName() . '</a>';
            }

            if($this->getChildCategories($cat->getId())) {
                $string .= '<i class="indicator glyphicon glyphicon-plus"></i><ul class="category-sublist">';
                $string .= $this->listCategory($this->getChildCategories($cat->getId()));
                $string .= '</ul>';
            }
            $string .= '</li>';
        }
        return $string;
    }

    public function getProductConfigurable() {
        if(!$this->_product) {
            $requestParams = Mage::registry('cminds_configurable_request');
            if(isset($requestParams['id'])) {
                $this->_product = Mage::getModel('catalog/product')->load($requestParams['id']);
            } else {
                $this->_product = new Varien_Object();
            }
        }

        return $this->_product;
    }

    public function getAllowedCategories() {
        if(!$this->_allowedCategories) {
            $categories = Mage::getModel('marketplace/categories')->getCollection()->addFilter('supplier_id', Mage::helper('marketplace')->getSupplierId());
            $this->_allowedCategories = array();

            foreach($categories AS $category) {
                $this->_allowedCategories[] = $category->getCategoryId();
            }
        }
        return $this->_allowedCategories;
    }
}