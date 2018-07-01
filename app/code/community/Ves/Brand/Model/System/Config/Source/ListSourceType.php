<?php

class Ves_Brand_Model_System_Config_Source_ListSourceType
{
    public function toOptionArray()
    {
        return array(
        	array('value'=>'latest', 'label'=>Mage::helper('ves_brand')->__('Latest Brands') ),
            array('value'=>'hit', 'label'=>Mage::helper('ves_brand')->__('Most Read') ),

        );
    }    
}
