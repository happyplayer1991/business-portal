<?php
/**
 * Magento Whatsappshare extension
 *
 * @category   Magecomp
 * @package    Magecomp_Whatsappshare
 * @author     Magecomp
 */
class Magecomp_Whatsappshare_Model_System_Config_Source_Category 
{
    public function toOptionArray() 
	{
        return array(
            array('value' => 0, 'label' => Mage::helper('adminhtml')->__('All Categories')),
            array('value' => 1, 'label' => Mage::helper('adminhtml')->__('Specific Categories'))
        );
    }

}
