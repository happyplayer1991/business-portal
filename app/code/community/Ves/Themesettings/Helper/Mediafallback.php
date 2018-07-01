<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class implementing the media fallback layer for swatches
 */
class Ves_Themesettings_Helper_Mediafallback extends Mage_ConfigurableSwatches_Helper_Mediafallback
{
    const MEDIA_GALLERY_ATTRIBUTE_CODE = 'media_gallery';

    /**
     * For given product, get configurable images fallback array
     * Depends on following data available on product:
     * - product must have child attribute label mapping attached
     * - product must have media gallery attached which attaches and differentiates local images and child images
     * - product must have child products attached
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $imageTypes - image types to select for child products
     * @return array
     */
    public function getConfigurableImagesFallbackArray2(Mage_Catalog_Model_Product $product, array $imageTypes,
        $keepFrame = false, $imageWidth = null, $imageHeight = null
        ) {
        if (!$product->hasConfigurableImagesFallbackArray()) {
            $mapping = $product->getChildAttributeLabelMapping();

            $mediaGallery = $product->getMediaGallery();

            if (!isset($mediaGallery['images'])) {
                return array(); //nothing to do here
            }

            // ensure we only attempt to process valid image types we know about
            $imageTypes = array_intersect(array('image', 'small_image'), $imageTypes);

            $imagesByLabel = array();
            $imageHaystack = array_map(function ($value) {
                return Mage_ConfigurableSwatches_Helper_Data::normalizeKey($value['label']);
            }, $mediaGallery['images']);

            // load images from the configurable product for swapping
            foreach ($mapping as $map) {
                $imagePath = null;

                //search by store-specific label and then default label if nothing is found
                $imageKey = array_search($map['label'], $imageHaystack);
                if ($imageKey === false) {
                    $imageKey = array_search($map['default_label'], $imageHaystack);
                }

                //assign proper image file if found
                if ($imageKey !== false) {
                    $imagePath = $mediaGallery['images'][$imageKey]['file'];
                }

                $imagesByLabel[$map['label']] = array(
                    'configurable_product' => array(
                        Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_SMALL => null,
                        Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_BASE => null,
                        ),
                    'products' => $map['product_ids'],
                    );

                $ves = Mage::helper('themesettings');
                $aspect_ratio = $ves->getConfig('category_product/aspect_ratio');
                $imageWidth = (int)$ves->getConfig('category_product/image_width');
                $imageHeight = $imageWidth;
                //If image width is not specified, use default values
                if ($imageWidth <= 0){
                    $imageWidth = 295;
                    $imageHeight = 295;
                }
                if($aspect_ratio){
                    $imageHeight = 0;
                    $catViewKeepFrame = FALSE;
                }else{
                    $catViewKeepFrame = TRUE;
                }
                if (Mage::registry('catViewKeepFrame') === NULL){
                    Mage::register('catViewKeepFrame', $catViewKeepFrame);
                }

                if ($imagePath) {
                    $imagesByLabel[$map['label']]['configurable_product']
                    [Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_SMALL] =
                    $this->_resizeProductImage2($product, 'small_image', $keepFrame, $imagePath, false, $imageWidth, $imageHeight);

                    $imagesByLabel[$map['label']]['configurable_product']
                    [Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_BASE] =
                    $this->_resizeProductImage2($product, 'image', $keepFrame, $imagePath, false, $imageWidth, $imageHeight);
                }
            }

            $imagesByType = array(
                'image' => array(),
                'small_image' => array(),
                );

            // iterate image types to build image array, normally one type is passed in at a time, but could be two
            foreach ($imageTypes as $imageType) {
                // load image from the configurable product's children for swapping
                /* @var $childProduct Mage_Catalog_Model_Product */
                if ($product->hasChildrenProducts()) {
                    foreach ($product->getChildrenProducts() as $childProduct) {
                        if ($image = $this->_resizeProductImage2($childProduct, $imageType, $keepFrame, null, false, $imageWidth, $imageHeight)) {
                            $imagesByType[$imageType][$childProduct->getId()] = $image;
                        }
                    }
                }

                // load image from configurable product for swapping fallback
                if ($image = $this->_resizeProductImage2($product, $imageType, $keepFrame, null, true, $imageWidth, $imageHeight)) {
                    $imagesByType[$imageType][$product->getId()] = $image;
                }
            }

            $array = array(
                'option_labels' => $imagesByLabel,
                Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_SMALL => $imagesByType['small_image'],
                Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_BASE => $imagesByType['image'],
                );

            $product->setConfigurableImagesFallbackArray($array);
        }

        return $product->getConfigurableImagesFallbackArray();
    }

    /**
     * Resize specified type of image on the product for use in the fallback and returns the image URL
     * or returns the image URL for the specified image path if present
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $type
     * @param bool $keepFrame
     * @param string $image
     * @param bool $placeholder
     * @return string|bool
     */
    protected function _resizeProductImage2($product, $type, $keepFrame, $image = null, $placeholder = false, $imageWidth = null, $imageHeight = null)
    {
        $hasTypeData = $product->hasData($type) && $product->getData($type) != 'no_selection';
        if ($image == 'no_selection') {
            $image = null;
        }
        if ($hasTypeData || $placeholder || $image) {
            $helper = Mage::helper('catalog/image')
            ->init($product, $type, $image)
                ->keepFrame(($hasTypeData || $image) ? $keepFrame : false)  // don't keep frame if placeholder
                ;

                if((!$imageWidth && !$imageHeight) || ($imageWidth && !$imageHeight)) {
                    if($imageWidth) {
                        $size = $imageWidth;
                    } else {
                        $size = Mage::getStoreConfig(Mage_Catalog_Helper_Image::XML_NODE_PRODUCT_BASE_IMAGE_WIDTH);
                        if ($type == 'small_image') {
                            $size = Mage::getStoreConfig(Mage_Catalog_Helper_Image::XML_NODE_PRODUCT_SMALL_IMAGE_WIDTH);
                        }
                    }

                    $helper = Mage::helper('catalog/image')
                    ->init($product, $type, $image)
                    ->keepAspectRatio(true)
                    ->keepFrame(false);

                    if (is_numeric($size)) {
                        $helper->constrainOnly(false)->resize(370);
                    }
                } else {
                    $helper->constrainOnly(false)->resize($imageWidth, $imageHeight);
                }

                return (string)$helper;
            }
            return false;
        }

    }
