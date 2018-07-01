<?php

class Cminds_Supplierfrontendproductuploader_Block_Product_Associated extends Cminds_Supplierfrontendproductuploader_Block_Product_Create
{
    private $_configurableProduct = null;

    public function getConfigurableModel()
    {
        if (!$this->_configurableProduct) {
            $requestParams = $this->getConfigurable();
            $this->_configurableProduct = Mage::getModel('supplierfrontendproductuploader/product_configurable');
            $this->_configurableProduct->setProduct($requestParams);
        }

        return $this->_configurableProduct;
    }

    public function getAvailableAttributeSets()
    {
        $s = Mage::getModel('eav/entity_attribute_set')->getCollection()->addFieldToFilter('available_for_supplier', 1);
        return $s;
    }

    public function getProductTypes()
    {
        $types = array(
            array(
                'label' => $this->__('Simple Product'),
                'value' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE
            ),
            array(
                'label' => $this->__('Configurable Product'),
                'value' => Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
            ),
            array(
                'label' => $this->__('Virtual Product'),
                'value' => Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL
            ),
            array(
                'label' => $this->__('Downloadable Product'),
                'value' => Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
            ),
        );
        return $types;
    }

    public function getAttributeSetId()
    {
        $requestParams = $this->getConfigurable();
        return $requestParams['attribute_set_id'];
    }

    public function getProductId()
    {
        return $this->getConfigurable()->getId();
    }

    public function getAttributes()
    {
        $configurableAttributesData = $this->getConfigurable()
            ->getTypeInstance()
            ->getConfigurableAttributesAsArray();
        return $configurableAttributesData;
    }

    public function getChildrenProducts()
    {
        $childProducts = Mage::getModel('catalog/product_type_configurable')
            ->getUsedProducts(null, $this->getConfigurable());

        return $childProducts;
    }

    public function getConfigurable()
    {
        return Mage::registry('product_object');
    }

    public function getChildrenProductIds()
    {
        $children = $this->getChildrenProducts();
        $ids = array();

        foreach ($children as $child) {
            $ids[] = $child->getId();
        }

        return $ids;
    }

    public function canSetValue($value_id)
    {
        return !$this->getConfigurableModel()->isValueUsed($value_id);
    }

    protected function _getSelectField($attribute, $data, $isMultiple = false)
    {
        $value = isset($data[$attribute->getAttributeCode()]) ? $data[$attribute->getAttributeCode()] : null;
        $isMultiple = ($isMultiple) ? " multiple" : "";
        $isMultipleStyle = ($isMultiple) ? " height:100px;" : "";
        $html = '<select name="' . $attribute->getAttributeCode() . '" style="'.$isMultipleStyle.'" class="required-entry associated-dropdown '. $attribute->getFrontend()->getClass() . '"'.$isMultiple.'>';
        $allOptions = $attribute->getSource()->getAllOptions(true);
        $html .= '<option value="">----------------</option>';
        $superAttributes = $this->getSuperAttributes();

        $alreadySet = array();
        foreach ($superAttributes as $superAttribute) {
            if (isset($superAttribute['attribute_id']) && $attribute->getId() == $superAttribute['attribute_id']) {
                foreach ($superAttribute['values'] as $values) {
                    $alreadySet[$values['value_index']] = $values['pricing_value'];
                }
            }
        }

        foreach ($allOptions as $option) {
            if ($option['value'] == '') continue;
            //if(!$this->canSetValue($option['value'])) continue;

            if (isset($alreadySet[$option['value']])) {
                $html .= '<option data-id="' . $alreadySet[$option['value']] . '" value="'.$option['value'].'" '.(($value == $option['label']) ? ' selected="selected"' : '').'>'.$option['label'] . ' + '. Mage::helper('core')->currency($alreadySet[$option['value']]) . '</option>';
            } else {
                $html .= '<option value="'.$option['value'].'" '.(($value == $option['label']) ? ' selected="selected"' : '').'>'.$option['label'] . '</option>';
            }
        }

        $html .= '</select>';
        return $html;
    }

    public function getNotAssociatedProducts()
    {
        $s = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('creator_id')
            ->addAttributeToFilter('type_id', 'simple')
            ->addAttributeToFilter('attribute_set_id', $this->getConfigurable()->getAttributeSetId())
            ->addAttributeToFilter('creator_id', Mage::helper('supplierfrontendproductuploader')->getSupplierId());

        $childrenIds = $this->getChildrenProductIds();

        if (count($childrenIds) > 0) {
            $s->addAttributeToFilter('entity_id', array('nin' => $childrenIds));
        }

        foreach ($s as $product) {
            if ($this->areOptionsExists($product)) {
                $s->removeItemByKey($product->getId());
            }
        }
        return $s;
    }

    public function areOptionsExists($simpleProduct)
    {
        $configurable_values = $this->getConfigurableModel()->getConfigurableProductValues();
        $product = Mage::getModel('catalog/product')->load($simpleProduct->getId());
        $superAttributes = $this->getConfigurableModel()->getSuperAttributes();
        $allAttributesCount = count($superAttributes);
        $matchedValuesCount = 0;
        foreach ($superAttributes as $attribute) {
            $simpleProductData = $product->getData($attribute['attribute_code']);
            
            if ($simpleProductData == null) {
                $matchedValuesCount++;
                continue;
            }

            foreach ($attribute['values'] as $value) {
                if ($value['value_index'] == $simpleProductData || !$simpleProductData) {
                    $matchedValuesCount++;
                }
            }
        }

        return ($matchedValuesCount >= $allAttributesCount);
    }

    public function getSuperAttributes()
    {
        return Mage::getModel('catalog/product')->load(
            $this->getRequest()->getParam('id')
        )->getTypeInstance()->getConfigurableAttributesAsArray();
    }

    public function getEditConfigurableUrl()
    {
        return Mage::getUrl(
            'supplierfrontendproductuploader/product/editConfigurable',
            array(
                'id' => $this->getConfigurable()->getId(),
                'type' => $this->getConfigurable()->getTypeId()
            )
        );
    }

    public function getSimpleProductEditUrl($product)
    {
        return Mage::getUrl(
            'supplierfrontendproductuploader/product/edit',
            array(
                'id' => $product->getId(),
                'type' => $product->getTypeId()
            )
        );
    }

    /**
     * Returns config setting
     * @return bool
     */
    public function canGenerateSku()
    {
        $dataHelper = Mage::helper("supplierfrontendproductuploader");
        return $dataHelper->canGenerateSku();
    }

    public function formatPrice($price)
    {
        return Mage::helper('core')->currency($price, true, false);
    }

    public function removeButtonLabel()
    {
        return $this->__("Remove Products");
    }

    public function unlinkButtonLabel()
    {
        return $this->__("Unlink Products");
    }

    public function updateQtyButtonLabel()
    {
        return $this->__("Update Stock");
    }
}
