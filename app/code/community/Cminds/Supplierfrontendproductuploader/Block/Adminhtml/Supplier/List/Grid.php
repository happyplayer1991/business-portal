<?php

class Cminds_Supplierfrontendproductuploader_Block_Adminhtml_Supplier_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('id');
        $this->setId('supplier_list_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _getCollectionClass()
    {
        return 'customer/customer';
    }

    protected function _prepareCollection()
    {
        $supplierHelper = Mage::helper("supplierfrontendproductuploader");
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->addAttributeToSelect('rejected_notfication_seen')
            ->addAttributeToSelect('supplier_approve')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

        $allowedGroups = $supplierHelper->getAllowedGroups();

        if ($allowedGroups) {
            $collection->addAttributeToFilter('group_id', array("in" => $allowedGroups));
        } else {
            $collection->addAttributeToFilter('group_id', 0);
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
            'header' => Mage::helper('customer')->__('ID'),
            'width' => '50px',
            'index' => 'entity_id',
            'type' => 'number',
        ));

        $this->addColumn('name',
            array(
            'header' => Mage::helper('customer')->__('Name'),
            'index' => 'name'
        ));
        $this->addColumn('email',
            array(
            'header' => Mage::helper('customer')->__('Email'),
            'width' => '150',
            'index' => 'email'
        ));

        $this->addColumn('Telephone',
            array(
            'header' => Mage::helper('customer')->__('Telephone'),
            'width' => '100',
            'index' => 'billing_telephone'
        ));

        $this->addColumn('billing_country_id',
            array(
            'header' => Mage::helper('customer')->__('Country'),
            'width' => '100',
            'type' => 'country',
            'index' => 'billing_country_id',
        ));

        $this->addColumn('billing_postcode',
            array(
            'header' => Mage::helper('customer')->__('State'),
            'width' => '90',
            'index' => 'billing_region',
        ));

        $this->addColumn('customer_since',
            array(
            'header' => Mage::helper('customer')->__('Since'),
            'type' => 'datetime',
            'align' => 'center',
            'index' => 'created_at',
            'gmtoffset' => true
        ));

        $this->addColumn('supplier_approve',
            array(
            'header' => Mage::helper('customer')->__('Is Approved'),
            'align' => 'center',
            'width' => '100',
            'index' => 'supplier_approve',
            'type' => 'options',
            'options' => array('1' => 'Yes', '0' => 'No')
        ));

        $this->addColumn('action',
            array(
            'header' => Mage::helper('customer')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('customer')->__('Edit'),
                    'url' => array('base' => '*/customer/edit', 'params' => array(
                            'supplier' => true)),
                    'field' => 'id',
                    'supplier' => true
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv',
            Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml',
            Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id')
            ->setErrorText(
                Mage::helper('core')->jsQuoteEscape(
                    Mage::helper('supplierfrontendproductuploader')->__('Please select supplier')
                )
        );

        $this->getMassactionBlock()->addItem('approve',
            array(
            'label' => Mage::helper('supplierfrontendproductuploader')->__('Approve'),
            'url' => $this->getUrl('*/*/massApprove'),
            'confirm' => Mage::helper('supplierfrontendproductuploader')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('dissapprove',
            array(
            'label' => Mage::helper('supplierfrontendproductuploader')->__('Dissapprove'),
            'url' => $this->getUrl('*/*/massDissapprove'),
            'confirm' => Mage::helper('supplierfrontendproductuploader')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('remove',
            array(
            'label' => Mage::helper('supplierfrontendproductuploader')->__('Remove'),
            'url' => $this->getUrl('*/*/massRemove'),
            'confirm' => Mage::helper('supplierfrontendproductuploader')->__('Are you sure?')
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit',
                array('id' => $row->getId(), 'supplier' => true));
    }
}