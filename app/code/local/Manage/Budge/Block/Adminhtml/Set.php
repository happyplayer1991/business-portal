<?php
  
class Manage_Budge_Block_Adminhtml_Set extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        //$this->_controller = 'adminhtml_budge';
        //$this->_blockGroup = 'set';
        $this->_headerText = Mage::helper('budge')->__('Budge Manager');
        $this->_addButtonLabel = Mage::helper('budge')->__('Save Item');
        parent::__construct();
    }
} 