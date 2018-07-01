<?php

class Ves_Brand_Model_System_Config_Source_Imagetypes
{
    public function toOptionArray()
    {
        return array(
        	array('value'=>'l', 'label'=>Mage::helper('ves_brand')->__('Large')." (".Mage::getStoreConfig('ves_brand/general_setting/large_imagesize') .")" ),
            array('value'=>'m', 'label'=>Mage::helper('ves_brand')->__('Medium')." (".Mage::getStoreConfig('ves_brand/general_setting/medium_imagesize') .")"),
            array('value'=>'s', 'label'=>Mage::helper('ves_brand')->__('Small')." (".Mage::getStoreConfig('ves_brand/general_setting/small_imagesize') .")"),

        );
    }    
}
