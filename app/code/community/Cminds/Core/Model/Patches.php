<?php

class Cminds_Core_Model_Patches extends Mage_Core_Model_Abstract
{
	public $appliedPatches = array();
	private $patchFile;

	public function __construct()
	{
		$this->patchFile = Mage::getBaseDir('etc') . DS . 'applied.patches.list';
		Mage::log(Mage::getBaseDir('etc') . DS . 'applied.patches.list');
		$this->loadPatchFile();
	}

	public function getPatches()
	{
		return $this->appliedPatches;
	}

	public function loadPatchFile()
	{
		$ioAdapter = new Varien_Io_File();
		if (!$ioAdapter->fileExists($this->patchFile)) {
		    return false;
		}

		$ioAdapter->open(array('path' => $ioAdapter->dirname($this->patchFile)));
		$ioAdapter->streamOpen($this->patchFile, 'r');

		while ($buffer = $ioAdapter->streamRead()) {
		    if(stristr($buffer,'|')){
		    	list($date, $patch) = array_map('trim', explode('|', $buffer));
		    	$this->appliedPatches[] = $patch;
		    }
		}
		$ioAdapter->streamClose();
	}
}