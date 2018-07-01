<?php

class Cminds_Supplierfrontendproductuploader_Block_Navbar extends Mage_Core_Block_Template
{
    private $_markedProductIds = null;

    public $nav_items = array(
        'HOME' => array(
            'label' => 'Home',
            'url' => 'supplier',
            'parent' => null,
            'action_names' => array(
                'cminds_supplierfrontendproductuploader_index_index'
            ),
            'sort' => 0
        ),
        'ADD_PRODUCT' => array(
            'label' => 'Add a Product',
            'url' => 'supplier/product/chooseType',
            'parent' => null,
            'action_names' => array(
                'cminds_supplierfrontendproductuploader_product_chooseType',
                ',cminds_supplierfrontendproductuploader_product_create',
            ),
            'sort' => 1
        ),
        'PRODUCT_LIST' => array(
            'label' => 'Product List',
            'url' => 'supplier/product/list',
            'parent' => null,
            'action_names' => array(
                'cminds_supplierfrontendproductuploader_product_list',
                'cminds_supplierfrontendproductuploader_product_edit',
                'cminds_supplierfrontendproductuploader_product_clone',
            ),
            'sort' => 2
        ),
        'SETTINGS' => array(
            'label' => 'Settings',
            'url' => null,
            'parent' => null,
            'action_names' => array(
                'cminds_supplierfrontendproductuploader_settings_notifications'
            ),
            'sort' => 3
        ),
        'NOTIFICATIONS' => array(
            'label' => 'Notifications',
            'url' => 'supplier/settings/notifications',
            'parent' => 'SETTINGS',
            'sort' => 0
        ),

        'REPORTS' => array(
            'label' => 'Reports',
            'url' => null,
            'parent' => null,
            'action_names' => array(
                'cminds_supplierfrontendproductuploader_product_ordered',
            ),
            'sort' => 4
        ),
        'REPORTS_ORDERED_ITEMS' => array(
            'label' => 'Ordered Items',
            'url' => 'supplier/product/ordered',
            'parent' => 'REPORTS',
            'sort' => 0
        ),
        'BACK' => array(
            'label' => 'Back to Home Page',
            'url' => '/',
            'parent' => null,
            'sort' => 5
        )

    );

    public function addCsvTabToMenu()
    {
        $this->nav_items['IMPORT'] = array(
            'label' => 'Import',
            'url' => null,
            'parent' => null,
            'action_names' => array(
                'cminds_supplierfrontendproductuploader_import_products',
            ),
            'sort' => 1.5
        );
        $this->nav_items['IMPORT_PRODUCTS'] = array(
            'label' => 'Products',
            'url' => 'supplier/import/products',
            'parent' => 'IMPORT',
            'sort' => 0
        );
    }
    public function getFirstLevelMenuItems()
    {
        if (Mage::helper('supplierfrontendproductuploader')->csvImportEnabled()) {
            $this->addCsvTabToMenu();
        }

        $arr = array();
        foreach ($this->nav_items as $key => $item) {
            if ($item['parent'] == null) {
                $arr[$key] = $item;
            }
        }
        $this->aasort($arr, 'sort');

        return $arr;
    }

    public function getChildsMenuItems($parent)
    {
        $arr = array();
        foreach ($this->nav_items as $item) {
            if ($item['parent'] == $parent) {
                $arr[] = $item;
            }
        }
        $this->aasort($arr, 'sort');

        return $arr;
    }

    public function addMenuItem($key, $value)
    {
        $this->nav_items[$key] = $value;
    }

    public function getMarkedProductCount()
    {
        if ($this->_markedProductIds == null) {
            $this->_markedProductIds = $this->getMarkedProduct();
        }

        return count($this->_markedProductIds);
    }

    public function hasMarkedProducts()
    {
        return ($this->getMarkedProductCount() > 0);
    }

    public function getMarkedProduct()
    {
        $count = array();

        $collection = Mage::getResourceModel('supplierfrontendproductuploader/product_collection')
            ->filterBySupplier(Mage::helper('supplierfrontendproductuploader')->getSupplierId());

        foreach ($collection as $product) {
            $count[] = $product->getId();
        }

        return $count;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('supplierfrontendproductuploader_nav_load', array(
            'items' => &$this->nav_items,
        ));
        if (!$this->getTemplate()) {
            return '';
        }
        $html = $this->renderView();
        return $html;
    }

    public function aasort(&$array, $key)
    {
        $sorter = array();
        $ret = array();

        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii]=$va[$key];
        }

        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii]=$array[$ii];
        }

        $array=$ret;
    }
}
