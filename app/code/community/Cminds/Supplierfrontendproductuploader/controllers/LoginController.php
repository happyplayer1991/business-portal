<?php

class Cminds_Supplierfrontendproductuploader_LoginController extends Cminds_Supplierfrontendproductuploader_Controller_Action {
    protected $forceHeader = true;
    protected $forceFooter = true;
    public function preDispatch() {
        parent::preDispatch();

        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            if (!$this->_getHelper()->hasAccess()) {
                if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $this->getResponse()->setRedirect($this->_getHelper()->getSupplierLoginPage());
                } else {
                    $this->getResponse()->setRedirect('/');
                }
            } else {
                $this->getResponse()->setRedirect(Mage::getUrl('supplier'));
            }
        }
    }

    public function indexAction() {

        if(Mage::getStoreConfig('supplierfrontendproductuploader_catalog/login/use_separated_login') != 1) {
            $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
            $this->getResponse()->setHeader('Status','404 File not found');
            $this->_forward('defaultNoRoute');
            return;
        }
        $this->_renderBlocks();
    }

    public function loginAction() {
        if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $session = Mage::getSingleton('customer/session');

            if ($this->getRequest()->isPost()) {
                $login = $this->getRequest()->getPost();
                if (!empty($login['email']) && !empty($login['password'])) {
                    try {
                        $session->login($login['email'], $login['password']);
                        if ($session->getCustomer()->getIsJustConfirmed()) {
                            $this->_redirect(Mage::getUrl('supplier'));
                        }
                        Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl().'supplier/index/index/');
                    } catch (Mage_Core_Exception $e) {
                        $session->addError($e->getMessage());
                        $session->setUsername($login['email']);
                        $this->_redirect('*');
                    } catch (Exception $e) {
                        if($e->getCode() == 1022) {
                            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl().'supplier/login/index/');
                        } else {
                            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl().'supplier/login/index/');
                        }
                    }
                } else {
                    $session->addError($this->__('Login and password are required.'));
                    Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl().'supplier/login/index/');
                }
            }
        } else {
            $this->_redirect(Mage::getUrl('supplier'));
        }
    }    
}
