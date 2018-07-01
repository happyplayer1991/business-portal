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

class Ves_Brand_Block_Brand_Productlist extends Mage_Catalog_Block_Product_List {
	
    var $_show = true;
    /**
     * Contructor
     */
    public function __construct($attributes = array())
    {
        $this->_show = $this->getGeneralConfig("show");
        
        if(!$this->_show) return;
        parent::__construct( $attributes );
    }

	public function getGeneralConfig( $val ){ 
		return Mage::getStoreConfig( "ves_brand/general_setting/".$val );
	}
	
	public function getConfig( $val ){ 
		return Mage::getStoreConfig( "ves_brand/module_setting/".$val );
	}
	
    protected function _prepareLayout()
    {
         $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $title = $this->getBrand()->getTitle();

            $breadcrumbs->addCrumb('home', array( 'label' => $this->__('Home'),  'title' => $this->__('Go to Home Page'), 'link'  => Mage::getBaseUrl() ))->addCrumb('brands', array(
                'label' => $this->__("Brand"),
                'title' => $this->__("Brand"),
				'link'  => Mage::getUrl( $this->getGeneralConfig("route") )
            ))
			->addCrumb('item', array(
                'label' => $title,
                'title' => $title,
            ));
        }
		if ($this->getBrand()->getPagetitle()) {
			$title = $this->getBrand()->getPagetitle();
		} else {
	        $title = $this->__("Brand - %s", $this->getBrand()->getTitle());
		}
        $this->getLayout()->getBlock('head')->setTitle($title);

		if ($this->getBrand()->getMetaKeywords()) {
			$keywords = $this->getBrand()->getMetaKeywords();
			$this->getLayout()->getBlock('head')->setKeywords($keywords);
		}

		if ($this->getBrand()->getMetaDescription()) {
			$description = $this->getBrand()->getMetaDescription();
			$this->getLayout()->getBlock('head')->setDescription($description);
		}
        return parent::_prepareLayout();
    }


    public function getHeaderText()
    {
        if( $this->getBrand()->getTitle() ) {
            return Mage::helper('brands')->__("Brand - '%s'", $this->htmlEscape($this->getBrand()->getTitle()));
        } else {
            return false;
        }
    }
    public function getBrand() {
        return Mage::registry('current_brand');
    }

    protected function _getProductCollection()    {
        if (is_null($this->_productCollection)) {
			$collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
	        	->addFieldToFilter(array(
			        				array('attribute'=>'vesbrand','eq'=>(int) $this->getRequest()->getParam('id', false)),
					))
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addMinimalPrice()
                ->addUrlRewrite();
            $this->_productCollection = $collection;
            Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_productCollection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($this->_productCollection);
        }

        return $this->_productCollection;
    }

	public function getLoadedProductCollection() {
		return $this->_getProductCollection();
	}

}
?>