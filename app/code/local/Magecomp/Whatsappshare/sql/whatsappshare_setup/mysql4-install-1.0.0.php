<?php
/**
 * Magento Whatsappshare extension
 *
 * @category   Magecomp
 * @package    Magecomp_Whatsappshare
 * @author     Magecomp
 */
$this->startSetup();
$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'whatsapp_category', array(
    'group'         => 'General Information',
    'input'         => 'select',
    'source'        => 'eav/entity_attribute_source_boolean',
    'label'         => 'WhatsApp Share',
    'backend'       => '',
    'visible'       => true,
    'required'      => false,
    'visible_on_front' => true,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
 
$this->endSetup();

?>
