<?php
/***************************************
 *** Customer Photo Crop Extension ***
 ***************************************
 *
 * @copyright   Copyright (c) 2015
 * @company     NetAttingo Technologies
 * @package     Netgo_Customerpic
 * @author 		Vipin
 * @dev			77vips@gmail.com
 *
 */
class Netgo_Customerpic_Block_Customerpic_Photo extends Mage_Core_Block_Template
{
    /**
     * @access 		Public
     * @author 		Vipin
	 * @dev			77vips@gmail.com
	 * @output 		Return saved customer photo
     */ 
	public function getCustomerPhoto()
    {
		$_helper = Mage::helper('netgo_customerpic');
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			$customerData = Mage::getModel('customer/customer')->load($customer->getId())->getData();
			$img = ($customerData['profile_photo'] != '') ? $customerData['profile_photo'] : '';
		}  
		
		if($img != ''){
			$thumb_height = $_helper->getThumbHeight();	
			$img = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/profile/thumbs/'.$thumb_height.'/'.$img; 
		}else{
			$img = $this->getSkinUrl('customerpic/img/no-image.png');
		}
		return $img;
    }
}
