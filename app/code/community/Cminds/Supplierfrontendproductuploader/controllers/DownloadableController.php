<?php

class Cminds_Supplierfrontendproductuploader_DownloadableController extends Cminds_Supplierfrontendproductuploader_Controller_Action {
    public function downloadableDataAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        if($id == null) {
            $this->norouteAction();
            return;
        }

        $currentSupplierId = $this->_getHelper()->getSupplierId();

        if ($currentSupplierId === false) {
            $this->getResponse()->setRedirect(Mage::helper('customer')->getLoginUrl());
            return;
        }

        $product = Mage::getModel('catalog/product')->load($id);

        if($product->getData('creator_id') != $this->_getHelper()->getSupplierId()) {
            $this->norouteAction();
            return;
        }

        Mage::register("supplier_product_id", $id);

        $this->_renderBlocks(true);
    }

    public function downloadableDataPostAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        if($id == null) {
            $this->norouteAction();
            return;
        }

        $currentSupplierId = $this->_getHelper()->getSupplierId();

        if ($currentSupplierId === false) {
            $this->getResponse()->setRedirect(Mage::helper('customer')->getLoginUrl());
            return;
        }

        $product = Mage::getModel('catalog/product')->load($id);

        if($product->getData('creator_id') != $this->_getHelper()->getSupplierId()) {
            $this->norouteAction();
            return;
        }

        $this->saveSampleData();
        $this->saveLinkData();
        $linksPurchaseSeparatelyPostValue = $this->getRequest()->getPost("links_purchased_separately");

        $product->setLinksPurchasedSeparately($linksPurchaseSeparatelyPostValue);
        $product->getResource()->saveAttribute($product, 'links_purchased_separately');

        $this->getResponse()->setRedirect(Mage::getUrl("*/*/downloadableData", array("id" => $id)));
    }

    protected function handleSampleFileUpload($index)
    {
        $fileData = array(
            'name' => $_FILES['links']['name'][$index]['sample'],
            'type' => $_FILES['links']['type'][$index]['file'],
            'tmp_name' => $_FILES['links']['tmp_name'][$index]['sample'],
            'error' => $_FILES['links']['error'][$index]['sample'],
            'size' => $_FILES['links']['size'][$index]['sample']
        );
        if (!$fileData['name']) {
            return;
        }

        $productDirection = Mage_Downloadable_Model_Link::getBaseSamplePath() .'/'. $fileData['name'];
        $fileUpload = move_uploaded_file($fileData["tmp_name"], $productDirection);

        if (!$fileUpload) {
            Mage::throwException("");
        }

        return $productDirection;
    }

    protected function handleSampleUpload($index)
    {
        $fileData = array(
            'name' => $_FILES['sample']['name'][$index]['file'],
            'type' => $_FILES['sample']['type'][$index]['file'],
            'tmp_name' => $_FILES['sample']['tmp_name'][$index]['file'],
            'error' => $_FILES['sample']['error'][$index]['file'],
            'size' => $_FILES['sample']['size'][$index]['file']
        );
        if (!$fileData['name']) {
            return;
        }

        $productDirection = Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'files' . DS . 'samples' . DS . $fileData['name'];
        $fileUpload = move_uploaded_file($fileData["tmp_name"], $productDirection);

        if (!$fileUpload) {
            Mage::throwException("");
        }

        return $productDirection;
    }

    protected function handleLinksFileUpload($index)
    {
        $fileData = array(
            'name' => $_FILES['links']['name'][$index]['file'],
            'type' => $_FILES['links']['type'][$index]['file'],
            'tmp_name' => $_FILES['links']['tmp_name'][$index]['file'],
            'error' => $_FILES['links']['error'][$index]['file'],
            'size' => $_FILES['links']['size'][$index]['file']
        );
        if (!$fileData['name']) {
            return;
        }

        $productDirection = Mage_Downloadable_Model_Link::getBasePath() .'/'. $fileData['name'];
        $fileUpload = move_uploaded_file($fileData["tmp_name"], $productDirection);

        if (!$fileUpload) {
            Mage::throwException("");
        }

        return $productDirection;
    }

    protected function saveSampleData()
    {
        $sampleData = $this->getRequest()->getParam("sample");
        $id = $this->getRequest()->getParam("id", null);

        foreach ($sampleData as $index => $sampleDataItem) {
            if (is_int($index)) {
                $downloadableSampleObject = Mage::getModel('downloadable/sample')->load($index);
            } else {
                if($sampleDataItem['title'] == "") {
                    continue;
                }
                $downloadableSampleObject = Mage::getModel('downloadable/sample');
                $downloadableSampleObject->setProductId($id);
            }
            $filePath = $this->handleSampleUpload($index);
            $downloadableSampleObject->setTitle($sampleDataItem['title']);
            $downloadableSampleObject->setSortOrder($sampleDataItem['order']);
            $downloadableSampleObject->setSampleType($sampleDataItem['type']);
            if($sampleDataItem['type'] == "file") {
                if ($filePath) {
                   $downloadableSampleObject->setSampleFile(basename($filePath));
                }
            } else {
                $downloadableSampleObject->setSampleUrl($sampleDataItem['url']);

            }
            $downloadableSampleObject->save();

            unset($downloadableSampleObject);
        }
    }


    protected function saveLinkData()
    {
        $linksData = $this->getRequest()->getParam("links");
        $id = $this->getRequest()->getParam("id", null);

        foreach ($linksData as $index => $linkDataItem) {
            if($linkDataItem['title'] == "") {
                continue;
            }

            if(is_int($index)) {
                $downloadableLinkObject = Mage::getModel('downloadable/link')->load($index);
            } else {
                if($linkDataItem['title'] == "") {
                    continue;
                }

                $downloadableLinkObject = Mage::getModel('downloadable/link');
                $downloadableLinkObject->setProductId($id);
            }
            $sampleFile = $this->handleSampleFileUpload($index);
            $file = $this->handleLinksFileUpload($index);

            $downloadableLinkObject->setTitle($linkDataItem['title']);
            $downloadableLinkObject->setPrice($linkDataItem['price']);
            $downloadableLinkObject->setNumberOfDownloads($linkDataItem['max_downloads']);
            $downloadableLinkObject->setSample($linkDataItem['sample']);
            $downloadableLinkObject->setLinkType($linkDataItem['type']);
            $downloadableLinkObject->setSampleType($linkDataItem['sample_type']);

            if($linkDataItem['type'] == "url") {
                $downloadableLinkObject->setLinkUrl($linkDataItem['url']);
            } else {
                if ($file) {
                    $downloadableLinkObject->setLinkFile(basename($file));
                }
            }
            if($linkDataItem['sample_type'] == "url") {
                $downloadableLinkObject->setSampleUrl($linkDataItem['sample_url']);
            } else {
                if ($file) {
                    $downloadableLinkObject->setSampleFile(basename($sampleFile));
                }
            }
            $downloadableLinkObject->setSortOrder($linkDataItem['sort']);
            $downloadableLinkObject->save();

            unset($downloadableLinkObject);
        }
    }

    public function deleteSampleAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        if($id == null) {
            $this->norouteAction();
            return;
        }

        try {
            $downloadableSampleObject = Mage::getModel('downloadable/sample')->load($id);
            $downloadableSampleObject->delete();
            $response = array("success" => true);
        } catch(Exception $e) {
            $response = array("success" => false);
        }

        $this->sendResponse($response);
    }

    public function deleteLinkAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        if($id == null) {
            $this->norouteAction();
            return;
        }

        try {
            $downloadableLinkObject = Mage::getModel('downloadable/link')->load($id);
            $downloadableLinkObject->delete();
            $response = array("success" => true);
        } catch(Exception $e) {
            $response = array("success" => false);
        }

        $this->sendResponse($response);
    }
}