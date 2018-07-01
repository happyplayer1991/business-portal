<?php
  
class Manage_Budge_Model_Budge extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('budge/budge');
    }
} 