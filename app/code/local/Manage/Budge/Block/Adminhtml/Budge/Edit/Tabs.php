<?php
  
class Manage_Budge_Block_Adminhtml_Budge_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  
    public function __construct()
    {
        parent::__construct();
        $this->setId('budge_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('budge')->__('Budge Information'));
    }
  
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('budge')->__('Budge Information'),
            'title'     => Mage::helper('budge')->__('Budge Information'),
            'content'   => $this->getLayout()->createBlock('budge/adminhtml_budge_edit_tab_form')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}