<?php

class Ves_Themesettings_Model_System_Config_Source_Js_Jquery_Easing
{
    public function toOptionArray()
    {
        return array(
			//Ease in-out
			array('value' => 'easeInOutSine',	'label' => Mage::helper('themesettings')->__('easeInOutSine')),
			array('value' => 'easeInOutQuad',	'label' => Mage::helper('themesettings')->__('easeInOutQuad')),
			array('value' => 'easeInOutCubic',	'label' => Mage::helper('themesettings')->__('easeInOutCubic')),
			array('value' => 'easeInOutQuart',	'label' => Mage::helper('themesettings')->__('easeInOutQuart')),
			array('value' => 'easeInOutQuint',	'label' => Mage::helper('themesettings')->__('easeInOutQuint')),
			array('value' => 'easeInOutExpo',	'label' => Mage::helper('themesettings')->__('easeInOutExpo')),
			array('value' => 'easeInOutCirc',	'label' => Mage::helper('themesettings')->__('easeInOutCirc')),
			array('value' => 'easeInOutElastic','label' => Mage::helper('themesettings')->__('easeInOutElastic')),
			array('value' => 'easeInOutBack',	'label' => Mage::helper('themesettings')->__('easeInOutBack')),
			array('value' => 'easeInOutBounce',	'label' => Mage::helper('themesettings')->__('easeInOutBounce')),
			//Ease out
			array('value' => 'easeOutSine',		'label' => Mage::helper('themesettings')->__('easeOutSine')),
			array('value' => 'easeOutQuad',		'label' => Mage::helper('themesettings')->__('easeOutQuad')),
			array('value' => 'easeOutCubic',	'label' => Mage::helper('themesettings')->__('easeOutCubic')),
			array('value' => 'easeOutQuart',	'label' => Mage::helper('themesettings')->__('easeOutQuart')),
			array('value' => 'easeOutQuint',	'label' => Mage::helper('themesettings')->__('easeOutQuint')),
			array('value' => 'easeOutExpo',		'label' => Mage::helper('themesettings')->__('easeOutExpo')),
			array('value' => 'easeOutCirc',		'label' => Mage::helper('themesettings')->__('easeOutCirc')),
			array('value' => 'easeOutElastic',	'label' => Mage::helper('themesettings')->__('easeOutElastic')),
			array('value' => 'easeOutBack',		'label' => Mage::helper('themesettings')->__('easeOutBack')),
			array('value' => 'easeOutBounce',	'label' => Mage::helper('themesettings')->__('easeOutBounce')),
			//Ease in
			array('value' => 'easeInSine',		'label' => Mage::helper('themesettings')->__('easeInSine')),
			array('value' => 'easeInQuad',		'label' => Mage::helper('themesettings')->__('easeInQuad')),
			array('value' => 'easeInCubic',		'label' => Mage::helper('themesettings')->__('easeInCubic')),
			array('value' => 'easeInQuart',		'label' => Mage::helper('themesettings')->__('easeInQuart')),
			array('value' => 'easeInQuint',		'label' => Mage::helper('themesettings')->__('easeInQuint')),
			array('value' => 'easeInExpo',		'label' => Mage::helper('themesettings')->__('easeInExpo')),
			array('value' => 'easeInCirc',		'label' => Mage::helper('themesettings')->__('easeInCirc')),
			array('value' => 'easeInElastic',	'label' => Mage::helper('themesettings')->__('easeInElastic')),
			array('value' => 'easeInBack',		'label' => Mage::helper('themesettings')->__('easeInBack')),
			array('value' => 'easeInBounce',	'label' => Mage::helper('themesettings')->__('easeInBounce')),
			//No easing
			array('value' => '',				'label' => Mage::helper('themesettings')->__('No easing'))
        );
    }
}
