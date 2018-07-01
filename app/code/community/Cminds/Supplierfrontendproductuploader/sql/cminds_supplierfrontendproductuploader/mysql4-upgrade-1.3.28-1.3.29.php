<?php
$installer = $this;
$installer->startSetup();
$entity = $this->getEntityTypeId('catalog_product');

$this->addAttribute($entity, 'supplier_product_code', array(
    'type' => 'varchar',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'input'  => 'text',
    'visible' => true,
    'required' => false,
    'default' => 0,
    'label' => 'Supplier Product Code'
));
$installer->endSetup();