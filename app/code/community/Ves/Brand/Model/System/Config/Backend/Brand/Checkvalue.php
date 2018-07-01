<?php

class Ves_Brand_Model_System_Config_Backend_Brand_Checkvalue extends Mage_Core_Model_Config_Data
{

    protected function _beforeSave(){
        $value=$this->getValue();
        	if ((!is_numeric($value) && !empty($value)) || $value < 0) {				
        	    throw new Exception(Mage::helper('ves_brand')->__($this->getField_config()->label . ': Value must be numeric.'));
        	}
        return $this;
    }

}
