<?php
class Cminds_Supplierfrontendproductuploader_Model_Config_Source_Import_Categories
{
    const BY_NAME = 0;
    const BY_IDS = 1;
    protected $_options;

    public function getAllOptions()
    {
        $this->_options = array(
            array('label' => 'Category Name(s)', 'value' => self::BY_NAME),
            array('label' => 'Category Id(s)', 'value' => self::BY_IDS),
        );

        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
