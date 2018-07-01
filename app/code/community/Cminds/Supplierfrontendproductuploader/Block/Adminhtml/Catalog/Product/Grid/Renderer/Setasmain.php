<?php
class Cminds_Supplierfrontendproductuploader_Block_Adminhtml_Catalog_Product_Grid_Renderer_Setasmain extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
    protected function _getValue(Varien_Object $row)
    {
        $p = Mage::getModel('catalog/product')->load($row->getEntityId());
        $str = '';
        if($p->getSupplierProductCode()) {
            if($p->getData('main_product_by_admin') == 0) {
                $label = 'Set as Main';
                $action = 'setmain';
            } else {
                $label = 'Main unset';
                $action = 'unsetmain';
            }
            $str = '<a href="' .  $this->getUrl("*/*/$action", array('id' => $row->getEntityId())) . '">'.$label.'</a>';
        }
        return $str;
    }
}
