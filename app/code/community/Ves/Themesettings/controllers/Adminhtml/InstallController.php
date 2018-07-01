<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_Themesettings
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Themesettings Extension
 *
 * @category   Ves
 * @package    Ves_Themesettings
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Themesettings_Adminhtml_InstallController extends Mage_Adminhtml_Controller_Action{
	public function importAction(){
		$ves_import = Mage::helper('themesettings/import');
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$readConnection = $resource->getConnection('core_read');
		$action_type = $this->getRequest()->getParam('action_type');
		$data = $this->getRequest()->getParams();
		if(isset($action_type) && $action_type == 'import' && $data = $this->getRequest()->getParams()){
			$filePath = '';
			$fileContent = '';
			if(isset($_FILES['data_import_file']['name']) && $_FILES['data_import_file']['name'] != '')
			{	
				$fileContent = file_get_contents($_FILES['data_import_file']['tmp_name']);
			}else{
				$theme_folder = $data['theme_folder'];
				if(isset($data[$theme_folder])){
					$filePath = $data[$theme_folder];
				}
				if($filePath!=''){
					$fileContent = file_get_contents($filePath);
				}
			}

			$importData = Mage::helper('core')->jsonDecode($fileContent);
			$store_id = $data['stores'];
			$overwrite = false;
			if($data['overwrite_blocks']){
				$overwrite = true;
			}
			if($importData!=''){
				try{
					foreach ($importData as $k => $sourceType) {
						if(!is_array($sourceType)) continue;
						foreach ($sourceType as $key => $source) {

							if($key == 'system_config'){
								foreach ($source as $section => $sections) {
									foreach ($sections as $column => $columns) {
										foreach ($columns as $field => $val) {
											$path = $section.'/'.$column.'/'.$field;

											if( $k == 'cmspages'){
												if($field == 'cms_home_page'){
													$page = Mage::getModel('cms/page')->getCollection()->addFieldToFilter('identifier',$val);
													if($page && $val!=Mage::getStoreConfig($path,$store_id)){
														Mage::getConfig()->saveConfig($path, $val, "stores", (int)$store_id );
													}
												}
											}
											if( $k != 'cmspages' && $val!=Mage::getStoreConfig($path,$store_id)){
												if($store_id==0){
													Mage::getConfig()->saveConfig($path, $val, "default", (int)$store_id );	
												}else{
													Mage::getConfig()->saveConfig($path, $val, "stores", (int)$store_id );
												}
											}
										}
									}

									if($section=='themesettings'){
										Mage::unregister('ves_store');
										$store = Mage::getModel('core/store')->load($store_id);
										$storeCode = $store->getCode();
										$generator = Mage::getModel('themesettings/cssgen_generator');
										$cookie = Mage::getModel('core/cookie');
										$cookie->delete('vespaneltool');
										$generator->generateStoreCss($storeCode);
									}
								}
							}

							if($key == 'tables'){
								foreach ($source as $tableName =>  $table) {
									$table_name = $resource->getTableName($tableName);
									if($table_name){
										$writeConnection->query("SET FOREIGN_KEY_CHECKS=0;");
										/*
										if(!$overwrite) {
											$writeConnection->query("TRUNCATE `".$table_name."`");
										}
										if($overwrite) {
										// Overide CMS Page, Static Block
											if( $k == 'cmspages' && $tableName == 'cms/page_store' ){
												$writeConnection->query(" DELETE FROM ".$table_name." WHERE page_id = ".$row['page_id']);
											}
											if( $k == 'staticblocks' && $tableName == 'cms/block_store' ){
												$writeConnection->query(" DELETE FROM ".$table_name." WHERE block_id = ".$row['block_id']);
											}
										}*/
										foreach ($table as $row) {
											$where = '';
											$query_data = $ves_import->buildQueryImport($row, $table_name, $overwrite, $store_id);
											$writeConnection->query($query_data[0].$where, $query_data[1]);
										}
										$writeConnection->query("SET FOREIGN_KEY_CHECKS=1;");
									}
								}
							}
						}
					}

					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('themesettings')->__('Import successfully'));

				}catch(Exception $e){

					Mage::logException($e);
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('An Error occured importing file.'));
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					
				}
			}
		}
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('themesettings/adminhtml_install_import_edit'));
		$this->renderLayout();
	}

	public function exportAction(){
		$this->loadLayout();
		$data = $this->getRequest()->getParams();
		if($data && isset($data['action_type']) && $data['action_type'] == 'export'){
			$backupFileContent = array();
			$ves = Mage::helper('themesettings');
			$ves_export = Mage::helper('themesettings/export');

			// Export Modules
			if($exportModules = $ves_export->exportModules($data))
				$backupFileContent['modules'] = $exportModules;

			// Export Widgets
			if($exportWidgets = $ves_export->exportWidgets($data))
				$backupFileContent['widgets'] = $exportWidgets;

			// Export CMS Pages
			if($exportCmsPages = $ves_export->exportCmsPages($data))
				$backupFileContent['cmspages'] = $exportCmsPages;

			// Export Static Blocks
			if($exportStaticBlocks = $ves_export->exportStaticBlocks($data))
				$backupFileContent['staticblocks'] = $exportStaticBlocks;

			if(!empty($backupFileContent)){
				$folderTheme = '';
				if(isset($data['folder'])){
					$folderTheme = str_replace("/", DS, $data['folder']) ;
				}
				$importDir = Mage::getBaseDir('skin') . DS . 'frontend'. DS . $folderTheme . DS . 'import';

				$file_name = str_replace(" ", "_", $data['file_name']).'.json';
				$backupFileContent['created_at'] = date("m/d/Y h:i:s a", Mage::getModel('core/date')->timestamp(time()));
				$backupFileContent = Mage::helper('core')->jsonEncode($backupFileContent);

				if($data['isdowload']){
					$this->_sendUploadResponse($file_name, $backupFileContent);
				}else{
					$filePath = $importDir. DS . $file_name;
					try{
						$ves_export->writeSampleDataFile($importDir, $file_name, $backupFileContent);
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('themesettings')->__('Successfully exported to file %s',$filePath));
					}catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('themesettings')->__('Can not save import sample file "%s".', $filePath));
						Mage::logException($e);
					}
				}
				$this->_redirect('*/*/export');
			}
		}
		$this->_addContent($this->getLayout()->createBlock('themesettings/adminhtml_install_export_edit'));
		$this->renderLayout();
	}

	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
	{
		$response = $this->getResponse();
		$response->setHeader('HTTP/1.1 200 OK','');
		$response->setHeader('Pragma', 'public', true);
		$response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
		$response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
		$response->setHeader('Last-Modified', date('r'));
		$response->setHeader('Accept-Ranges', 'bytes');
		$response->setHeader('Content-Length', strlen($content));
		$response->setHeader('Content-type', $contentType);
		$response->setBody($content);
		$response->sendResponse();
		die;
	}
}