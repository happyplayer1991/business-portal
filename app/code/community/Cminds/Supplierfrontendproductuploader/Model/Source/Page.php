<?php

class Cminds_Supplierfrontendproductuploader_Model_Source_Page extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array();
            $additional['value'] = 'page_id';
            $additional['label'] = 'title';

            $cmsPageCollection = Mage::getResourceModel('cms/page_collection');
            foreach ($cmsPageCollection as $item) {
                foreach ($additional as $code => $field) {
                    $data[$code] = $item->getData($field);
                }
                $this->_options[] = $data;
            }

            array_unshift(
                $this->_options,
                array('value'=>'', 'label'=>Mage::helper('catalog')->__('Please select a static page ...'))
            );
        }
        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
