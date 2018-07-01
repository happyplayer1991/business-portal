<?php
  
class Manage_Budge_Adminhtml_SetController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() {
        $this->_title($this->__('Set Budge'));

        $this->loadLayout()
        ->_addContent($this->getLayout()->createBlock('budge/adminhtml_budge')->setTemplate('budge/set.phtml'))
        ->renderLayout();
    }
  
} 