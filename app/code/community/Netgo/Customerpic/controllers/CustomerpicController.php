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
class Netgo_Customerpic_CustomerpicController extends Mage_Core_Controller_Front_Action
{
    /**
     * @access 		Public
     * @author 		Vipin
	 * @dev			77vips@gmail.com
	 * @output 		Return saved customer photo
     */
    public function indexAction()
    {
        $this->loadLayout();
        if(! Mage::helper('customer')->isLoggedIn()){
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account'));
        }
        $this->renderLayout();
    }

    /**
     * @access 		Public
     * @author 		Vipin
	 * @dev			77vips@gmail.com
	 * @output 		Upload customer photo
     */
    public function uploadAction(){		
		$_helper = Mage::helper('netgo_customerpic');
		$root_path = $_helper->getBaseDir(); 

		//Get the tmp url
		$photo_src = $_FILES['photo']['tmp_name'];
		$photo_path = $_FILES['photo']['name'];		
		$ext = $_helper->getFileExt($photo_path);
		
		//Check if the photo really exists
		if (is_file($photo_src)) { 		
			$thumb_width = $_helper->getThumbWidth();	
			$thumb_height = $_helper->getThumbHeight();				
			
			if (!file_exists($root_path.'/media/profile/thumbs/'.$thumb_height)) { 				
				mkdir($root_path.'/media/profile/thumbs/'.$thumb_height, 0777, true); 
			}			 
			//Photo path in our example  
			$img_name = $_helper->generateImgName($ext);
			$photo_dest = $root_path.'/media/profile/'.$img_name;
			copy($photo_src, $photo_dest);
			
			$img_path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true ).'media/profile/'.$img_name;	
					
			echo '<script type="text/javascript">window.top.window.show_popup_crop("'.$img_path.'","'.$customer_id.'","'.$thumb_width.'","'.$thumb_height.'")</script>';
		}
	}
    /**
     * @access 		Public
     * @author 		Vipin
	 * @dev			77vips@gmail.com
	 * @output 		Crop customer photo
     */
	public function cropAction(){	
		$_helper = Mage::helper('netgo_customerpic');
		$root_path = $_helper->getBaseDir();  
		$media_url = $_helper->getMediaUrl();
		
		//Target size
		$postData = $this->getRequest()->getPost(); 
		$targ_w = $postData['targ_w'];$_POST['targ_w'];
		$targ_h = $postData['targ_h'];$_POST['targ_h'];
		
		//Quality
		$img_quality = $_helper->getQuality();
		//Get thumb size
		$thumb_width = $_helper->getThumbWidth();
		$thumb_height = $_helper->getThumbHeight();
		
		//Photo path
		$src 	= $postData['photo_url'];   
		$img_r 	= $_helper->imagecreatefromExt($src);
		
		$dst_r 	= ImageCreateTrueColor( $thumb_width, $thumb_height);
		
		//Crop photo
		imagecopyresampled($dst_r, $img_r, 0, 0, $postData['x'], $postData['y'], $thumb_width, $thumb_height, $postData['w'], $postData['h']);
		
		//Create the physical photo
		$img_name = $_helper->getImgName($src); 
		
		imagejpeg($dst_r, $root_path.'/media/profile/thumbs/'.$thumb_height.'/'.$img_name,$img_quality);		
		
		//Save as customer profile picture
		$this->saveImage($postData, $img_name);
		
		echo '<img src="'.$media_url.'/profile/thumbs/'.$thumb_height.'/'.$img_name.'?'.time().'">';
		
		imagedestroy($img_r);
		exit;
	}	
	/**
     * @access 		Public
     * @author 		Vipin
	 * @dev			77vips@gmail.com
	 * @output 		Save customer photo
     */
	public function saveImage($postData, $img_name){		
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customer = Mage::getSingleton('customer/session')->getCustomer();			
			$custObj = Mage::getModel('customer/customer')->load($customer->getId());			
			if($postData['x'] != '' && $postData['y'] != '' && $postData['w'] != '' && $postData['h'] != ''){
				if($img_name != ''){
					$custObj->setProfilePhoto($img_name);
					$custObj->save();
				}
			}
		}
	}
}
