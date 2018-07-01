<?php
  
class Manage_Budge_Adminhtml_BudgeController extends Mage_Adminhtml_Controller_Action
{
  
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('budge/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }  
    
    public function indexAction() {
        $this->_title($this->__('Budge Manager'));
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('budge/adminhtml_budge'));
        $this->renderLayout();
    }
  
    public function editAction()
    {
        $budgeId     = $this->getRequest()->getParam('id');
        $budgeModel  = Mage::getModel('budge/budge')->load($budgeId);
  
        if ($budgeModel->getId() || $budgeId == 0) {
  
            Mage::register('budge_data', $budgeModel);
  
            $this->loadLayout();
            $this->_setActiveMenu('budge/items');
            
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
            
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            
            $this->_addContent($this->getLayout()->createBlock('budge/adminhtml_budge_edit'))
                 ->_addLeft($this->getLayout()->createBlock('budge/adminhtml_budge_edit_tabs'));
                
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('budge')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {
                $postData = $this->getRequest()->getPost();
                $budgeModel = Mage::getModel('budge/budge');
                if(isset($_FILES['icon']['name']) && $_FILES['icon']['name'] != '') {
                    try {
                        /* Starting upload */	
                        $uploader = new Varien_File_Uploader('icon');
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $path = Mage::getBaseDir('media') . DS . 'budges' . DS;
                        $destFile = $path.'/'.$_FILES['icon']['name'];

                        $filename = $uploader->getNewFileName($destFile);
                        $uploader->save($path, $filename);
                        $icon_img ='budges/'.$filename;

                    } catch (Exception $e) {
                  
                    }	
                } elseif((isset($postData['icon']['delete']) && $postData['icon']['delete'] == 1)){
                    //can also delete file from fs
                    unlink(Mage::getBaseDir('media') . DS . $postData['icon']['value']);
                    //set path to null and save to database
                    $postData['icon'] = "";
                    $icon_img = "";
                } else {
                    $postData['icon'] = isset($postData['icon']['value'])?$postData['icon']['value']:"";
                }
                
                if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
                    try {
                        /* Starting upload */	
                        $uploader = new Varien_File_Uploader('image');
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $path = Mage::getBaseDir('media') . DS . 'budges' . DS;
                        $destFile = $path.'/'.$_FILES['image']['name'];

                        $filename = $uploader->getNewFileName($destFile);
                        $uploader->save($path, $filename);
                        $image_img ='budges/'.$filename;

                    } catch (Exception $e) {
                  
                    }	
                } elseif((isset($postData['image']['delete']) && $postData['image']['delete'] == 1)){
                    //can also delete file from fs
                    unlink(Mage::getBaseDir('media') . DS . $postData['image']['value']);
                    //set path to null and save to database
                    $postData['image'] = "";
                    $image_img = "";
                } else {
                    $postData['image'] = isset($postData['image']['value'])?$postData['image']['value']:"";
                }
                
                $budgeModel->setId($this->getRequest()->getParam('id'))
                    ->setName($postData['name'])
                    ->setDescription($postData['description'])
                    ->setValue($postData['value'])
                    ->setStatus($postData['status'])
                    ->setIcon($icon_img)
                    ->setImage($image_img)
                    ->save();
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setBudgeData(false);
                
                		// save rewrite url
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setBudgeData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $budgeModel = Mage::getModel('budge/budge');
                
                $budgeModel->setId($this->getRequest()->getParam('id'))
                    ->delete();
                    
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massStatusAction() {
        $IDList = $this->getRequest()->getParam('budge');
        if(!is_array($IDList)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select record(s)'));
        } else {
            try {
                foreach ($IDList as $itemId) {
                    $_model = Mage::getSingleton('budge/budge')
                            ->setIsMassStatus(true)
                            ->load($itemId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($IDList))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

	public function massDeleteAction() {
        $IDList = $this->getRequest()->getParam('budge');
        if(!is_array($IDList)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select record(s)'));
        } else {
            try {
                foreach ($IDList as $itemId) {
                    $_model = Mage::getModel('budge/budge')
                            ->setIsMassDelete(true)->load($itemId);

                    $stores = $_model->getStoreId();
					if($stores && isset($stores[0]) && $stores[0]) {
	            		foreach($stores as $store_id) {
	            			Mage::getModel('core/url_rewrite')->loadByIdPath('budge/budge/'.$itemId."/store_id/".$store_id)->delete();
	            		}
	            		
	            	}

	            	Mage::getModel('core/url_rewrite')->loadByIdPath('venusbrand/brand/'.$itemId)->delete();

                    $_model->delete();
                }


                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($IDList)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Product grid for AJAX request.
     * Sort and filter result for example.
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('budge/adminhtml_budge_grid')->toHtml()
        );
    }

        /**
     * 
     */
    public function getBudgesAction()
    {
        $mediaurl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $budges = Mage::getModel('budge/budge')->getCollection();

        $budges_data = array();
        foreach($budges as $budge):
            $data = $budge->getData();
            $icon = $data['icon'];
            /* make children data of root node */
            $tree_data = array(
                'id' => $data['budge_id'],
                'text' => $data['name'],
                'type' => "child",
                'icon' => $mediaurl . $icon,
                'state' => array(
                    'selected' => false
                    )  
            );
            array_push($budges_data, $tree_data);
        endforeach;
        echo json_encode($budges_data);
    }
} 