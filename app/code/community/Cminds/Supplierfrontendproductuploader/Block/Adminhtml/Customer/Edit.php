<?php
class Cminds_Supplierfrontendproductuploader_Block_Adminhtml_Customer_Edit extends Mage_Adminhtml_Block_Customer_Edit
{
    public function __construct()
    {
        parent::__construct();

        if ($this->isSupplier()) {
            $this->_updateButton('save', 'label', Mage::helper('customer')->__('Save Supplier'));
            $this->_updateButton('save', 'label', Mage::helper('customer')->__('Save Supplier'));
            $this->_updateButton('delete', 'label', Mage::helper('customer')->__('Delete Supplier'));
            $this->_updateButton('back', 'onclick', 'setLocation(\'' . $_SERVER['HTTP_REFERER'] . '\')');
            $this->_removeButton('order');
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function isSupplier()
    {
        $supplierParam = $this->getRequest()->getParam('supplier', false);
        $isSupplier = Mage::helper('supplierfrontendproductuploader')->isSupplier(
            Mage::registry('current_customer')->getId()
        );

        return $supplierParam || $isSupplier;
    }

    public function getDeleteUrl()
    {
        return $this->getUrl(
            '*/*/delete',
            array(
                $this->_objectId => $this->getRequest()->getParam($this->_objectId),
                'supplier' => $this->isSupplier()
            )
        );
    }

    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
        return $this->getUrl('*/' . $this->_controller . '/save', array('supplier' => $this->isSupplier()));
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_customer')->getId()) {
            return $this->escapeHtml(Mage::registry('current_customer')->getName());
        } else {
            if ($this->isSupplier()) {
                return Mage::helper('customer')->__('New Supplier');
            } else {
                return Mage::helper('customer')->__('New Customer');
            }
        }
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }
}