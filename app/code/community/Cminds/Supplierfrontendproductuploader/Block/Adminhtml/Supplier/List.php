<?php
class Cminds_Supplierfrontendproductuploader_Block_Adminhtml_Supplier_List extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'supplierfrontendproductuploader';
        $this->_controller = 'adminhtml_supplier_list';
        $this->_headerText = $this->__('Supplier');
        $this->_addButtonLabel = $this->__('Add New Supplier');
        parent::__construct();
    }


    public function getCreateUrl()
    {
        return $this->getUrl('*/customer/new', array('supplier' => true));
    }
}
