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
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Blog Extension
 *
 * @category   Ves
 * @package    Ves_Brand
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Brand_Block_Brand_Toolbar extends Mage_Core_Block_Template
{

  public function __construct($attributes = array())
  {
    parent::__construct( $attributes );
    if(!Mage::getStoreConfig('ves_brand/general_setting/show')) {
      return;
    }
  }

  protected function _prepareLayout() {

  }
  public function getTotal() {
    return Mage::registry('paginateTotal');
  }

  public function getPages() {
    return ceil(($this->getTotal())/(int)$this->getLimitPerPage() );
  }

  public function getLimitPerPage(){
    return Mage::registry('paginateLimitPerPage');
  }

  public function getCurrentLink() {
    $module = $this->getRequest()->getModuleName();
    $controller = $this->getRequest()->getControllerName();
    $module = strtolower($module);
    if($module == "ves_brand" || $module == "venusbrand"){
      if($controller == "brand" || $controller == "index") {
        $filter_group_url = $this->getRequest()->getParam('category');
        //echo $filter_group_url;
        if (isset($filter_group_url)) {
            $filter_group = $filter_group_url;
        }else{
            $filter_group = $this->getConfig('filter_group');
        }
        if( (int)$filter_group ) {
          return Mage::getModel('ves_brand/group')->load((int)$filter_group)->getCategoryLink();
        } else {
          $route = trim( Mage::getStoreConfig('ves_brand/general_setting/route') );
          return  Mage::getBaseUrl().$route;
        }
        
        
      }
    }
    return;
  }
}