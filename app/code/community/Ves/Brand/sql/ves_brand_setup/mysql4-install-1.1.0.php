<?php
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('catalog_product', 'vesbrand', array(
	'label' => 'Product Brand',
	'type' => 'int',
	'input' => 'select',
	'source' => 'ves_brand/system_config_source_ListBrand',
	'visible' => true,
	'required' => false,
	'position' => 10,
));

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();
$installer->run("
 
CREATE TABLE IF NOT EXISTS `{$this->getTable('ves_brand/brand')}` (
  `brand_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `layout` varchar(250) NOT NULL,
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `file` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`brand_id`),
  UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- DROP TABLE IF EXISTS `{$this->getTable('ves_brand/brand_store')}`;
CREATE TABLE `{$this->getTable('ves_brand/brand_store')}` (
  `brand_id` int(10) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`brand_id`,`store_id`),
  CONSTRAINT `FK_BRAND_BRAND_STORE_THEME` FOREIGN KEY (`brand_id`) REFERENCES `{$this->getTable('ves_brand/brand')}` (`brand_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_BRAND_BRAND_STORE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Brand items to Stores';

");

 


$installer->endSetup();

