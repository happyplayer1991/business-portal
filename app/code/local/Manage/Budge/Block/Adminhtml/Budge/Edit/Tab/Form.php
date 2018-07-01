<?php
  
class Manage_Budge_Block_Adminhtml_Budge_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('budge_form', array('legend'=>Mage::helper('budge')->__('Budge information')));
        
        
        $storeId = Mage::app()->getStore(true)->getId();
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array(
                'wysiwyg'                     => true,
                'add_widgets'                 => true,
                'add_variables'               => true,
                'add_images'                  => true,
                'encode_directives'           => true,
                'store_id'                    => $storeId,
                'add_directives'              => true,
                'directives_url'              => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_wysiwyg/directive'),
                'files_browser_window_url'    => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
                'files_browser_window_width'  => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width'),
                'files_browser_window_height' => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height')
                )
            );
        

        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('budge')->__('Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));

        $fieldset->addField('icon', 'image', array(
            'label'     => Mage::helper('budge')->__('Icon'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'icon',
        ));
		
		$fieldset->addField('image', 'image', array(
            'label'     => Mage::helper('budge')->__('Image'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'image',
        ));

        $fieldset->addField('description', 'editor', array(
            'label'     => Mage::helper('budge')->__('Description'),
            'class'     => 'required-entry',
            'required'  => true,
            'style'     => 'width:98%; height:400px;',
            'wysiwyg'   => true,
            'name'      => 'description',
            'config'    => $config
        ));
        
        $fieldset->addField('value', 'text', array(
            'label'     => Mage::helper('budge')->__('Value'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'value',
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('budge')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('budge')->__('Enabled'),
                ),
  
                array(
                    'value'     => 0,
                    'label'     => Mage::helper('budge')->__('Disabled'),
                ),
            ),
        ));
        
        /*
        $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => Mage::helper('budge')->__('Content'),
            'title'     => Mage::helper('budge')->__('Content'),
            'style'     => 'width:98%; height:400px;',
            'wysiwyg'   => false,
            'required'  => true,
        ));
        */

        if ( Mage::getSingleton('adminhtml/session')->getBudgeData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBudgeData());
            Mage::getSingleton('adminhtml/session')->setBudgeData(null);
        } elseif ( Mage::registry('budge_data') ) {
            $form->setValues(Mage::registry('budge_data')->getData());
        }
        return parent::_prepareForm();
    }
} 