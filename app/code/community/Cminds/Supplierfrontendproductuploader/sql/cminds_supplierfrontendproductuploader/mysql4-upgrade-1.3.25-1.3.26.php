<?php
$installer = $this;
$installer->startSetup();

$installer->updateAttribute('customer','notification_product_ordered','default_value',1);

$installer->endSetup();