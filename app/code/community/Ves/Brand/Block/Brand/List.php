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

class Ves_Brand_Block_Brand_List extends Mage_Catalog_Block_Product_List {
  
  var $_show = true;
  /**
   * Contructor
   */
  public function __construct($attributes = array())
  {
    $this->_show = $this->getGeneralConfig("show");
    
    if(!$this->_show) return;

    parent::__construct( $attributes );

    $config_template = $this->getGeneralConfig("listing_layout");

    $my_template = "";
    if(isset($attributes['template']) && $attributes['template']) {

      $my_template = $attributes['template'];

    } elseif($this->hasData("template")) {
      $my_template = $this->getData("template");

    }else {
      $my_template = "ves/brand/default.phtml";
    }

    $this->setTemplate($my_template);

  }

  public function getGeneralConfig( $val ){ 
    return Mage::getStoreConfig( "ves_brand/general_setting/".$val );
  }
  
  public function getConfig( $val ){ 
    return Mage::getStoreConfig( "ves_brand/module_setting/".$val );
  }
  
    protected function _prepareLayout()
    {
        $title =  $this->__("All Brands");
        $module = $this->getRequest()->getModuleName();
        $filter_group = $this->getRequest()->getParam('category');
        $route = $this->getGeneralConfig("route");
        if (!$route) {
          $route = $module;
        }
       

        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb( 'home', array( 'label'=>Mage::helper('ves_brand')->__('Home'),
          'title'=> Mage::helper('ves_brand')->__('Go to Home Page'),
          'link' => Mage::getBaseUrl()) );

        $breadcrumbs->addCrumb( 'venus_brand', array( 'label' => $title,
          'title' => $title,
          'link'  =>  Mage::getBaseUrl().$route) );
        //set title by list all brand
        $this->setTitleBrand($title);
        //set by group 
        if (isset($filter_group)) {
          //set tile by group
          $brand_group = $this->getGroup($filter_group);
          $this->setTitleBrand($brand_group['name']);
          $breadcrumbs->addCrumb( 'venus_group', array( 'label' => $brand_group['name'],
          'title' => Mage::helper('ves_brand')->__($brand_group['name']),
          'link'  => Mage::getBaseUrl().$route.'/'.$brand_group['identifier'].'.html') );

          $this->getLayout()->getBlock('head')->setTitle($title."-".$brand_group['name']);
        }else{
          $this->getLayout()->getBlock('head')->setTitle($title);
        }

        $this->getCountingPost();

        return parent::_prepareLayout();
    }
  public function _toHtml(){
    $grid_col_ls = $this->getGeneralConfig("grid_col_ls");
    $grid_col_ls = $grid_col_ls?(int)$grid_col_ls:3;
    $grid_col_ms = $this->getGeneralConfig("grid_col_ms");
    $grid_col_ms = $grid_col_ms?(int)$grid_col_ms:3;
    $grid_col_ss = $this->getGeneralConfig("grid_col_ss");
    $grid_col_ss = $grid_col_ss?(int)$grid_col_ss:2;
    $grid_col_mss = $this->getGeneralConfig("grid_col_mss");
    $grid_col_mss = $grid_col_mss?(int)$grid_col_mss:1;

    $this->assign("grid_col_ls", $grid_col_ls);
    $this->assign("grid_col_ms", $grid_col_ms);
    $this->assign("grid_col_ss", $grid_col_ss);
    $this->assign("grid_col_mss", $grid_col_mss);
    return parent::_toHtml();
  }
  public function getLayoutMode() {
    return $this->getGeneralConfig("listing_layout");
  }
  public function getBrands(){
    $page = $this->getRequest()->getParam('page') ? $this->getRequest()->getParam('page') : 1;
    $page = (($page - 1) > 0)?($page-1):0;
    $limit = (int)$this->getGeneralConfig("list_limit");
    $keyword = $this->getRequest()->getParam( "search_query" );
    $keyword = trim($keyword);
    $filter_group_url = $this->getRequest()->getParam('category');
    $filter_group = 0;
    if (isset($filter_group_url)) {
        $filter_group = $filter_group_url;
    }else{
        $filter_group = $this->getConfig('filter_group');
    }
    $grouparr = explode(",", $filter_group);

    $collection = Mage::getModel('ves_brand/brand')->getCollection();
    if($filter_group) {
       $collection->addFieldToFilter("group_brand_id", array("in" => $grouparr ));
    }
    

    if($keyword && strlen($keyword) >= 3) {
       $collection->addKeywordFilter($keyword);
    }

    $collection->getSelect()->limit($limit, $page*$limit);

    return $collection;
  }

  public function getCountingPost(){
    $limit = (int)$this->getGeneralConfig("list_limit");
    $keyword = $this->getRequest()->getParam( "search_query" );
    $keyword = trim($keyword);

    $filter_group_url = $this->getRequest()->getParam('category');

    if (isset($filter_group_url)) {
        $filter_group = $filter_group_url;
    }else{
        $filter_group = $this->getConfig('filter_group');
    }
    
    $collection = Mage::getModel('ves_brand/brand')->getCollection();
    $grouparr = explode(",", $filter_group);
    if($filter_group) {
       $collection->addFieldToFilter("group_brand_id", array("in" => $grouparr ));
    }

    if($keyword && strlen($keyword) >= 3) {
        $collection->addKeywordFilter($keyword);
    }
    

    Mage::register( 'paginateTotal', count($collection) );
    Mage::register( "paginateLimitPerPage", $limit );
  }

 
  public function getBrand() {
      return Mage::registry('current_brand');
  }
  public function getGroup($id_group) {
      $collection = Mage::getModel('ves_brand/group')->load($id_group);
      $data = array('name' => $collection->getName(), 'identifier' => $collection->getIdentifier());
      return $data;
  }

}
?>