<?php
$installer = $this;
$installer->startSetup();
$installer->updateAttribute('catalog_product','creator_id','used_in_product_listing', 1);
$installer->endSetup();