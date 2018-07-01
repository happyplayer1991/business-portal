<?php

class Cminds_Supplierfrontendproductuploader_Model_Mysql4_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection {

    public function filterByFrontendproductStatus( $status ) {
        switch ( $status ) {
            case 'pending':
                $this->addAttributeToFilter( array(
                    array(
                        'attribute' => 'frontendproduct_product_status',
                        'eq'        => Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_PENDING
                    )
                ) );
                break;
            case 'active':
                $this->addAttributeToFilter( array(
                    array(
                        'attribute' => 'frontendproduct_product_status',
                        'eq'        => Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_APPROVED
                    )
                ) );
                break;
            case 'inactive':
                $this->addAttributeToFilter( array(
                    array(
                        'attribute' => 'frontendproduct_product_status',
                        'eq'        => Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_NONACTIVE
                    )
                ) );
                break;
            case 'disapproved':
                $this->addAttributeToFilter( array(
                    array(
                        'attribute' => 'frontendproduct_product_status',
                        'eq'        => Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_DISAPPROVED
                    )
                ) );
                break;
            default:
                break;
        }

        return $this;
    }

    public function filterBySupplier( $supplier_id ) {
        $this->addAttributeToFilter( array(
            array(
                'attribute' => 'creator_id',
                'eq'        => $supplier_id
            )
        ) );

        return $this;
    }

    public function filterByName($name)
    {
        if ($name) {
            $this->addFieldToFilter(array(
                array(
                    'attribute' => 'name',
                    'like'      => '%' . $name . '%'
                ),
                array(
                    'attribute' => 'sku',
                    'like'      => '%' . $name . '%'
                )
            ) );
        }

        return $this;
    }

    public function filterBySupplierCode(
        $supplierCode = null,
        $equal = false
    ) {

        $this->addFieldToFilter( "supplier_product_code",
            array( "neq" => '' ) );
        if ( $equal ) {
            $this->addFieldToFilter( "supplier_product_code",
                array( "eq" => $supplierCode ) );
        } else {
            $this->addFieldToFilter( "supplier_product_code",
                array( "like" => '%' . $supplierCode . '%' ) );
        }

        return $this;
    }

    public function filterBySku( $sku ) {
        if ( $sku ) {
            $this->addFieldToFilter( array(
                array(
                    'attribute' => 'sku',
                    'like'      => '%' . $sku . '%'
                )
            ) );
        }

        return $this;
    }

    public function setVisibilities() {
        $helper = Mage::helper( 'supplierfrontendproductuploader' );
        if ( $helper->isProductCodeEnabled() ) {
            $this->joinAttribute( 'supplier_product_code',
                'catalog_product/supplier_product_code', 'entity_id', null,
                'left', Mage::app()->getStore()->getId() );
            $this->joinAttribute( 'main_product_by_admin',
                'catalog_product/main_product_by_admin', 'entity_id', null,
                'left', Mage::app()->getStore()->getId() );
            $this->joinAttribute( 'sorting_level_codes',
                'catalog_product/sorting_level_codes', 'entity_id', null,
                'left', Mage::app()->getStore()->getId() );
            $this->joinAttribute( 'price', 'catalog_product/price', 'entity_id',
                null, 'left', Mage::app()->getStore()->getId() );
            $this->joinAttribute( 'creator_id', 'catalog_product/creator_id',
                'entity_id', null, 'left', Mage::app()->getStore()->getId() );
            $this->joinAttribute( 'frontendproduct_product_status',
                'catalog_product/frontendproduct_product_status', 'entity_id',
                null, 'left', Mage::app()->getStore()->getId() );
            $this->filterByFrontendproductStatus( 'active' );

            Mage::getSingleton( 'cataloginventory/stock' )->addInStockFilterToCollection( $this );
            $data = array();
            /**
             *  If there is more products with the same supplier_product_code get only one with the lowest price
             */
            foreach ( $this->getData() as $product ) {
                if ( isset( $product['supplier_product_code'] ) && $product['supplier_product_code'] ) {
                    switch ( $helper->sortClonedProductsBy() ) {
                        case Cminds_Supplierfrontendproductuploader_Model_Config_Source_Codes_Sort::LOWER_PRICE:
                            $data[ $product['entity_id'] ]['price'] = $product['price'];
                            break;
                        case Cminds_Supplierfrontendproductuploader_Model_Config_Source_Codes_Sort::SUPPLIER_RATINGS:
                            $ratingAvg = Mage::getModel( 'marketplace/rating' )->getSupplierAvgRating( $product['creator_id'] );
                            $data[ $product['entity_id'] ]['rating'] = - $ratingAvg;
                            break;
                        case Cminds_Supplierfrontendproductuploader_Model_Config_Source_Codes_Sort::SORT_LEVEL:
                            $data[ $product['entity_id'] ]['sorting_level'] = $product['sorting_level_codes'];
                            break;
                    }

                    if ( $product['main_product_by_admin'] == 1 ) {
                        $data['main_product_by_admin'] = $product['entity_id'];
                    }
                }
            }

            $arraysIds = array();
            $visible   = 0;
            if ( count( $data ) > 1 ) {
                if ( isset( $data['main_product_by_admin'] ) ) {
                    $visible = $data['main_product_by_admin'];
                    $value   = $data['main_product_by_admin'];
                    unset( $data['main_product_by_admin'] );
                    unset( $data[ $value ] );
                } else {
                    $visible = array_keys( $data, min( $data ) )[0];
                    unset( $data[ array_keys( $data, min( $data ) )[0] ] );
                }
                $arraysIds = array_keys( $data );
            }

            try {
                $stores = Mage::app()->getStores( true );
                $action = Mage::getModel( 'catalog/product_action' );

                foreach ( $stores AS $store ) {
                    if ( ( $store->getId() || $store->getId() == 0 ) && $visible ) {
                        $action->updateAttributes( $arraysIds, array(
                            'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE
                        ), $store->getId() );

                        $action->updateAttributes( array( $visible ), array(
                            'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
                        ), $store->getId() );
                    }
                }

            } catch ( Exception $ex ) {
                Mage::log( $ex->getMessage() );
                Mage::getSingleton( 'core/session' )->addError( $ex->getMessage() );
                Mage::app()->getFrontController()->getResponse()->setRedirect( Mage::getUrl( 'supplier/product/list/' ) );
            }
        }
    }
}