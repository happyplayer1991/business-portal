<?php
class Ves_Themesettings_Model_System_Config_Source_Footer_FooterBottomLayout{
	public function toOptionArray()
	{
		$footer_profiles = array();
		if(Mage::helper("themesettings")->checkModuleInstalled("Ves_BlockBuilder")) {
			$collection = Mage::getModel("ves_blockbuilder/block")->getCollection();
			$collection = $collection->addFooterFilter();
			if( $collection->count() > 0 ) {
				foreach($footer_profiles as $item){
					$footer_profiles[] = array(
						'label' => $item->getTitle(),
						'value' => $item->getId(),
						);
				}
			}
			return $footer_profiles;
		}
	}
}