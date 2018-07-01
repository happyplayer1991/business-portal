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

class Ves_Brand_BrandController extends Mage_Core_Controller_Front_Action
{  	
	public function indexAction(){
		 
	 	$show = $this->getGeneralConfig("show");
	 	if(!$show) {
	 		$this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
			$this->getResponse()->setHeader('Status','404 File not found');
			$this->_redirect('404-notfound');
	 	}
	 	if($this->getRequest()->getParam('category')){
        		Mage::register('category_brand', $this->getRequest()->getParam('category'));
        }
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function viewAction(){
		$show = $this->getGeneralConfig("show");
	 	if(!$show) {
	 		$this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
			$this->getResponse()->setHeader('Status','404 File not found');
			$this->_redirect('404-notfound');
	 	}


		$id = (int) $this->getRequest()->getParam( 'id', false);
        $brand = Mage::getModel('ves_brand/brand')->load( $id );
        Mage::register('current_brand', $brand);

		$this->loadLayout();
		$this->renderLayout();
	}

	public function getGeneralConfig( $val, $default = "" ){ 
		return Mage::getStoreConfig( "ves_brand/general_setting/".$val );
	}
}
?>