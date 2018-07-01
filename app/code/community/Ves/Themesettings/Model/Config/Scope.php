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
class Ves_Themesettings_Model_Config_Scope
{
	const SCOPE_DEFAULT		= 'default';
	const SCOPE_WEBSITES	= 'websites';
	const SCOPE_STORES		= 'stores';
	const SCOPE_DELIMITER	= '@';

	protected $_options;

	/**
	 * Retrieve scope values for form, compatible with form dropdown options
	 *
	 * @param bool
	 * @param bool
	 * @return array
	 */
	public function getScopeSelectOptions($empty = false, $all = false)
	{
		if (!$this->_options)
		{
			$options = array();
			if ($empty)
			{
				$options[] = array(
					'label' => Mage::helper('themesettings')->__('-- Please Select --'),
					'value' => '',
					);
			}
			if ($all)
			{
				$options[] = array(
					'label' => Mage::helper('adminhtml')->__('Default Config'),
					'value' => self::SCOPE_DEFAULT . self::SCOPE_DELIMITER . '0', 'style' => '',
					);
			}

			$nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
			$storeModel = Mage::getSingleton('adminhtml/system_store');
			/* @var $storeModel Mage_Adminhtml_Model_System_Store */

			foreach ($storeModel->getWebsiteCollection() as $website)
			{
				$websiteShow = false;
				foreach ($storeModel->getGroupCollection() as $group)
				{
					if ($group->getWebsiteId() != $website->getId())
					{
						continue;
					}
					$groupShow = false;
					foreach ($storeModel->getStoreCollection() as $store)
					{
						if ($store->getGroupId() != $group->getId())
						{
							continue;
						}
						if (!$websiteShow)
						{
							$options[] = array(
								'label' => $website->getName(),
								'value' => self::SCOPE_WEBSITES . self::SCOPE_DELIMITER . $website->getId(),
								);
							$websiteShow = true;
						}
						if (!$groupShow)
						{
							$groupShow = true;
							$values    = array();
						}
						$values[] = array(
							'label' => str_repeat($nonEscapableNbspChar, 4) . $store->getName(),
							'value' => self::SCOPE_STORES . self::SCOPE_DELIMITER . $store->getId(),
							);
					} //end: foreach store
					if ($groupShow)
					{
						$options[] = array(
							'label' => str_repeat($nonEscapableNbspChar, 4) . $group->getName(),
							'value' => $values,
							);
					}
				} //end: foreach group
			} //end: foreach website

			$this->_options = $options;
		}

		echo '<pre>';
		print_r($this->_options);
		echo '</pre>';
		die('test'); 
		
		return $this->_options;
	}

	/**
	 * Decode scope code: retrieve scope and scope id from the scope code and return values as an array
	 *
	 * @param string
	 * @return array
	 */
	public function decodeScope($str)
	{
		//Check if correct format of input (should contain proper delimiter)
		if (FALSE === strstr($str, self::SCOPE_DELIMITER))
		{
			throw new Exception('Incorrect format of scope/scopeId value.');

			//If single store mode supported:
			//Single id without delimiter is a store id
			/*if (!$str)
			{
				$output['scope']	= self::SCOPE_DEFAULT;
				$output['scopeId']	= '0';
			}
			else
			{
				$output['scope']	= self::SCOPE_STORES;
				$output['scopeId']	= $str;
			}*/
		}

		//Split input value to get scope and scope id
		$values = explode(self::SCOPE_DELIMITER, $str);

		$output = array();
		$output['scope']	= $values[0];
		$output['scopeId']	= $values[1];

		return $output;
	}

	/**
	 * Encode scope code: create scope code based on store id
	 *
	 * @param string|int
	 * @return string
	 */
	public function encodeScopeUsingStoreId($storeId)
	{
		$storeId = intval($storeId);
		if ($storeId === 0)
		{
			$scope = self::SCOPE_DEFAULT . self::SCOPE_DELIMITER . '0';
		}
		else
		{
			$scope = self::SCOPE_STORES . self::SCOPE_DELIMITER . $storeId;
		}
		return $scope;
	}
}
