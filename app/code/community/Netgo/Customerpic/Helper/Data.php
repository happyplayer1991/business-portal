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
class Netgo_Customerpic_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @access 		Public
     * @author 		NetAttingo Technologies
	 * @dev			Vipin(77vips@gmail.com)
	 * @output 		Return options
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }
	
	/**
     * @access 		Public
     * @author 		NetAttingo Technologies
	 * @dev			Vipin(77vips@gmail.com)
	 * @output 		Absolute directory path
     */
	public function getBaseDir()
    {
        return Mage::getBaseDir(); 
    }
	
	/**
     * @access 		Public
     * @author 		NetAttingo Technologies
	 * @dev			Vipin(77vips@gmail.com)
	 * @output 		Absolute directory path
     */
	public function getFileExt($file_path)
    {
        return pathinfo($file_path, PATHINFO_EXTENSION);
    }
	
	/**
     * @access 		Public
     * @author 		NetAttingo Technologies
	 * @dev			Vipin(77vips@gmail.com)
	 * @output 		Generate and return image name
     */
	public function generateImgName($ext)
    {
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customerData = Mage::getSingleton('customer/session')->getCustomer();
			$customer_id =  $customerData->getId();
		}		
        return 'customer_profile_'.time().'_'.$customer_id.".".$ext;
    }
	
	/**
     * @access 		Public
     * @author 		NetAttingo Technologies
	 * @dev			Vipin(77vips@gmail.com)
	 * @output 		Media url
     */
	public function getMediaUrl()
    {
		return Mage::getBaseUrl('media');
	}
	 
	/**
     * @access 		Public
     * @author 		NetAttingo Technologies
	 * @dev			Vipin(77vips@gmail.com)
	 * @output 		Return image quality
     */
	public function getQuality()
    {
		$quality = Mage::getStoreConfig('netgo_customerpic/customerpic/img_quality');
		$quality = (isset($quality) && $quality != '') ? $quality : 90;
		return $quality;
	}
	
	/**
     * @access 		Public
     * @author 		NetAttingo Technologies
	 * @dev			Vipin(77vips@gmail.com)
	 * @output 		Return image width
     */
	public function getThumbWidth()
    {
		$thumb_width = Mage::getStoreConfig('netgo_customerpic/customerpic/img_width');
		$thumb_width = (isset($thumb_width) && $thumb_width != '') ? $thumb_width : 150;
		return $thumb_width;
	}
	
	/**
     * @access 		Public
     * @author 		NetAttingo Technologies
	 * @dev			Vipin(77vips@gmail.com)
	 * @output 		Return image height
     */
	public function getThumbHeight()
    {
		$thumb_height = Mage::getStoreConfig('netgo_customerpic/customerpic/img_height');
		$thumb_height = (isset($thumb_height) && $thumb_height != '') ? $thumb_height : 200;
		return $thumb_height;
	}
	
	/**
     * @access 		Public
     * @author 		NetAttingo Technologies
	 * @dev			Vipin(77vips@gmail.com)
	 * @output 		Create image according to EXT
     */
	public function imagecreatefromExt($src)
    {
		$img_path 		= explode("/media/", $src); 
		$path_to_image 	= $this->getBaseDir().'/media/'.$img_path[1];
		$info 			= pathinfo($path_to_image);
		$extension 		= strtolower($info['extension']);
		
		switch ($extension) {
			case 'jpg':
				$img_r = imagecreatefromjpeg("{$path_to_image}");
			break;
			case 'jpeg':
				$img_r = imagecreatefromjpeg("{$path_to_image}");
			break;
			case 'png':
				$img_r = imagecreatefrompng("{$path_to_image}");
			break;
			case 'gif':
				$img_r = imagecreatefromgif("{$path_to_image}");
			break;
			default:
				$img_r = imagecreatefromjpeg("{$path_to_image}");
		}
		return $img_r;
	}
	
	/**
     * @access 		Public
     * @author 		NetAttingo Technologies
	 * @dev			Vipin(77vips@gmail.com)
	 * @output 		Return image name from the given path
     */
	public function getImgName($src)
    {
		return preg_replace('/^.+[\\\\\\/]/', '', $src);
	}
}
