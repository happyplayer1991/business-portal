<?php

class Cminds_Supplierfrontendproductuploader_Block_Product_Downloadable extends Cminds_Supplierfrontendproductuploader_Block_Product_Create
{
    /**
     * @return false|Mage_Downloadable_Model_Product_Type
    */
    public function getTypeModel()
    {
        return Mage::getModel("downloadable/product_type");
    }

    /**
     * @return Mage_Downloadable_Model_Mysql4_Sample_Collection
    */
    public function getSamples()
    {
        return $this->getTypeModel()->getSamples($this->getProduct());
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->getTypeModel()->getLinks($this->getProduct());
    }

    /**
     * @return string
     */
    public function getDeleteSampleUrl()
    {
        return Mage::getUrl("supplier/downloadable/deleteSample");
    }

    /**
     * @return string
     */
    public function getDeleteLinkUrl()
    {
        return Mage::getUrl("supplier/downloadable/deleteLink");
    }

    /**
     * @return string
     */
    public function getSubmitUrl()
    {
        return Mage::getUrl("supplier/downloadable/downloadableDataPost");
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    public function getLinkFileName($name)
    {
        $name = explode("/", $name);
        return $name[count($name)-1];
    }

}
