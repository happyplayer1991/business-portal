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
class Ves_Brand_Block_Adminhtml_Brand_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('brand_form');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('ves_brand')->__('Brand Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('ves_brand')->__('General Information'),
            'title'     => Mage::helper('ves_brand')->__('General Information'),
            'content'   => $this->getLayout()->createBlock('ves_brand/adminhtml_brand_edit_tab_form')->toHtml(),
        ));
		$this->addTab('form_section_seo', array(
            'label'     => Mage::helper('ves_brand')->__('SEO'),
            'title'     => Mage::helper('ves_brand')->__('SEO'),
            'content'   => $this->getLayout()->createBlock('ves_brand/adminhtml_brand_edit_tab_meta')->toHtml(),
        ));
		/*
		$this->addTab('form_section_params', array(
            'label'     => Mage::helper('ves_brand')->__('Parameters'),
            'title'     => Mage::helper('ves_brand')->__('Parameters'),
            'content'   => $this->getLayout()->createBlock('ves_brand/adminhtml_brand_edit_tab_param')->toHtml(),
        ));
		*/
        return parent::_beforeToHtml();
    }
}