<?php
  
class Manage_Budge_Block_Adminhtml_Budge extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_budge';
        $this->_blockGroup = 'budge';
        $this->_headerText = Mage::helper('budge')->__('Budge Manager');
        $this->_addButtonLabel = Mage::helper('budge')->__('Add Item');
        parent::__construct();
    }
} 