<?php

class Ves_Brand_Model_System_Config_Source_ListEvents {

    public function toOptionArray()
    {
        return array(
            array('value'=>'mouseover', 'label'=>Mage::helper('adminhtml')->__('Mouse Over')),
            array('value'=>'click', 'label'=>Mage::helper('adminhtml')->__('Click'))
        );
    }

}
