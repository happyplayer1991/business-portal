<?php
  
class Manage_Budge_Model_Mysql4_Budge extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {  
        $this->_init('budge/budge', 'budge_id');
    }
} 