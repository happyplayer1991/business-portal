<?php
class Cminds_Supplierfrontendproductuploader_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('supplier_products');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(false);
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('creator_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('supplier_product_code')
            ->addAttributeToSelect('sorting_level_codes');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }

        $collection->addAttributeToSelect('price');
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        $collection->addAttributeToFilter('creator_id',array('neq' => NULL));
        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
            ));
        $this->addColumn('creator_id',
            array(
                'header'=> Mage::helper('catalog')->__('Supplier ID'),
                'width' => '50px',
                'index' => 'creator_id',
            ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
            ));
        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
            ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
            ));

        if(Mage::helper('supplierfrontendproductuploader')->isProductCodeEnabled()) {
            $this->addColumn('supplier_product_code',
                array(
                    'header'=> Mage::helper('supplierfrontendproductuploader')->__('Supplier Product Code'),
                    'width' => '100px',
                    'sortable'  => true,
                    'index'     => 'supplier_product_code',
                ));

            $this->addColumn('set_as_main',
                array(
                    'header'=> Mage::helper('supplierfrontendproductuploader')->__('Set as Main'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'set_as_main',
                    'type'      => 'options',
                    'renderer' => 'Cminds_Supplierfrontendproductuploader_Block_Adminhtml_Catalog_Product_Grid_Renderer_Setasmain'
                ));

            $this->addColumn('sorting_level_codes',
                array(
                    'header'=> Mage::helper('supplierfrontendproductuploader')->__('Sort Level'),
                    'width' => '100px',
                    'sortable'  => true,
                    'index'     => 'sorting_level_codes',
                ));
        }

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
            ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header'=> Mage::helper('catalog')->__('Qty'),
                    'width' => '100px',
                    'type'  => 'number',
                    'index' => 'qty',
                ));
        }

        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
            ));

        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            ));

        $this->addColumn('created_at',
            array(
                'header'=> Mage::helper('catalog')->__('Created On'),
                'width' => '70px',
                'index' => 'created_at',
                'renderer' => 'Cminds_Supplierfrontendproductuploader_Block_Adminhtml_Catalog_Product_Grid_Renderer_Createddate'
            ));

        $this->addColumn('enable',
            array(
                'header'=> Mage::helper('supplierfrontendproductuploader')->__('Approve'),
                'width' => '100px',
                'sortable'  => false,
                'index'     => 'enable',
                'type'      => 'options',
                'options'   => array(
                    'approve' => 'Approve',
                    'disapprove' => 'Disapprove'
                ),
                'renderer' => 'Cminds_Supplierfrontendproductuploader_Block_Adminhtml_Catalog_Product_Grid_Renderer_Approve',
                'filter_condition_callback' => array($this, '_filterApprovalCallback')
            ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'adminhtml/catalog_product/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'), 'supplier' => true)
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
            ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Rss')) {
            $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
        }

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('catalog')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));

        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('catalog')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Status'),
                    'values' => $statuses
                )
            )
        ));

        $this->getMassactionBlock()->addItem('approve', array(
            'label'=> Mage::helper('catalog')->__('Approve'),
            'url'  => $this->getUrl('*/*/massApprove'),
            'confirm' => Mage::helper('catalog')->__('Are you sure to approve these products?')
        ));
        $this->getMassactionBlock()->addItem('disapprove', array(
            'label'=> Mage::helper('catalog')->__('Disapprove'),
            'url'  => $this->getUrl('*/*/massDisapprove'),
            'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/index', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array(
                'store' => $this->getRequest()->getParam('store'),
                'supplier' => true,
                'id' => $row->getId())
        );
    }

    protected function _filterApprovalCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        if (empty($value)) {
            $this->getCollection();
        }

        if($value == 'approve') {
            $this->getCollection()
                ->addAttributeToFilter(
                    'frontendproduct_product_status',
                    array('neq' => 1)
                );
        } elseif($value == 'disapprove') {
            $this->getCollection()
                ->addAttributeToFilter(
                    'frontendproduct_product_status',
                    array('eq' => 1)
                );
        }

        return $this;
    }
}
