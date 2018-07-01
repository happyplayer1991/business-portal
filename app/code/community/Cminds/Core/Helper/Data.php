<?php
class Cminds_Core_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getPatches()
    {
        if(Mage::getModel('cminds/patches')->loadPatchFile() !== false) {
            $patches = Mage::getModel('cminds/patches')->getPatches();
            $patch = array_search("SUPEE-6788", $patches);

            return $patch;
        } else {
            return false;
        }
    }
}