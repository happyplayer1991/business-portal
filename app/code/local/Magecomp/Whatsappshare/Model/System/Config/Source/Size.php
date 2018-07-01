<?php
/**
 * Magento Whatsappshare extension
 *
 * @category   Magecomp
 * @package    Magecomp_Whatsappshare
 * @author     Magecomp
 */
class Magecomp_Whatsappshare_Model_System_Config_Source_Size
{
	public function toOptionArray() 
	{
        return array(
            array('value' => 'small', 'label' => Mage::helper('adminhtml')->__('Small')),
            array('value' => 'medium', 'label' => Mage::helper('adminhtml')->__('Medium')),
			array('value' => 'large', 'label' => Mage::helper('adminhtml')->__('Large'))
        );
    }
}