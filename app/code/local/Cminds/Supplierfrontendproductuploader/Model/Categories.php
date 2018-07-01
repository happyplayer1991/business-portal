<?php
class Cminds_Supplierfrontendproductuploader_Model_Categories extends Mage_Core_Model_Abstract
{
    public $array = array();
    public function __construct() {
        
    }
    public function getAllCategories()
    {
        if ($this->_options === null) {
            $this->_options = array();

            $categories = array();
            $_helper = Mage::helper('catalog/category');
            $_categories = $_helper->getStoreCategories();
            if(count($_categories) > 0) {
                foreach($_categories as $_category) {
                    $sub_categories = array(); 
                    array_push($sub_categories, array('name' => $_category->getName(), 'id' => $_category->getId()));
                    $_category = Mage::getModel('catalog/category')->load($_category->getId());
                    $_subcategories = $_category->getChildrenCategories();
                    foreach($_subcategories as $subcategory) {
                        array_push($sub_categories, array('name' => $subcategory->getName(),'id' => $subcategory->getId()));
                        array_push($this->array, $subcategory->getId());
                    }
                    array_push($categories, $sub_categories);
                }
            }
            
            $this->_options = $categories;
        }
 
        return $this->_options;
    }
    
    public function getCategoriesArray() {
        return $this->array;
    }
}
