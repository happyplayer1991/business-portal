<?php
class Ves_Themesettings_Model_System_Config_Backend_Header_File extends Mage_Adminhtml_Model_System_Config_Backend_File
{
    /**
     * Upload max file size in kilobytes
     *
     * @var int
     */
    protected $_maxFileSize = 0;

    /**
     * Save uploaded file before saving config value
     *
     * @return Mage_Adminhtml_Model_System_Config_Backend_File
     */
    protected function _beforeSave()
    {
    	$value = $this->getValue();
    	if (isset($_FILES['groups']) && isset($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'])){

    		$uploadDir = $this->_getUploadDir();


            $file = array();
            $tmpName = $_FILES['groups']['tmp_name'];
            $file['tmp_name'] = $tmpName[$this->getGroupId()]['fields'][$this->getField()]['value'];
            $name = $_FILES['groups']['name'];
            $file['name'] = $name[$this->getGroupId()]['fields'][$this->getField()]['value'];

            if($file['name']!=''){
                try {
                    $uploader = new Mage_Core_Model_File_Uploader($file);
                    $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                    $uploader->setAllowRenameFiles(true);
                    $uploader->addValidateCallback('size', $this, 'validateMaxSize');
                    $result = $uploader->save($uploadDir);
                } catch (Exception $e) {
                    Mage::throwException($e->getMessage());
                    return $this;
                }
                $filename = $result['file'];
                if ($filename) {
                    if ($this->_addWhetherScopeInfo()) {
                        $filename = $this->_prependScopeInfo($filename);
                    }
                    $this->setValue($filename);
                }
            }
        } else {
            if (is_array($value) && !empty($value['delete'])) {
                $this->delete();
                $this->_dataSaveAllowed = false;
            } else {
                $this->unsValue();
            }
        }
        return $this;
    }

    /**
     * Getter for allowed extensions of uploaded files.
     *
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return array('png', 'gif', 'jpg', 'jpeg');
    }

}