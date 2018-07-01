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
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Blog Extension
 *
 * @category   Ves
 * @package    Ves_Blog
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Brand_Model_System_Config_Source_Listcolumns
{

  public function toOptionArray()
  {

    $output = array();
    $output[] = array("value"=>"" , "label" => Mage::helper('adminhtml')->__("Auto"));
    $output[] = array("value"=>"1" , "label" => 1);
    $output[] = array("value"=>"2" , "label" => 2);
    $output[] = array("value"=>"3" , "label" => 3);
    $output[] = array("value"=>"4" , "label" => 4);
    $output[] = array("value"=>"5" , "label" => 5);
    $output[] = array("value"=>"6" , "label" => 6);

    return $output ;
  }
}