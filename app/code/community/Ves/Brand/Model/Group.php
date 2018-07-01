<?php
 /*------------------------------------------------------------------------
  # Ves Map Module 
  # ------------------------------------------------------------------------
  # author:    Ves.Com
  # copyright: Copyright (C) 2012 http://www.ves.com. All Rights Reserved.
  # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.ves.com
  # Technical Support:  http://www.ves.com/
-------------------------------------------------------------------------*/
class Ves_Brand_Model_Group extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {	
	    $this->_init('ves_brand/group');
	
    }
    public function getCategoryLink(){
      if($isSecure = Mage::app()->getStore()->isCurrentlySecure()) {
        $base_url = Mage::getBaseUrl( Mage_Core_Model_Store::URL_TYPE_LINK, true );
      } else {
        $base_url = Mage::getBaseUrl();
      }
      return  $base_url.Mage::getModel('core/url_rewrite')->loadByIdPath('venusbrand/brand/index/category/'.$this->getId())->getRequestPath();
    }

    public function deletes($group_id)
    {
      $res = true;
      $images = $this->image;
      foreach ($images as $image)
      {
        if (preg_match('/sample/', $image) === 0)
          if ($image && file_exists(dirname(__FILE__).'/images/'.$image))
            $res &= @unlink(dirname(__FILE__).'/images/'.$image);
      }
      $model = $this->setId($group_id);
          try{
            $model->delete();
            //Mage::getSingleton('core/session')->addSuccess( 'Deleted profile successfully!' );  
            //echo 'Inserted abc',$group_id;
        }catch(exception $e){
            //Mage::getSingleton( 'core/session') ->addError( $e->getMessage() );
        }
      return $res;
    }
}