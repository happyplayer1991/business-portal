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
class Ves_Brand_Model_Observer  extends Varien_Object
{
	
	/**
	 *
	 */
	public function initControllerRouters( $observer ){	
		
        $request = $observer->getEvent()->getFront()->getRequest();
	
		$identifier = trim($request->getPathInfo(), '/');
	
	 
        $condition = new Varien_Object(array(
            'identifier' => $identifier,
            'continue'   => true
        ));
        Mage::dispatchEvent('brand_controller_router_match_before', array(
            'router'    => $this,
            'condition' => $condition
        ));
        $identifier = $condition->getIdentifier();
		
		 
        if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return true;
        }

        if (!$condition->getContinue())
            return false;
		$route = trim( Mage::getStoreConfig('ves_brand/general_setting/route') );
		if($identifier) {
			
            if(  preg_match("#^".$route."(\.html)?$#",$identifier, $match) ) {
                $request->setModuleName('venusbrand')
                        ->setControllerName('brand')
                        ->setActionName('index');
                $request->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                    $identifier
                );
                return true;
			
            }  
			return true;
	    } 
		
        return false;					
	}
	
	/**
	 *
	 */
	public function beforeRender( Varien_Event_Observer $observer ){
	//	$controller_name = Mage::app()->getRequest()->getControllerModule();
	//	$menu_name = $controller_name."_".Mage::app()->getRequest()->getControllerName();
		$helper =  Mage::helper('ves_brand/data');
		
		
		// if($helper->checkAvaiable( $controller_name )){
			 $config = $helper->get();
			 $this->_loadMedia( $config );
		 	/**LATEST BLOG */
		//	$this->brandScrollModule( $menu_name , $helper );
			/** CATEGORY BLOG */
	//		$this->brandNavModule( $menu_name , $helper );
		// }
   }
   
   public function getGeneralConfig( $val, $default = "" ){ 
		return Mage::getStoreConfig( "ves_brand/general_setting/".$val );
 	}

   public function getModuleConfig( $val ){
		return Mage::getStoreConfig( "ves_brand/module_setting/".$val );
   }
   
   public function brandScrollModule( $menu_name, $helper ){
   		if( !$this->getModuleConfig("enable_scrollmodule") ){
			return ;
		}
		
		if($helper->checkMenuItem( $menu_name, $this->getModuleConfig("scroll_menuassignment") )){
			
			$layout = Mage::getSingleton('core/layout');
			$title = $this->getModuleConfig("scroll_title");
			$position = $this->getModuleConfig("scroll_position");
			if( !$position ){ $position = "right"; }
			
			$cposition = $this->getModuleConfig("scroll_customposition");
			if( $cposition ){ $position = $cposition; }

			$display = $this->getModuleConfig("scroll_display");
			if( $display=="after" ){ $display = true; }else { $display=false; }
	
			$block =  $layout->createBlock( 'ves_brand/scroll' );
	
			if($myblock = $layout->getBlock( $position )){
				$myblock->insert($block, $title , $display);
			}

		}
   }
   
   
   
    public function brandNavModule( $menu_name, $helper ){
		if( !$this->getModuleConfig("enable_brandnavmodule")){
			return ;
		}

		if($helper->checkMenuItem( $menu_name, $this->getModuleConfig("brandnav_menuassignment") )){
			
			$layout = Mage::getSingleton('core/layout');
			$title = $this->getModuleConfig("brandnav_title");
			$position = $this->getModuleConfig("brandnav_position");
			if( !$position ){ $position = "right"; }
			
			$cposition = $this->getModuleConfig("brandnav_customposition");
			if( $cposition ){ $position = $cposition; }

			$display = $this->getModuleConfig("brandnav_display");
			if( $display=="after" ){ $display = true; }else { $display=false; }

			$block =  $layout->createBlock( 'ves_brand/brandnav' );

			if($myblock = $layout->getBlock( $position )){
				$myblock->insert($block, $title , $display);
			}

		}
	}

   function _loadMedia( $config = array()){
		/*
		$mediaHelper =  Mage::helper('ves_brand/media');
		$mediaHelper->addMediaFile("skin_css", "ves_brand/style.css" );*/
	}
}
