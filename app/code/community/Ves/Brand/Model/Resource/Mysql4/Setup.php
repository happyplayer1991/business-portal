<?php 

class Ves_Brand_Model_Resource_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup {

    public function getDefaultEntities() { die( "ha cong tien" );
        return array(
            'catalog_product' => array(
                'entity_model' => 'catalog/product',
                'attribute_model' => 'catalog/resource_eav_attribute',
                'table' => 'catalog/product',
                'additional_attribute_table' => 'catalog/eav_attribute',
                'entity_attribute_collection' => 'catalog/product_attribute_collection',
                'attributes' => array(
                    'vesbrand' => array(
                        'group' => 'General',
                        'label' => 'Product Brand',
                        'type' => 'int',
                        'input' => 'select',
                        'default' => '0',
                        'class' => '',
                        'backend' => 'brands/product_attribute_backend_unit',
                        'frontend' => '',
                        'source' => 'brands/product_attribute_source_unit',
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => true,
                        'searchable' => true,
                        'filterable' => 1,
                        'is_filterable' => 1,
                        'is_filterable_in_search' => 1,
                        'comparable' => true,
                        'visible_on_front' => false,
                        'visible_in_advanced_search' => true,
                        'unique' => false
                    )
                )
            ),
        );
    }

}
?>