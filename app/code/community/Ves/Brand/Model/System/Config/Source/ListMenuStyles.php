<?php

class Ves_Brand_Model_System_Config_Source_ListMenuStyles {

	public function toOptionArray()
    {
        return array(
            array('value'=>'Accordion', 'label'=>Mage::helper('adminhtml')->__('Accordion')),
            array('value'=>'Dropdown', 'label'=>Mage::helper('adminhtml')->__('Dropdown')),
            array('value'=>'Tree', 'label'=>Mage::helper('adminhtml')->__('Tree')),
        );
    }

}
