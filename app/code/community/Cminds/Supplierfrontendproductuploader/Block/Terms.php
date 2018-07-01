<?php

class Cminds_Supplierfrontendproductuploader_Block_Terms extends Mage_Core_Block_Template
{
    /**
     * Get post action url.
     *
     * @return string
     */
    public function getActionUrl()
    {
        return Mage::getUrl('supplier/index/agreeTerms');
    }
}
