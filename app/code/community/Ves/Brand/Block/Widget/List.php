<?php
class Ves_Brand_Block_Widget_List extends Ves_Brand_Block_Brandnav implements Mage_Widget_Block_Interface
{
	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{
	 	parent::__construct($attributes);
		
		/*Cache Block*/
        $enable_cache = $this->getConfig("enable_cache", 1 );
        if(!$enable_cache) {
          $cache_lifetime = null;
        } else {
          $cache_lifetime = $this->getConfig("cache_lifetime", 86400 );
          $cache_lifetime = (int)$cache_lifetime>0?$cache_lifetime: 86400;
        }

        $this->addData(array('cache_lifetime' => $cache_lifetime));

        $this->addCacheTag(array(
          Mage_Core_Model_Store::CACHE_TAG,
          Mage_Cms_Model_Block::CACHE_TAG,
          Ves_Brand_Model_Brand::CACHE_WIDGET_LIST_TAG
        ));

	}

	/**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
           'VES_BRAND_WIDGET_LIST',
           $this->getNameInLayout(),
           Mage::app()->getStore()->getId(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           'template' => $this->getTemplate(),
        );
    }

	public function _toHtml() {
      $pretext = $this->getConfig("pretext");
      $pretext = base64_decode($pretext);
      $filter_group = $this->getConfig('filter_group');
      $show = $this->getConfig("show");
      $limit = (int)$this->getConfig('itemvisiable');
      
      if(!$show ) return;
      $collection = Mage::getModel( 'ves_brand/brand' )->getCollection();
      $collection->addFieldToFilter("group_brand_id", array("eq" => $filter_group))
      ->addFieldToFilter('is_active', 1)
      ->setOrder( 'position', 'ASC' );

      if($limit){
        $collection ->setPageSize($limit);
      }
      $resroute = Mage::getStoreConfig('ves_brand/general_setting/route');
      $extension = ".html";
      foreach( $collection as $model ){
        if(!$model->getLink()){
          Mage::getModel('core/url_rewrite')->loadByIdPath('venusbrand/brand/'.$model->getId())
                ->setIdPath('venusbrand/brand/'.$model->getId())
                ->setRequestPath($resroute .'/'.$model->getIdentifier().$extension  )
                ->setTargetPath('venusbrand/brand/view/id/'.$model->getId())
                ->save();
        } 
      }
      $this->setData("pretext", $pretext);
      $this->assign('resroute',$resroute);
      return parent::_toHtml();
	}
}