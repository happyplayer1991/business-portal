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

class Ves_Brand_Block_Adminhtml_Category_Edit_Tab_Param extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $_model = Mage::registry('category_data');
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('category_params', array('legend'=>Mage::helper('ves_brand')->__('Parameter')));
		
		$fieldset->addField('template', 'select', array(
			'label'     => Mage::helper('ves_brand')->__('Template'),
			'name'      => 'param[template]',
			'values'    => array( 0=> $this->__("No"), 1=> $this->__("Yes") )
		));
		
		$fieldset->addField('show_childrent', 'select', array(
			'label'     => Mage::helper('ves_brand')->__('Show Childrent'),
			'name'      => 'param[show_childrent]',
			'values'    => array( 0=> $this->__("No"), 1=> $this->__("Yes") )
		));
		
		$fieldset->addField('primary_cols', 'text', array(
			'label'     => Mage::helper('ves_brand')->__('Show Childrent'),
			'name'      => 'param[primary_cols]',
			'default'   => '2'
		));
		 
        
		if ( Mage::getSingleton('adminhtml/session')->getCategoryData() )
		  {
			  $form->setValues(Mage::getSingleton('adminhtml/session')->getCategoryData());
			  Mage::getSingleton('adminhtml/session')->getCategoryData(null);
		  } elseif ( Mage::registry('category_data') ) {
			  $form->setValues(Mage::registry('category_data')->getData());
		  }
        
        return parent::_prepareForm();
    }
	
	  public function getParentToOptionArray() {
		$catCollection = Mage::getModel('ves_brand/category')
					->getCollection();
		$id = Mage::registry('category_data')->getId();
		if($id) {
			$catCollection->addFieldToFilter('category_id', array('neq' => $id));
		}
		$option = array();
		$option[] = array( 'value' => 0, 
						   'label' => 'Top Level');
		foreach($catCollection as $cat) {
			$option[] = array( 'value' => $cat->getId(), 
							   'label' => $cat->getTitle() );
		}
		return $option;
    }
}
