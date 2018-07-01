<?php

class Ves_Brand_Model_System_Config_Source_ListBrand extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    { 
		
        if (!$this->_options) {
			$this->_options  = array( array("value"=>"0", "label"=>"--- None ---") );
            $collection = Mage::getModel( "ves_brand/brand" )->getCollection();
			
			foreach( $collection as $brand ){
				$this->_options[] = array("value"=>$brand->getId(), "label"=>$brand->getTitle() ); 
			}			
        }
        return $this->_options;
    }
}