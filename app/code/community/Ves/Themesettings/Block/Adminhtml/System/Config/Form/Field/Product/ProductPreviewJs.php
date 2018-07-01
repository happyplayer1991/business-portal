<?php
class Ves_Themesettings_Block_Adminhtml_System_Config_Form_Field_Product_ProductPreviewJs extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	/**
     * Render element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$fieldIdSuffix = strstr($element->getHtmlId(), '_product_previewjs');
			//Replace the suffix with suffix appropriate for the background color picker in the current options group
		$bgcPickerId = str_replace($fieldIdSuffix, '_bg_color', $element->getHtmlId());

		$elements = array();
		$elements['product-item'] = array(
			'bg_color' => 'background-color',
			'hover_bg_color' => 'background-color',
			'padding_top' => 'padding-top',
			'padding_right' => 'padding-right',
			'padding_bottom' => 'padding-bottom',
			'padding_left' => 'padding-left',
			'border_width' => 'border-width',
			'border_color' => 'border-color',
			'border_style' => 'border-style',
			);
		$elements['product-name'] = array(
			'name_fonts' => 'font-family',
			'name_color' => 'color',
			'name_hover_color' => 'color',
			''
			);
		$elements['price-box'] = array(
			'price_color' => 'color',
			);
		$elements['old-price'] = array(
			'old_price_color' => 'color',
			);
		$elements['special-price'] = array(
			'special_price_color' => 'color',
			);
		$elements['btn-cart'] = array(
			'addtocart_color' => 'color',
			'addtocart_hover_color' => 'color',
			'addtocart_bg_color' => 'background-color',
			'addtocart_hover_bg_color' => 'background-color'
			);
		$elements['quickview'] = array(
			'quickview_color' => 'color',
			'quickview_hover_color' => 'color',
			'quickview_bg_color' => 'background-color',
			'quickview_hover_bg_color' => 'background-color'
			);
		$elements['link-compare'] = array(
			'compare_color' => 'color',
			'compare_hover_color' => 'color',
			'compare_bg_color' => 'background-color',
			'compare_hover_bg_color' => 'background-color'
			);
		$elements['link-wishlist'] = array(
			'whishlist_color' => 'color',
			'whishlist_hover_color' => 'color',
			'whishlist_bg_color' => 'background-color',
			'whishlist_hover_bg_color' => 'background-color'
			);
		$elements['desc'] = array(
			'short_description_color' => 'color'
			);
		$elements['countdown-timmer'] = array(
			'countdown_timer_color' => 'color'
			);
		$elements['new-icon'] = array(
			'new_label_color' => 'color',
			'new_label_bg_color' => 'background-color',
			);
		$elements['onsale'] = array(
			'sale_label_color' => 'color',
			'sale_label_bg_color' => 'background-color',
			);

		$script = '';
		$script .= '<script type="text/javascript">
		jQuery(function(){
			';
			foreach ($elements as $k => $v) {
				if(is_array($v)){
					foreach ($v as $key => $val) {
						$htmlId = str_replace($fieldIdSuffix, '_'.$key, $element->getHtmlId());
						$id = time();
						$script .= '
						var tex'.$id.'	= jQuery("#'. $htmlId .'");
						tex'.$id.'.change(function() {
						var val = jQuery(this).val();
						var elemenClass = ".'.$k.'";
						';
						if(preg_match('/hover/', $key)){
							$script .= 'var originalData = "";
								if(jQuery(elemenClass).css("'.$val.'") !== "undefined"){
									originalData = jQuery(elemenClass).css("'.$val.'");
								}
							';
							$script .=	'jQuery(elemenClass).hover(function(){
								jQuery(elemenClass).css({"'.$val.'": val});
							},function(){
								if(originalData!=""){
									jQuery(elemenClass).css({"'.$val.'": originalData});
								}
							});';
						}else{
							$script .= 'jQuery(elemenClass).css({"'.$val.'": val});';
						}
						$script .= '}).change();';
						}}}
						$script .= '			
						});
						</script>';
						$useContainerId = $element->getData('use_container_id');
						return sprintf('<tr class="system-fieldset-sub-head fieldset-hidden" id="row_%s"><td colspan="5">%s</td></tr>',
							$element->getHtmlId(), $script
		);
	}
}