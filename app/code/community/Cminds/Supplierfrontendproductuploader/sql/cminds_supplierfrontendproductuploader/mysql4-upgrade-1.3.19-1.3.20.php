<?php

$installer = $this;

$installer->startSetup();
$table = $installer->getTable('eav_attribute');
$installer->getConnection()->addColumn(
        $table, 'available_for_supplier', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => false, 
            'comment' => 'Available For Supplier'
        ));
        
$installer->endSetup();

        