<?php
  
class Manage_Budge_Model_Mysql4_Budge_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        //parent::__construct();
        $this->_init('budge/budge');
    }
} 