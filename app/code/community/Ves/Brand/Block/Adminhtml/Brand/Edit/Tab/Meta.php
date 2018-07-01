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

class Ves_Brand_Block_Adminhtml_Brand_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $_model = Mage::registry('brand_data');
        $form = new Varien_Data_Form();
        $this->setForm($form);

		$fieldset = $form->addFieldset('category_meta', array('legend'=>Mage::helper('ves_brand')->__('Meta Information')));
        
		
		$fieldset->addField('meta_keywords', 'editor', array(
			'label'     => Mage::helper('ves_brand')->__('Meta Keywords'),
			'class'     => '',
			'required'  => false,
			'name'      => 'meta_keywords',
			'style'     => 'width:600px;height:100px;',
			'wysiwyg'   => false
		));
		$fieldset->addField('meta_description', 'editor', array(
			'label'     => Mage::helper('ves_brand')->__('Meta Description'),
			'class'     => '',
			'required'  => false,
			'name'      => 'meta_description',
			'style'     => 'width:600px;height:100px;',
			'wysiwyg'   => false
		));
		
        
		if ( Mage::getSingleton('adminhtml/session')->getBrandData() )
		  {
			  $form->setValues(Mage::getSingleton('adminhtml/session')->getBrandData());
			  Mage::getSingleton('adminhtml/session')->getBrandData(null);
		  } elseif ( Mage::registry('brand_data') ) {
			  $form->setValues(Mage::registry('brand_data')->getData());
		  }
        
        return parent::_prepareForm();
    }
	
	  
}
