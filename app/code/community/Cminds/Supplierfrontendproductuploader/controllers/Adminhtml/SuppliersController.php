<?php

class Cminds_Supplierfrontendproductuploader_Adminhtml_SuppliersController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/suppliers/supplier_grid');
    }

    public function indexAction()
    {
        $this->_title($this->__('Suppliers'));
        $this->loadLayout();
        $this->_setActiveMenu('suppliers');
        $this->_addContent($this->getLayout()->createBlock('supplierfrontendproductuploader/adminhtml_supplier_list'));
        $this->renderLayout();
    }

    public function massApproveAction()
    {
        $helper = Mage::helper('supplierfrontendproductuploader');
        $supplierIds = $this->getRequest()->getParam('id');
        if (!is_array($supplierIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                $helper->__('Please select supplier/s')
            );
        } else {
            try {
                $model = Mage::getModel('customer/customer');
                foreach ($supplierIds as $supplierId) {
                    $model->load($supplierId)->addData(array(
                        'supplier_approve' => 1
                    ));
                    $model->save();
                    /**
                     * Send mail to supplier when account was approved by admin (if needed)
                     */
                    if ($helper->isSupplierNeedsToBeApproved()) {
                        Mage::helper('supplierfrontendproductuploader/email')->notifySupplierWhenApproved($supplierId);
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $helper->__(
                        'Total of %d supplier(s) were approved.',
                        count($supplierIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massDissapproveAction()
    {
        $helper = Mage::helper('supplierfrontendproductuploader');
        $supplierIds = $this->getRequest()->getParam('id');
        if (!is_array($supplierIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('marketplace')->__('Please select supplier/s')
            );
        } else {
            try {
                $model = Mage::getModel('customer/customer');
                foreach ($supplierIds as $supplierId) {
                    $model->load($supplierId)->addData(array(
                        'supplier_approve' => 0
                    ));
                    $model->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $helper->__(
                        'Total of %d supplier(s) were dissapproved.',
                        count($supplierIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massRemoveAction()
    {
        $supplierIds = $this->getRequest()->getParam('id');

        if (!is_array($supplierIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('marketplace')->__('Please select supplier/s')
            );
        } else {

            try {
                $model = Mage::getModel('customer/customer');
                foreach ($supplierIds as $supplierId) {
                    $model->load($supplierId)->delete();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('marketplace')->__('Removed %d supplier(s).',
                        count($supplierIds))
                );
            } catch (Exception $error) {
                Mage::getSingleton('adminhtml/session')->addError($error->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function attributeSetsAction()
    {
        $customerId = (int) $this->getRequest()->getParam('id');
        $customer   = Mage::getModel('customer/customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);

        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock(
                    'supplierfrontendproductuploader/adminhtml_customer_edit_tab_attributesets',
                    'assigned_attribute_sets'
                )
                ->toHtml()
        );
    }
}