<?php

$installer = $this;

$installer->startSetup();
$tableExist = Mage::getSingleton('core/resource')
    ->getConnection('core_write')
    ->isTableExists(trim($this->getTable('supplierfrontendproductuploader/attributesets'), '`'));

if (!$tableExist) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('supplierfrontendproductuploader/attributesets'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Id')
        ->addColumn('supplier_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
        ), 'Customer ID')
        ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
        ), 'Vendor ID');
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
