<?php
  
class Manage_Budge_Block_Adminhtml_Budge_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                
        $this->_objectId = 'id';
        $this->_blockGroup = 'budge';
        $this->_controller = 'adminhtml_budge';
  
        $this->_updateButton('save', 'label', Mage::helper('budge')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('budge')->__('Delete Item'));
    }
  
    public function getHeaderText()
    {
        if( Mage::registry('budge_data') && Mage::registry('budge_data')->getId() ) {
            return Mage::helper('budge')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('budge_data')->getTitle()));
        } else {
            return Mage::helper('budge')->__('Add Item');
        }
    }
} 