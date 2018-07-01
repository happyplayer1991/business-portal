<?php

    class Cminds_Supplierfrontendproductuploader_Model_Config_Downloadable_DownloadableTypes {
        public function toOptionArray() {

            
            $uploader = array(
                array('label'=>'jpg', 'value'=>'jpg'),
                array('label'=>'jpeg', 'value'=>'jpeg'),
                array('label'=>'pdf', 'value'=>'pdf'),
                array('label'=>'png', 'value'=>'png'),
                array('label'=>'gif', 'value'=>'gif'),
                array('label'=>'csv', 'value'=>'csv'),
                array('label'=>'zip', 'value'=>'zip'),
                );
            return $uploader;
            
        }
    }

