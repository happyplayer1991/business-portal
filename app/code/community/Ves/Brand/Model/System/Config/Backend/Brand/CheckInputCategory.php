<?php

class Ves_Brand_Model_System_Config_Backend_Brand_checkInputCategory extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave(){
        $value     = trim($this->getValue());

        if ($value && !eregi('^([1-9]+)+([,]?([0-9]+))*$', $value)) { 

            throw new Exception(Mage::helper('ves_brand')->__('Categories ID: Format is incorrect.'));
        }

        return $value;
         
    }


}
