<?php

class Cminds_Supplierfrontendproductuploader_Block_Import_Products extends Mage_Core_Block_Template
{
    public function _construct()
    {
        $this->setTemplate('supplierfrontendproductuploader/import/products.phtml');
    }

    public function getReport()
    {
        return Mage::registry('import_data');
    }

    public function isCsvExists()
    {
        return (is_array($this->getReport()) && count($this->getReport()) > 0);
    }

    public function getAmountOfUploadsSucceeded()
    {
        $success = array();


        foreach ($this->getReport() AS $report) {
            if (!$report['success']) {
                continue;
            }
            $success[] = $report;
        }

        return $success;
    }

    public function getFailed()
    {
        $failed = array();

        foreach ($this->getReport() AS $report) {
            if ($report['success']) {
                continue;
            }
            $failed[] = $report;
        }

        return $failed;
    }

    public function getMaxImagesCount()
    {
        return Mage::helper('supplierfrontendproductuploader')->getMaxImages();
    }

    public function isUploadDone()
    {
        return Mage::registry('upload_done');
    }

    public function getSelectedAttributeSetId()
    {
        return Mage::registry('attributeSetId');
    }
}
