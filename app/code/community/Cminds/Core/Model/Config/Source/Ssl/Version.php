<?php
class Cminds_Core_Model_Config_Source_Ssl_Version {
    public function toOptionArray() {
        $allSet = array(
            array('value' => 0, 'label' => "Default"),
            array('value' => 1, 'label' => "TLSv1"),
            array('value' => 2, 'label' => "SSLv2"),
            array('value' => 3, 'label' => "SSLv3"),
            array('value' => 4, 'label' => "TLSv1_0"),
            array('value' => 5, 'label' => "TLSv1_1"),
            array('value' => 6, 'label' => "TLSv1_2"),
        );

        return $allSet;
    }
}
