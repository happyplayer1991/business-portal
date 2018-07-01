<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_Themesettings
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Themesettings Extension
 *
 * @category   Ves
 * @package    Ves_Themesettings
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Themesettings_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Patterns
     *
     * @var array
     */
    protected $_texPath;
    
    /**
     * Background images
     *
     * @var array
     */
    protected $_bgImagesPath;
    
    /**
     * Prepare paths
     */
    public function __construct()
    {
        //Create paths
        $this->_texPath = 'ves_themesettings/patterns/default/';
        $this->_bgImagesPath = 'ves_themesettings/backgrounds/';
    }

    // Background images and textures /////////////////////////////////////////////////////////////////

    /**
     * Get background images directory path
     *
     * @return string
     */
    public function getBgImagesPath()
    {
        return $this->_bgImagesPath;
    }
    
    /**
     * Get textures/patterns directory path
     *
     * @return string
     */
    public function getTexPath()
    {
        return $this->_texPath;
    }

    /**
     * Get value of the extension's configuration
     *
     * @return string
     */
    public function getConfig($key, $default = "", $storeCode = NULL) {
        return $this->getSystemConfig($key,$default,"themesettings",$storeCode);
    }

    public function getCfgDesign($key, $default = "", $storeCode = NULL) {
        return $this->getSystemConfig($key, $default,"themesettings_design",$storeCode);
    }
    
    public function getCfgLayout($key, $default = "", $storeCode = NULL) {
        return $this->getSystemConfig($key, $default,"themesettings_layout",$storeCode);
    }

    public function getSystemConfig($key, $default = "", $package = "themesettings", $storeCode = NULL) {
        $return = "";
        $_session_config = Mage::registry($package);

        if(Mage::registry('ves_store')){
            $storeCode = Mage::registry('ves_store');
        }

        $enable_paneltool = Mage::getStoreConfig('themesettings/general/enable_paneltool');
        if( ($val = $this->getPanelData($package."/".$key)) && $enable_paneltool){
            return $val; 
        }

        if ($_session_config && isset($_session_config[$package."/".$key])) {
            $return = $_session_config[$package."/".$key];
        }else {
            $return = Mage::getStoreConfig("{$package}/{$key}", $storeCode);
        }
        if ($return == "" && $default) {
            $return = $default;
        }
        return $return;
    }

    public function getLayoutConfig($key, $default, $storeCode = NULL){}

	/**
     * Check product is new
     *
     * @param  Mage_Catalog_Model_Product $_product
     * @return bool
     */
	public function checkProductIsNew($_product = null) {
		$from_date = $_product->getNewsFromDate();
		$to_date = $_product->getNewsToDate();
		$is_new = false;
		$is_new = $this->isNewProduct($from_date, $to_date);
		$today = strtotime("now");

		if ($from_date && $to_date) {
			$from_date = strtotime($from_date);
			$to_date = strtotime($to_date);
			if ($from_date <= $today && $to_date >= $today) {
				$is_new = true;
			}
		}
		elseif ($from_date && !$to_date) {
			$from_date = strtotime($from_date);
			if ($from_date <= $today) {
				$is_new = true;
			}
		}elseif (!$from_date && $to_date) {
			$to_date = strtotime($to_date);
			if ($to_date >= $today) {
				$is_new = true;
			}
		}

		return $is_new;
	}

	public function isNewProduct( $created_date, $num_days_new = 3) {
		$check = false;

		$startTimeStamp = strtotime($created_date);
		$endTimeStamp = strtotime("now");

		$timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff/86400;// 86400 seconds in one day

        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        if($numberDays <= $num_days_new) {
        	$check = true;
        }

        return $check;
    }

    public function subString($text, $length = 100, $replacer = '...', $is_striped = true) {
    	$text = ($is_striped == true) ? strip_tags($text) : $text;
    	if (strlen($text) <= $length) {
    		return $text;
    	}
    	$text = substr($text, 0, $length);
    	$pos_space = strrpos($text, ' ');
    	return substr($text, 0, $pos_space) . $replacer;
    }

    public function objToArray($obj)
    {
    	if (is_object($obj)) $obj = (array)$obj;
    	if (is_array($obj)) {
    		$new = array();
    		foreach ($obj as $key => $val) {
    			$new[$key] = Mage::helper('themesettings')->objToArray($val);
    		}
    	} else {
    		$new = $obj;
    	}
    	return $new;
    }

    public function getLang(){
      return substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2);
  }

    /**
     * Get alternative image HTML of the given product
     *
     * @param Mage_Catalog_Model_Product    $product        Product
     * @param int                           $w              Image width
     * @param int                           $h              Image height
     * @param string                        $imgVersion     Image version: image, small_image, thumbnail
     * @return string
     */
    public function getAltImgHtml($product, $w, $h, $imgVersion='small_image')
    {
        $column = $this->getConfig('category_product/alt_image_column');
        $value = $this->getConfig('category_product/alt_image_column_value');
        $product->load('media_gallery');
        if ($gal = $product->getMediaGalleryImages())
        {
            $altImg = $gal->getItemByColumnValue($column, $value);
            if(!$altImg){
                $altImg = $gal->getItemByColumnValue('position', 1);
            }
            if(isset($altImg) && $altImg->getFile()){
                return Mage::helper('themesettings/image')->getImg($product, $w, $h, $imgVersion, $altImg->getFile());
            }else{
                return '';
            }
        }
        return '';
    }

    public function checkModuleInstalled( $module_name = "") {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;
        if($modulesArray) {
            $tmp = array();
            foreach($modulesArray as $key=>$value) {
                $tmp[$key] = $value;
            }
            $modulesArray = $tmp;
        }

        if(isset($modulesArray[$module_name])) {

            if((string)$modulesArray[$module_name]->active == "true") {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * Returns true, if color is specified and the value doesn't equal "transparent"
     *
     * @param string $color color code
     * @return bool
     */
    public function isColor($color)
    {
        if ($color && $color != 'transparent')
            return true;
        else
            return false;
    }

    /**
     * Get file path: CSS design
     *
     * @return string
     */
    public function getDesignFile()
    {
        return 'themesettings/css/themesettings_' . Mage::app()->getStore()->getCode() . '.css';
    }

    public function getAllStores() {
        $allStores = Mage::app()->getStores();
        $stores = array();
        foreach ($allStores as $_eachStoreId => $val)
        {
            $stores[]  = Mage::app()->getStore($_eachStoreId)->getId();
        }
        return $stores;
    }

    public function getIconUrl($image){
        return Mage::getBaseUrl('media').'ves_themesettings/icon/'.$image;
    }
    
    public function getPanelData($key = NULL){
        $cookie = Mage::getSingleton('core/cookie');
        $cookieData = $cookie->get('vespaneltool');
        if(!$cookieData) return;
        $data = unserialize($cookieData);
        if($key == NULL){
            return $data;
        }
        return isset($data[$key])?$data[$key]:'';
    }
    public function isHomePage(){
            $ishome=false;
            $page = Mage::app()->getFrontController()->getRequest()->getRouteName();

            if ($page == 'cms'){
                $storeId    = Mage::app()->getStore()->getId();
                $homepage_identifier2 = "home";

                if($storeId) {
                    $homepage_identifier = Mage::getStoreConfig("web/default/cms_home_page", $storeId);
                    $tmp_array = explode("|", $homepage_identifier);
                    $homepage_identifier2 = isset($tmp_array[0])?$tmp_array[0]:$homepage_identifier;
                } else {
                    $homepage_identifier = Mage::getStoreConfig("web/default/cms_home_page");
                }

                $ishome = (Mage::getSingleton('cms/page')->getIdentifier() == $homepage_identifier || $homepage_identifier2 == Mage::getSingleton('cms/page')->getIdentifier()) ? true :false;

                $ishomepage =  Mage::app()->getRequest()->getParam('ishome');
                if($ishomepage) {
                    $ishome = true;
                }
            }
            return $ishome;
        }
}