<?php
 /*------------------------------------------------------------------------
  # VenusTheme Brand Module 
  # ------------------------------------------------------------------------
  # author:    VenusTheme.Com
  # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
  # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.venustheme.com
  # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/

class Ves_Brand_Block_Groupmenu extends Ves_Brand_Block_List {

	var $_show = true;
  /**
   * Contructor
   */
  public function __construct($attributes = array())
  {

    $this->_show = $this->getGeneralConfig("show");
    if(!$this->_show) return;

    parent::__construct( $attributes );
	  $my_template = "ves/brand/groupmenu.phtml";
    $this->setTemplate($my_template);

  }

  public function _toHtml(){
    // if(!$this->getConfig("enable_groupmodule")) {
    //   return ;
    // }
    $menus = $this->getCategoryBrand();
    // Assign html
    $this->setCategoryBrands($menus);
    return parent::_toHtml(); 
  }
  public function getCategoryBrand(){

    $limit = (int)$this->getGeneralConfig("limit_group",8);
    $collection = Mage::getModel('ves_brand/group')->getCollection()
    ->addFieldToFilter("status", array("eq" => 1))
    ->setPageSize($limit)
    ->setOrder("group_id","DESC");

    return $collection;
  }

}