<?php
 /*------------------------------------------------------------------------
  # VenusTheme Brand Module 
  # ------------------------------------------------------------------------
  # author:    VenusTheme.Com
  # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
  # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.venustheme.com
  # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/
class Ves_Brand_Block_Adminhtml_Brand_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {
		
        parent::__construct();
		
	
        $this->setId('brandGrid');
        $this->setDefaultSort('date_from');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
		
    }

  //  protected function _getStore() {
   //     $storeId = (int) $this->getRequest()->getParam('store', 0);
   //     return Mage::app()->getStore($storeId);
   // }

    protected function _prepareCollection() {
        $collection = Mage::getModel('ves_brand/brand')->getCollection();
        //$store = $this->_getStore();
        //if ($store->getId()) {
        //    $collection->addStoreFilter($store);
       // }
		
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
	
	
    protected function _prepareColumns() {  
	 
        $this->addColumn('brand_id', array(
                'header'    => Mage::helper('ves_brand')->__('ID'),
                'align'     =>'center',
                'width'     => '50px',
                'index'     => 'brand_id',
        ));
		$this->addColumn('file', array(
                'header'    => Mage::helper('ves_brand')->__('Avatar'),
                'align'     =>'center',
                'width'     => '120px',
                'index'     => 'file',
                'renderer'  => 'Ves_Brand_Block_Adminhtml_Renderer_Image'
        )); 
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', 
                    array (
                            'header' => Mage::helper('cms')->__('Store view'), 
                            'index' => 'store_id', 
                            'type' => 'store', 
                            'store_all' => true, 
                            'store_view' => true, 
                            'sortable' => false, 
                            'filter_condition_callback' => array (
                                    $this, 
                                    '_filterStoreCondition' ) ));
        }

		$this->addColumn('title', array(
                'header'    => Mage::helper('ves_brand')->__('Title'),
                'align'     =>'left',
                'index'     => 'title',
        ));
		$this->addColumn('identifier', array(
                'header'    => Mage::helper('ves_brand')->__('Identifier'),
                'align'     =>'left',
                'index'     => 'identifier',
        ));	
        $this->addColumn('group_brand_id', array(
                'header'    => Mage::helper('ves_brand')->__('Group Name'),
                'align'     =>'left',
                'index'     => 'group_brand_id',
                //'width'     => '120px',
                'renderer'  => 'Ves_Brand_Block_Adminhtml_Renderer_Group'
        ));
		$this->addColumn('position', array(
                'header'    => Mage::helper('ves_brand')->__('Sort Order'),
                'align'     =>'left',
                'index'     => 'position',
				 'width'     => '80px',
        ));
		
		$this->addColumn('is_active', array(
                'header'    => Mage::helper('ves_brand')->__('Status'),
                'align'     => 'left',
                'width'     => '80px',
                'index'     => 'is_active',
                'type'      => 'options',
                'options'   => array(
                        1 => Mage::helper('ves_brand')->__('Enabled'),
                        0 => Mage::helper('ves_brand')->__('Disabled'),
                ),
        ));

        return parent::_prepareColumns();
    }
    /**
     * Helper function to do after load modifications
     *
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
    /**
     * Helper function to add store filter condition
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        
        $this->getCollection()->addStoreFilter($value);
    }

    protected function _prepareMassaction() { 
        $this->setMassactionIdField('brand_id');
        $this->getMassactionBlock()->setFormFieldName('brand');

        $this->getMassactionBlock()->addItem('delete', array(
                'label'    => Mage::helper('ves_brand')->__('Delete'),
                'url'      => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('ves_brand')->__('Are you sure?')
        ));

        $statuses = array(
                1 => Mage::helper('ves_brand')->__('Enabled'),
                2 => Mage::helper('ves_brand')->__('Disabled')
				);
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
                'label'=> Mage::helper('ves_brand')->__('Change status'),
                'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                        'visibility' => array(
                                'name' => 'status',
                                'type' => 'select',
                                'class' => 'required-entry',
                                'label' => Mage::helper('ves_brand')->__('Status'),
                                'values' => $statuses
                        )
                )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}