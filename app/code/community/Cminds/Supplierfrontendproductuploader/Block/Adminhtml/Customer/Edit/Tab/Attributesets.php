<?php

/**
 * Class Cminds_Supplierfrontendproductuploader_Block_Adminhtml_Customer_Edit_Tab_Attributesets
 */
class Cminds_Supplierfrontendproductuploader_Block_Adminhtml_Customer_Edit_Tab_Attributesets
    extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selectedAttributes;
    private $_selectedAllAttributes;

    public function __construct()
    {
        parent::__construct();
        $this->setId('assigned_attribute_sets');
        $this->setDefaultSort('attribute_set_name');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return Mage::registry('current_customer');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Attribute sets');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Attribute sets');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        if ($this->getCustomer()->getId()
            && Mage::helper('supplierfrontendproductuploader')->isSupplier($this->getCustomer()->getId())
            && Mage::helper('supplierfrontendproductuploader')->bindAttributeSets()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/suppliers/attributeSets', array('_current' => true));
    }

    /**
     * @param $row
     * @return bool
     */
    public function getRowUrl($row)
    {
        return false;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('eav/entity_attribute_set')
            ->getCollection()
            ->addFieldToFilter('entity_type_id', 4);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'field_name' => 'attributes_ids[]',
            'values'    => $this->_getSelectedAttributes(),
            'align'     => 'center',
            'index'     => 'attribute_set_id'
        ));
        $this->addColumn('all_attributes', array(
            'type'      => 'checkbox',
            'field_name' => 'all_attributes_ids[]',
            'values'    => $this->_getSelectedAllAttributes(),
            'align'     => 'center',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'index'     => 'attribute_set_id'
        ));

        $this->addColumn('attribute_set_name', array(
            'header'    => Mage::helper('catalog')->__('Attribute sets Name'),
            'index'     => 'attribute_set_name',
        ));

        return parent::_prepareColumns();
    }

    private function _getSelectedAttributes()
    {
        $supplier_id = Mage::app()->getRequest()->getParam('id');

        if (!$this->_selectedAttributes) {
            $attributes = Mage::getModel('supplierfrontendproductuploader/attributesets')
                ->getCollection()
                ->addFilter('supplier_id', $supplier_id);

            $_selectedAttributes = array();

            foreach ($attributes as $link) {
                $_selectedAttributes[] = $link->getAttributeSetId();
            }

            $allAttributes = $this->_getSelectedAllAttributes();

            foreach ($allAttributes as $attribute_id) {
                if (in_array($attribute_id, $_selectedAttributes)) {
                    $this->_selectedAttributes[] = $attribute_id;
                }
            }
        }

        return $this->_selectedAttributes;
    }

    private function _getSelectedAllAttributes()
    {
        if (!$this->_selectedAllAttributes) {
            $attributes = Mage::getModel('eav/entity_attribute_set')->getCollection();
            $this->_selectedAllAttributes = array();

            foreach ($attributes as $link) {
                $this->_selectedAllAttributes[] = $link->getAttributeSetId();
            }
        }
        return $this->_selectedAllAttributes;
    }
}
