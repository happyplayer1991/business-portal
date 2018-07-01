<?php
class Cminds_Core_Adminhtml_CmindsController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('config/cmindsConf');
    }

    public function deactivateLicenseAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $id = str_replace('_is_approved', '', $id);
        $id = str_replace('row_cmindsConf_', '', $id);

        $result = false;
        if ($id) {
            Mage::getModel('cminds/deactivate')->run($id);
            $result = true;
        }
        
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json');
        $response->setBody(Mage::helper('core')->jsonDecode($result));
    }
}