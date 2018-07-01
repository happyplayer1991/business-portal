<?php
$installer = $this;
$installer->startSetup();
$entity = $this->getEntityTypeId('catalog_product');
$installer->updateAttribute($entity,'supplier_product_code','available_for_supplier',1);

$this->addAttribute($entity, 'created_using_code', array(
    'type' => 'int',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'input'  => 'text',
    'visible' => false,
    'required' => false,
    'default' => 0,
    'label' => 'Created with Product Code'
));

$this->addAttribute($entity, 'main_product_by_admin', array(
    'type' => 'int',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'input'  => 'text',
    'visible' => false,
    'required' => false,
    'default' => 0,
    'label' => 'Main product set by Admin'
));

$this->addAttribute($entity, 'sorting_level_codes', array(
    'type' => 'int',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'input'  => 'text',
    'visible' => true,
    'required' => false,
    'default' => 0,
    'label' => 'Sorting Level by Supplier Codes'
));
$installer->endSetup();