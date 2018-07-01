<?php
  
class Manage_Budge_Block_Adminhtml_Budge_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('budgeGrid');
        // This is the primary key of the database
        $this->setDefaultSort('budge_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
  
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('budge/budge')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
  
    protected function _prepareColumns()
    {
        $this->addColumn('budge_id', array(
            'header'    => Mage::helper('budge')->__('ID'),
            'align'     => 'center',
            'width'     => '50px',
            'index'     => 'budge_id',
        ));
        
        $this->addColumn('image', array( 
            'header' => Mage::helper('budge')->__('Image'), 
            'align' => 'left', 
            'index' => 'image',
            "renderer" =>"Manage_Budge_Block_Adminhtml_Renderer_Image", 
        ));
        
        $this->addColumn('name', array(
            'header'    => Mage::helper('budge')->__('Name'),
            'align'     => 'left',
            'index'     => 'name',
        ));


        $this->addColumn('value', array(
            'header'    => Mage::helper('budge')->__('Value'),
            'align'     => 'center',
            'index'     => 'value',
        ));

        $this->addColumn('status', array(
  
            'header'    => Mage::helper('budge')->__('Status'),
            'align'     => 'center',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1 => 'Enabled',
                0 => 'Disabled',
            ),
        ));
  
        return parent::_prepareColumns();
    }
  
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
        $this->setMassactionIdField('budge_id');
        $this->getMassactionBlock()->setFormFieldName('budge');

        $this->getMassactionBlock()->addItem('delete', array(
                'label'    => Mage::helper('budge')->__('Delete'),
                'url'      => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('budge')->__('Are you sure?')
        ));

        $statuses = array(
                1 => Mage::helper('budge')->__('Enabled'),
                0 => Mage::helper('budge')->__('Disabled')
				);
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
                'label'=> Mage::helper('budge')->__('Change status'),
                'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                        'visibility' => array(
                                'name' => 'status',
                                'type' => 'select',
                                'class' => 'required-entry',
                                'label' => Mage::helper('budge')->__('Status'),
                                'values' => $statuses
                        )
                )
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
  
    public function getGridUrl()
    {
      return $this->getUrl('*/*/grid', array('_current'=>true));
    }
  
  
} 