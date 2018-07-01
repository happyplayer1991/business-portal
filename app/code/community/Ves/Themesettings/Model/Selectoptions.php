<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_Themesettings
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Themesettings Extension
 *
 * @category   Ves
 * @package    Ves_Themesettings
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Themesettings_Model_Selectoptions extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    /**
     * Retrieve Full Option values array
     *
     * @param bool $withEmpty     Add empty option to array
     * @param bool $defaultValues
     *
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        if (!$this->_options) {
            $options = array();
            $options[] = array(
                'value' => '1',
                'label' => 'Category Template style 1',
                );
            $options[] = array(
                'value' => '2',
                'label' => 'Category Template style 2',
                );
            $options[] = array(
                'value' => '3',
                'label' => 'Category Template style 3',
                );
            $options[] = array(
                'value' => '4',
                'label' => 'Category Template style 4',
                );
            $options[] = array(
                'value' => '5',
                'label' => 'Category Template style 5',
                );
            $this->_options = $options;
        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, array(
                'value' => '',
                'label' => 'Category Default Template',
                ));
        }
        return $options;
    }
}